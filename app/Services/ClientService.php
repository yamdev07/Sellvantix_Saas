<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ClientService
{
    // =========================================================
    // LISTING & FILTERS
    // =========================================================

    /**
     * Return a filtered query builder for the client index.
     *
     * Supports:
     *  - search  : partial match on name / email / phone
     *  - status  : "active" (purchased in last 30 days) | "inactive"
     *  - period  : "today" | "week" | "month" — filters by registration date
     *
     * Always eager-loads sales counts and sums so the caller can paginate or
     * add further constraints before executing.
     */
    public function list(Request $request): Builder
    {
        $query = Client::withCount('sales')
            ->withSum('sales', 'total_price');

        // ── Search ──────────────────────────────────────────────────────────
        if ($search = $request->input('search')) {
            $query->where(function (Builder $q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('phone', 'LIKE', "%{$search}%");
            });
        }

        // ── Status filter ────────────────────────────────────────────────────
        match ($request->input('status')) {
            'active'   => $query->whereHas('sales', function (Builder $q) {
                $q->where('created_at', '>=', now()->subDays(30));
            }),
            'inactive' => $query->whereDoesntHave('sales', function (Builder $q) {
                $q->where('created_at', '>=', now()->subDays(30));
            }),
            default    => null,
        };

        // ── Period filter (registration date) ────────────────────────────────
        match ($request->input('period')) {
            'today' => $query->whereDate('clients.created_at', today()),
            'week'  => $query->whereBetween('clients.created_at', [
                now()->startOfWeek(),
                now()->endOfWeek(),
            ]),
            'month' => $query->whereMonth('clients.created_at', now()->month)
                             ->whereYear('clients.created_at', now()->year),
            default => null,
        };

        return $query->latest('clients.created_at');
    }

    // =========================================================
    // CREATE
    // =========================================================

    /**
     * Create a new client record for the given tenant.
     */
    public function create(array $data, int $tenantId): Client
    {
        return DB::transaction(function () use ($data, $tenantId) {
            $data['tenant_id'] = $tenantId;

            return Client::create($data);
        });
    }

    /**
     * Quick-create a client via AJAX (same logic, separate method for
     * semantic clarity and independent validation in the controller).
     */
    public function quickCreate(array $data, int $tenantId): Client
    {
        return DB::transaction(function () use ($data, $tenantId) {
            $data['tenant_id'] = $tenantId;

            return Client::create($data);
        });
    }

    // =========================================================
    // UPDATE
    // =========================================================

    /**
     * Update a client and return the refreshed model.
     */
    public function update(Client $client, array $data): Client
    {
        DB::transaction(function () use ($client, $data) {
            $client->update($data);
        });

        return $client->fresh();
    }

    // =========================================================
    // DELETE
    // =========================================================

    /**
     * Soft-delete a client and return a translated confirmation message.
     */
    public function delete(Client $client): string
    {
        DB::transaction(function () use ($client) {
            $client->delete();
        });

        return "Le client « {$client->name} » a été supprimé avec succès.";
    }

    // =========================================================
    // SEARCH (AJAX autocomplete)
    // =========================================================

    /**
     * Search clients by name, email or phone — max 10 results.
     * Returns only id / name / email / phone for lightweight payloads.
     */
    public function search(string $term): Collection
    {
        return Client::where(function (Builder $q) use ($term) {
                $q->where('name', 'LIKE', "%{$term}%")
                  ->orWhere('email', 'LIKE', "%{$term}%")
                  ->orWhere('phone', 'LIKE', "%{$term}%");
            })
            ->limit(10)
            ->get(['id', 'name', 'email', 'phone']);
    }

    // =========================================================
    // STATS (single client)
    // =========================================================

    /**
     * Return aggregated statistics for a single client.
     *
     * @return array{
     *   total_sales: int,
     *   total_spent: float,
     *   average_cart: float,
     *   first_purchase: string|null,
     *   last_purchase: string|null,
     *   products_purchased: int,
     * }
     */
    public function stats(Client $client): array
    {
        $sales = $client->sales()->get(['id', 'total_price', 'created_at']);

        $totalSales    = $sales->count();
        $totalSpent    = (float) $sales->sum('total_price');
        $averageCart   = $totalSales > 0 ? round($totalSpent / $totalSales, 2) : 0.0;

        $firstPurchase = $sales->min('created_at');
        $lastPurchase  = $sales->max('created_at');

        // Distinct products purchased by this client
        $productsPurchased = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->where('sales.client_id', $client->id)
            ->distinct('sale_items.product_id')
            ->count('sale_items.product_id');

        return [
            'total_sales'        => $totalSales,
            'total_spent'        => $totalSpent,
            'average_cart'       => $averageCart,
            'first_purchase'     => $firstPurchase
                                        ? Carbon::parse($firstPurchase)->format('d/m/Y')
                                        : null,
            'last_purchase'      => $lastPurchase
                                        ? Carbon::parse($lastPurchase)->format('d/m/Y')
                                        : null,
            'products_purchased' => $productsPurchased,
        ];
    }

    // =========================================================
    // FAVORITE PRODUCTS
    // =========================================================

    /**
     * Return the top 5 products purchased by a client, ordered by quantity.
     *
     * Each row contains: product_id, product_name, total_quantity, total_spent.
     */
    public function favoriteProducts(Client $client): \Illuminate\Support\Collection
    {
        return DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->where('sales.client_id', $client->id)
            ->select(
                'products.id   as product_id',
                'products.name as product_name',
                DB::raw('SUM(sale_items.quantity)   as total_quantity'),
                DB::raw('SUM(sale_items.total_price) as total_spent')
            )
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_quantity')
            ->limit(5)
            ->get();
    }

    // =========================================================
    // SALES BY MONTH (chart data)
    // =========================================================

    /**
     * Return the last 12 months of sales for a client, grouped by year/month.
     *
     * Each row contains: year, month, total_sales (COUNT), total_revenue (SUM).
     */
    public function salesByMonth(Client $client): \Illuminate\Support\Collection
    {
        return DB::table('sales')
            ->where('client_id', $client->id)
            ->where('created_at', '>=', now()->subMonths(12)->startOfMonth())
            ->select(
                DB::raw('YEAR(created_at)  as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*)          as total_sales'),
                DB::raw('SUM(total_price)  as total_revenue')
            )
            ->groupBy(DB::raw('YEAR(created_at)'), DB::raw('MONTH(created_at)'))
            ->orderBy(DB::raw('YEAR(created_at)'))
            ->orderBy(DB::raw('MONTH(created_at)'))
            ->get();
    }

    // =========================================================
    // TOP PRODUCTS (detailed)
    // =========================================================

    /**
     * Return the top 10 products purchased by a client with full spend detail.
     *
     * Each row: product_id, product_name, total_quantity, total_spent.
     */
    public function topProducts(Client $client): \Illuminate\Support\Collection
    {
        return DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->where('sales.client_id', $client->id)
            ->select(
                'products.id   as product_id',
                'products.name as product_name',
                DB::raw('SUM(sale_items.quantity)   as total_quantity'),
                DB::raw('SUM(sale_items.total_price) as total_spent')
            )
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_quantity')
            ->limit(10)
            ->get();
    }

    // =========================================================
    // REPORT DATA
    // =========================================================

    /**
     * Build all data needed by the client report page.
     *
     * @return array{
     *   clients: \Illuminate\Pagination\LengthAwarePaginator,
     *   topClients: \Illuminate\Database\Eloquent\Collection,
     *   reportData: array{
     *     total_clients: int,
     *     active_clients: int,
     *     total_spent: float,
     *     formatted_total_spent: string,
     *     average_spent: float,
     *   },
     * }
     */
    public function reportData(Request $request, int $tenantId): array
    {
        // ── Paginated list with optional filters ─────────────────────────────
        $query = Client::where('tenant_id', $tenantId)
            ->withCount('sales')
            ->withSum('sales', 'total_price');

        if ($search = $request->input('search')) {
            $query->where(function (Builder $q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('phone', 'LIKE', "%{$search}%");
            });
        }

        $clients = $query->latest()->paginate(15)->withQueryString();

        // ── Aggregate stats over ALL tenant clients ──────────────────────────
        $allStats = DB::table('clients')
            ->leftJoin('sales', 'sales.client_id', '=', 'clients.id')
            ->where('clients.tenant_id', $tenantId)
            ->whereNull('clients.deleted_at')
            ->select(
                DB::raw('COUNT(DISTINCT clients.id)          as total_clients'),
                DB::raw('COALESCE(SUM(sales.total_price), 0) as total_spent')
            )
            ->first();

        $totalClients = (int) ($allStats->total_clients ?? 0);
        $totalSpent   = (float) ($allStats->total_spent  ?? 0);

        // Active = at least one sale in the last 30 days
        $activeClients = Client::where('tenant_id', $tenantId)
            ->whereHas('sales', function (Builder $q) {
                $q->where('created_at', '>=', now()->subDays(30));
            })
            ->count();

        $averageSpent = $totalClients > 0 ? round($totalSpent / $totalClients, 2) : 0.0;

        return [
            'clients'    => $clients,
            'topClients' => $this->topClients($tenantId),
            'reportData' => [
                'total_clients'         => $totalClients,
                'active_clients'        => $activeClients,
                'total_spent'           => $totalSpent,
                'formatted_total_spent' => number_format($totalSpent, 0, ',', ' ') . ' FCFA',
                'average_spent'         => $averageSpent,
            ],
        ];
    }

    // =========================================================
    // TOP CLIENTS
    // =========================================================

    /**
     * Return the top 10 clients for a tenant ordered by number of sales.
     */
    public function topClients(int $tenantId): Collection
    {
        return Client::where('tenant_id', $tenantId)
            ->withCount('sales')
            ->withSum('sales', 'total_price')
            ->orderByDesc('sales_count')
            ->limit(10)
            ->get();
    }
}
