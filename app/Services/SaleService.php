<?php

namespace App\Services;

use App\DTOs\CreateSaleData;
use App\Models\Client;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleService
{
    /**
     * Crée une vente complète (items + mouvements de stock) dans une transaction.
     *
     * @throws \Exception si stock insuffisant ou produit invalide
     */
    public function create(CreateSaleData|array $data, int $tenantId, int $userId): Sale
    {
        if (is_array($data)) {
            $data = CreateSaleData::fromArray($data);
        }

        return DB::transaction(function () use ($data, $tenantId, $userId) {
            $sale = Sale::create([
                'client_id'   => $data->clientId,
                'user_id'     => $userId,
                'total_price' => 0,
                'tenant_id'   => $tenantId,
            ]);

            $clientName = $this->resolveClientName($data->clientId);
            $grandTotal = 0;

            foreach ($data->items as $item) {
                /** @var Product $product */
                $product = Product::lockForUpdate()->find($item->productId);

                if (!$product) {
                    throw new \Exception("Produit introuvable : #{$item->productId}");
                }
                if ($product->tenant_id !== $tenantId) {
                    throw new \Exception("Le produit \"{$product->name}\" n'appartient pas à votre boutique.");
                }
                if ($product->stock < $item->quantity) {
                    throw new \Exception("Stock insuffisant pour \"{$product->name}\". Disponible : {$product->stock}, demandé : {$item->quantity}.");
                }

                $stockAfter = $product->stock - $item->quantity;

                StockMovement::create([
                    'product_id'         => $product->id,
                    'type'               => 'sortie',
                    'quantity'           => $item->quantity,
                    'purchase_price'     => $product->purchase_price,
                    'sale_price'         => $item->unitPrice,
                    'stock_after'        => $stockAfter,
                    'motif'              => "Vente #{$sale->id} à {$clientName}",
                    'reference_document' => "VTE-{$sale->id}",
                    'user_id'            => $userId,
                    'tenant_id'          => $tenantId,
                ]);

                SaleItem::create([
                    'sale_id'     => $sale->id,
                    'product_id'  => $product->id,
                    'quantity'    => $item->quantity,
                    'unit_price'  => $item->unitPrice,
                    'total_price' => $item->unitPrice * $item->quantity,
                    'tenant_id'   => $tenantId,
                ]);

                $product->decrement('stock', $item->quantity);

                $grandTotal += $item->unitPrice * $item->quantity;
            }

            $sale->update(['total_price' => max(0, $grandTotal - $data->discount)]);

            return $sale->fresh(['items.product', 'client']);
        });
    }

    /**
     * Annule une vente et restitue le stock.
     *
     * @throws \Exception si la vente est déjà annulée
     */
    public function cancel(Sale $sale, int $userId): void
    {
        if ($sale->status === 'cancelled') {
            throw new \Exception('Cette vente est déjà annulée.');
        }

        DB::transaction(function () use ($sale, $userId) {
            foreach ($sale->items as $item) {
                $product = Product::lockForUpdate()->find($item->product_id);
                if (!$product) continue;

                $stockAfter = $product->stock + $item->quantity;

                StockMovement::create([
                    'product_id'         => $product->id,
                    'type'               => 'entree',
                    'quantity'           => $item->quantity,
                    'purchase_price'     => $product->purchase_price,
                    'sale_price'         => $item->unit_price,
                    'stock_after'        => $stockAfter,
                    'motif'              => "Annulation vente #{$sale->id}",
                    'reference_document' => "CANCEL-{$sale->id}",
                    'user_id'            => $userId,
                    'tenant_id'          => $sale->tenant_id,
                ]);

                $product->increment('stock', $item->quantity);
            }

            $sale->update(['status' => 'cancelled']);
        });
    }

    /**
     * Génère les données du rapport de ventes pour un tenant donné,
     * avec filtres optionnels (dates, client, utilisateur).
     *
     * @return array{
     *   sales: \Illuminate\Contracts\Pagination\LengthAwarePaginator,
     *   reportData: array,
     *   clients: \Illuminate\Support\Collection,
     *   users: \Illuminate\Support\Collection
     * }
     */
    public function reportData(Request $request, int $tenantId): array
    {
        // --- Build filtered query ---
        $query = Sale::where('tenant_id', $tenantId)
            ->with(['client', 'user', 'items']);

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->input('start_date'));
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->input('end_date'));
        }
        if ($request->filled('client_id')) {
            $query->where('client_id', $request->input('client_id'));
        }
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }

        // Paginated results (clone before aggregation)
        $sales = (clone $query)->latest()->paginate(15)->withQueryString();

        // All matching sales for aggregate calculations
        $allSales = (clone $query)->get();

        // --- Aggregates ---
        $totalSales   = $allSales->count();
        $totalRevenue = $allSales->sum('total_price');
        $totalItems   = $allSales->sum(fn ($s) => $s->items->sum('quantity'));
        $averageSale  = $totalSales > 0 ? $totalRevenue / $totalSales : 0;

        // --- Sales by day ---
        $salesByDay = $allSales
            ->groupBy(fn ($s) => $s->created_at->format('Y-m-d'))
            ->map(fn ($group, $date) => [
                'date'  => $date,
                'count' => $group->count(),
                'total' => $group->sum('total_price'),
            ])
            ->values();

        // --- Top products (DB-level join, tenant-scoped) ---
        $topProductsQuery = DB::table('sale_items')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->where('sales.tenant_id', $tenantId)
            ->select(
                'products.id',
                'products.name',
                DB::raw('SUM(sale_items.quantity) as total_quantity'),
                DB::raw('SUM(sale_items.total_price) as total_revenue')
            )
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_quantity')
            ->limit(10);

        if ($request->filled('start_date')) {
            $topProductsQuery->whereDate('sales.created_at', '>=', $request->input('start_date'));
        }
        if ($request->filled('end_date')) {
            $topProductsQuery->whereDate('sales.created_at', '<=', $request->input('end_date'));
        }
        if ($request->filled('client_id')) {
            $topProductsQuery->where('sales.client_id', $request->input('client_id'));
        }
        if ($request->filled('user_id')) {
            $topProductsQuery->where('sales.user_id', $request->input('user_id'));
        }

        $topProducts = $topProductsQuery->get();

        // --- Filter dropdown data ---
        $clients = Client::where('tenant_id', $tenantId)
            ->orderBy('name')
            ->get(['id', 'name']);

        $users = User::where('tenant_id', $tenantId)
            ->orderBy('name')
            ->get(['id', 'name']);

        return [
            'sales'      => $sales,
            'reportData' => [
                'total_sales'             => $totalSales,
                'total_revenue'           => $totalRevenue,
                'formatted_total_revenue' => number_format($totalRevenue, 0, ',', ' ') . ' FCFA',
                'total_items'             => $totalItems,
                'average_sale'            => $averageSale,
                'formatted_average_sale'  => number_format($averageSale, 0, ',', ' ') . ' FCFA',
                'sales_by_day'            => $salesByDay,
                'top_products'            => $topProducts,
            ],
            'clients' => $clients,
            'users'   => $users,
        ];
    }

    /**
     * Retourne les 10 produits les plus vendus pour un tenant,
     * classés par quantité totale vendue décroissante.
     *
     * @return \Illuminate\Support\Collection
     */
    public function topProducts(int $tenantId): \Illuminate\Support\Collection
    {
        return DB::table('sale_items')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->where('sales.tenant_id', $tenantId)
            ->select(
                'products.name',
                DB::raw('SUM(sale_items.quantity) as total_quantity'),
                DB::raw('SUM(sale_items.total_price) as total_revenue')
            )
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_quantity')
            ->limit(10)
            ->get();
    }

    /**
     * Retourne les statistiques globales de vente pour un tenant.
     *
     * @return array{
     *   total_sales: int,
     *   total_revenue: float,
     *   total_quantity_sold: int,
     *   average_sale: float,
     *   unique_clients: int,
     *   active_cashiers: int
     * }
     */
    public function getStats(int $tenantId): array
    {
        $salesStats = Sale::where('tenant_id', $tenantId)
            ->selectRaw('COUNT(*) as total_sales, COALESCE(SUM(total_price), 0) as total_revenue, COUNT(DISTINCT client_id) as unique_clients, COUNT(DISTINCT user_id) as active_cashiers')
            ->first();

        $totalQuantitySold = SaleItem::whereHas('sale', function ($q) use ($tenantId) {
            $q->where('tenant_id', $tenantId);
        })->sum('quantity');

        $totalSales  = (int) ($salesStats->total_sales ?? 0);
        $totalRevenue = (float) ($salesStats->total_revenue ?? 0);
        $averageSale = $totalSales > 0 ? $totalRevenue / $totalSales : 0;

        return [
            'total_sales'          => $totalSales,
            'total_revenue'        => $totalRevenue,
            'total_quantity_sold'  => (int) $totalQuantitySold,
            'average_sale'         => $averageSale,
            'unique_clients'       => (int) ($salesStats->unique_clients ?? 0),
            'active_cashiers'      => (int) ($salesStats->active_cashiers ?? 0),
        ];
    }

    private function resolveClientName(?int $clientId): string
    {
        if (!$clientId) return 'Client comptoir';

        $client = \App\Models\Client::find($clientId);
        return $client?->name ?? 'Client';
    }
}
