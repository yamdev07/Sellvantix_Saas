<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\Product;
use App\Models\Client;
use App\Models\Supplier;
use App\Models\Tenant;
use App\Models\User;
use App\Models\SaleItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    /**
     * Toutes les stats du dashboard tenant en UN SEUL passage.
     */
    public function tenantStats(int $tenantId): array
    {
        // Cache 5 min — invalidé automatiquement à minuit (today change)
        return Cache::remember("dashboard.tenant.{$tenantId}." . Carbon::today()->toDateString(), 300, function () use ($tenantId) {
            return $this->buildTenantStats($tenantId);
        });
    }

    public function invalidateTenantCache(int $tenantId): void
    {
        Cache::forget("dashboard.tenant.{$tenantId}." . Carbon::today()->toDateString());
    }

    private function buildTenantStats(int $tenantId): array
    {
        $today   = Carbon::today();
        $weekAgo = Carbon::today()->subDays(7);

        // --- Ventes et CA en une query agrégée ---
        $salesAgg = Sale::where('tenant_id', $tenantId)
            ->selectRaw("
                COUNT(*) as total_count,
                SUM(total_price) as total_revenue,
                SUM(CASE WHEN DATE(created_at) = ? THEN 1 ELSE 0 END) as today_count,
                SUM(CASE WHEN DATE(created_at) = ? THEN total_price ELSE 0 END) as today_revenue,
                COUNT(DISTINCT CASE WHEN MONTH(created_at) = ? AND YEAR(created_at) = ? THEN client_id END) as active_clients
            ", [$today->toDateString(), $today->toDateString(), $today->month, $today->year])
            ->first();

        // --- Produits en une query agrégée ---
        $productsAgg = Product::where('tenant_id', $tenantId)
            ->selectRaw("
                COUNT(*) as total,
                SUM(sale_price * stock) as stock_value,
                SUM(CASE WHEN stock = 0 THEN 1 ELSE 0 END) as out_of_stock,
                SUM(CASE WHEN stock > 0 AND stock <= stock_alert THEN 1 ELSE 0 END) as low_stock
            ")
            ->first();

        // --- Alertes stock avec détail (10 max) ---
        $criticalThreshold = 2;
        $lowStockProducts = Product::where('tenant_id', $tenantId)
            ->whereColumn('stock', '<=', 'stock_alert')
            ->where('stock', '>', $criticalThreshold)
            ->orderBy('stock')
            ->limit(10)
            ->get(['id', 'name', 'stock', 'stock_alert']);

        $criticalStockProducts = Product::where('tenant_id', $tenantId)
            ->where('stock', '<=', $criticalThreshold)
            ->orderBy('stock')
            ->limit(10)
            ->get(['id', 'name', 'stock', 'stock_alert']);

        // --- Ventes récentes ---
        $recentSales = Sale::with(['client:id,name', 'items'])
            ->where('tenant_id', $tenantId)
            ->latest()
            ->limit(10)
            ->get();

        // --- Données graphique 7 jours en UNE query ---
        $chartData = Sale::where('tenant_id', $tenantId)
            ->where('created_at', '>=', Carbon::today()->subDays(6)->startOfDay())
            ->selectRaw('DATE(created_at) as date, SUM(total_price) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date');

        $dates  = [];
        $totals = [];
        for ($i = 6; $i >= 0; $i--) {
            $d = Carbon::today()->subDays($i);
            $dates[]  = $d->format('d/m');
            $totals[] = $chartData[$d->toDateString()] ?? 0;
        }

        // --- Nouveaux clients ---
        $newClients = Client::where('tenant_id', $tenantId)
            ->whereBetween('created_at', [$weekAgo, $today])
            ->count();

        // --- Fournisseurs ---
        $totalSuppliers = Supplier::where('tenant_id', $tenantId)->count();

        // --- Employés ---
        $employeesCount = User::where('tenant_id', $tenantId)
            ->where('role', '!=', 'super_admin')
            ->count();

        return [
            'sales_today'             => $salesAgg->today_count ?? 0,
            'revenue_today'            => $salesAgg->today_revenue ?? 0,
            'revenue_all'              => $salesAgg->total_revenue ?? 0,
            'active_clients'           => $salesAgg->active_clients ?? 0,
            'new_clients'              => $newClients,
            'total_products'           => $productsAgg->total ?? 0,
            'total_stock_value'        => $productsAgg->stock_value ?? 0,
            'low_stock_count'          => ($productsAgg->low_stock ?? 0) + ($productsAgg->out_of_stock ?? 0),
            'low_stock_products'       => $lowStockProducts,
            'critical_stock_products'  => $criticalStockProducts,
            'recent_sales'             => $recentSales,
            'chart_dates'              => $dates,
            'chart_totals'             => $totals,
            'total_suppliers'          => $totalSuppliers,
            'employees_count'          => $employeesCount,
            'low_sales_alert'          => ($salesAgg->today_count ?? 0) < 5,
        ];
    }

    /**
     * Stats globales pour le super admin.
     */
    public function globalStats(): array
    {
        // Tenants
        $tenantsAgg = Tenant::selectRaw("
            COUNT(*) as total,
            SUM(is_active) as active,
            SUM(CASE WHEN is_active = 0 THEN 1 ELSE 0 END) as inactive,
            SUM(CASE WHEN is_active = 1 AND subscription_ends_at <= ? AND subscription_ends_at > NOW() THEN 1 ELSE 0 END) as expiring_soon
        ", [now()->addDays(30)])->first();

        // Users par rôle
        $usersByRole = User::selectRaw("role, COUNT(*) as count")
            ->whereNotNull('tenant_id')
            ->groupBy('role')
            ->pluck('count', 'role');

        // Nouveaux tenants
        $newTenants = Tenant::selectRaw("
            SUM(CASE WHEN DATE(created_at) = ? THEN 1 ELSE 0 END) as today,
            SUM(CASE WHEN created_at >= ? THEN 1 ELSE 0 END) as this_week,
            SUM(CASE WHEN MONTH(created_at) = ? AND YEAR(created_at) = ? THEN 1 ELSE 0 END) as this_month
        ", [now()->toDateString(), now()->startOfWeek(), now()->month, now()->year])->first();

        // CA global
        $revenueAgg = Sale::selectRaw("
            SUM(total_price) as all_time,
            SUM(CASE WHEN DATE(created_at) = ? THEN total_price ELSE 0 END) as today,
            SUM(CASE WHEN MONTH(created_at) = ? AND YEAR(created_at) = ? THEN total_price ELSE 0 END) as this_month
        ", [now()->toDateString(), now()->month, now()->year])->first();

        // CA global 30 jours (une query)
        $revenueChart = Sale::selectRaw('DATE(created_at) as date, SUM(total_price) as total')
            ->where('created_at', '>=', Carbon::today()->subDays(29)->startOfDay())
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date');

        $chartDates = [];
        $chartRevenues = [];
        for ($i = 29; $i >= 0; $i--) {
            $d = Carbon::today()->subDays($i);
            $chartDates[]   = $d->format('d/m');
            $chartRevenues[] = $revenueChart[$d->toDateString()] ?? 0;
        }

        // Inscriptions 12 mois (une query)
        $regChart = Tenant::selectRaw('YEAR(created_at) as yr, MONTH(created_at) as mo, COUNT(*) as count')
            ->where('created_at', '>=', now()->subMonths(11)->startOfMonth())
            ->groupBy('yr', 'mo')
            ->orderBy('yr')->orderBy('mo')
            ->get()
            ->keyBy(fn($r) => "{$r->yr}-{$r->mo}");

        $months = [];
        $registrations = [];
        for ($i = 11; $i >= 0; $i--) {
            $d = now()->subMonths($i);
            $months[]        = $d->format('M Y');
            $registrations[] = $regChart["{$d->year}-{$d->month}"]->count ?? 0;
        }

        // Top 5 tenants
        $topTenants = Tenant::withCount('users')
            ->withSum('sales', 'total_price')
            ->orderBy('sales_sum_total_price', 'desc')
            ->limit(5)
            ->get();

        // Produits et stock globaux
        $productsAgg = Product::selectRaw("
            COUNT(*) as total,
            SUM(purchase_price * stock) as stock_value,
            SUM(CASE WHEN stock = 0 THEN 1 ELSE 0 END) as out_of_stock
        ")->first();

        return [
            'total_tenants'      => $tenantsAgg->total ?? 0,
            'active_tenants'     => $tenantsAgg->active ?? 0,
            'inactive_tenants'   => $tenantsAgg->inactive ?? 0,
            'expiring_soon'      => $tenantsAgg->expiring_soon ?? 0,
            'total_users'        => User::whereNotNull('tenant_id')->count(),
            'users_by_role'      => $usersByRole,
            'new_tenants_today'  => $newTenants->today ?? 0,
            'new_tenants_week'   => $newTenants->this_week ?? 0,
            'new_tenants_month'  => $newTenants->this_month ?? 0,
            'revenue_all_time'   => $revenueAgg->all_time ?? 0,
            'revenue_today'      => $revenueAgg->today ?? 0,
            'revenue_this_month' => $revenueAgg->this_month ?? 0,
            'chart_dates'        => $chartDates,
            'chart_revenues'     => $chartRevenues,
            'months'             => $months,
            'registrations'      => $registrations,
            'top_tenants'        => $topTenants,
            'total_products'     => $productsAgg->total ?? 0,
            'total_stock_value'  => $productsAgg->stock_value ?? 0,
            'out_of_stock'       => $productsAgg->out_of_stock ?? 0,
        ];
    }

    // =========================================================================
    // AJAX ENDPOINTS
    // =========================================================================

    /**
     * Données pour le graphique des ventes sur $period jours.
     *
     * @return array{dates: array<string>, totals: array<float|int>}
     */
    public function chartData(int $tenantId, int $period, bool $isSuperAdmin): array
    {
        $query = Sale::selectRaw('DATE(created_at) as date, SUM(total_price) as total')
            ->where('created_at', '>=', Carbon::today()->subDays($period - 1)->startOfDay())
            ->groupBy('date')
            ->orderBy('date');

        if (!$isSuperAdmin) {
            $query->where('tenant_id', $tenantId);
        }

        $chartData = $query->pluck('total', 'date');

        $dates  = [];
        $totals = [];

        for ($i = $period - 1; $i >= 0; $i--) {
            $d        = Carbon::today()->subDays($i);
            $dates[]  = $d->format('d/m');
            $totals[] = $chartData[$d->toDateString()] ?? 0;
        }

        return [
            'dates'  => $dates,
            'totals' => $totals,
        ];
    }

    /**
     * Stats rapides pour les appels AJAX selon le rôle de l'utilisateur.
     */
    public function ajaxStats(User $user): array
    {
        $today     = Carbon::today();
        $tenantId  = $user->tenant_id;

        if ($user->isSuperAdminGlobal()) {
            // ---- Super Admin Global ----
            $salesAgg = Sale::selectRaw("
                SUM(CASE WHEN DATE(created_at) = ? THEN 1 ELSE 0 END)               as sales_today,
                SUM(CASE WHEN DATE(created_at) = ? THEN total_price ELSE 0 END)      as revenue_today,
                SUM(total_price)                                                       as total_revenue_all,
                AVG(total_price)                                                       as average_sale
            ", [$today->toDateString(), $today->toDateString()])->first();

            $lowStockCount = Product::whereColumn('stock', '<=', 'stock_alert')->count();

            $tenantsAgg = Tenant::selectRaw("
                COUNT(*) as total_tenants,
                SUM(is_active) as active_tenants
            ")->first();

            $totalProducts = Product::count();

            return [
                'salesToday'      => (int) ($salesAgg->sales_today ?? 0),
                'totalRevenue'    => (float) ($salesAgg->revenue_today ?? 0),
                'lowStockCount'   => (int) $lowStockCount,
                'activeTenants'   => (int) ($tenantsAgg->active_tenants ?? 0),
                'totalTenants'    => (int) ($tenantsAgg->total_tenants ?? 0),
                'averageSale'     => (float) ($salesAgg->average_sale ?? 0),
                'totalProducts'   => (int) $totalProducts,
                'totalRevenueAll' => (float) ($salesAgg->total_revenue_all ?? 0),
            ];
        }

        // ---- Tenant user ----
        $salesAgg = Sale::where('tenant_id', $tenantId)
            ->selectRaw("
                SUM(CASE WHEN DATE(created_at) = ? THEN 1 ELSE 0 END)               as sales_today,
                SUM(CASE WHEN DATE(created_at) = ? THEN total_price ELSE 0 END)      as revenue_today,
                SUM(total_price)                                                       as total_revenue_all,
                AVG(total_price)                                                       as average_sale,
                COUNT(DISTINCT CASE WHEN MONTH(created_at) = ? AND YEAR(created_at) = ? THEN client_id END) as active_clients
            ", [
                $today->toDateString(),
                $today->toDateString(),
                $today->month,
                $today->year,
            ])->first();

        $lowStockCount = Product::where('tenant_id', $tenantId)
            ->whereColumn('stock', '<=', 'stock_alert')
            ->count();

        $totalProducts = Product::where('tenant_id', $tenantId)->count();

        return [
            'salesToday'      => (int) ($salesAgg->sales_today ?? 0),
            'totalRevenue'    => (float) ($salesAgg->revenue_today ?? 0),
            'lowStockCount'   => (int) $lowStockCount,
            'activeClients'   => (int) ($salesAgg->active_clients ?? 0),
            'averageSale'     => (float) ($salesAgg->average_sale ?? 0),
            'totalProducts'   => (int) $totalProducts,
            'totalRevenueAll' => (float) ($salesAgg->total_revenue_all ?? 0),
        ];
    }

    /**
     * Les 10 dernières ventes d'un tenant avec détails.
     *
     * @return \Illuminate\Support\Collection
     */
    public function recentSales(int $tenantId): \Illuminate\Support\Collection
    {
        return Sale::with(['client:id,name', 'items.product:id,name'])
            ->where('tenant_id', $tenantId)
            ->latest()
            ->limit(10)
            ->get()
            ->map(function (Sale $sale) {
                $productNames = $sale->items
                    ->map(fn($item) => $item->product?->name ?? 'Produit inconnu')
                    ->filter()
                    ->implode(', ');

                return [
                    'id'              => $sale->id,
                    'product_name'    => $productNames,
                    'client_name'     => $sale->client?->name ?? 'Client anonyme',
                    'total_price'     => (float) $sale->total_price,
                    'formatted_total' => number_format($sale->total_price, 0, ',', ' ') . ' FCFA',
                    'created_at'      => $sale->created_at,
                    'formatted_date'  => $sale->created_at->format('d/m/Y H:i'),
                ];
            });
    }

    /**
     * Produits en stock faible ou nul pour un tenant.
     *
     * @return \Illuminate\Support\Collection
     */
    public function lowStock(int $tenantId): \Illuminate\Support\Collection
    {
        return Product::where('tenant_id', $tenantId)
            ->whereColumn('stock', '<=', 'stock_alert')
            ->orderBy('stock')
            ->get()
            ->map(function (Product $product) {
                return [
                    'id'             => $product->id,
                    'name'           => $product->name,
                    'stock'          => $product->stock,
                    'status'         => $product->stock_status,
                    'sale_price'     => (float) $product->sale_price,
                    'formatted_price' => number_format($product->sale_price, 0, ',', ' ') . ' FCFA',
                    'profit_margin'  => round($product->profit_margin, 2),
                ];
            });
    }

    /**
     * Statistiques avancées pour un tenant.
     */
    public function advancedStats(int $tenantId): array
    {
        $now           = Carbon::now();
        $startOfMonth  = $now->copy()->startOfMonth();
        $startOfLast   = $now->copy()->subMonth()->startOfMonth();
        $endOfLast     = $now->copy()->subMonth()->endOfMonth();

        $salesThisMonth = Sale::where('tenant_id', $tenantId)
            ->whereMonth('created_at', $now->month)
            ->whereYear('created_at', $now->year)
            ->count();

        $salesLastMonth = Sale::where('tenant_id', $tenantId)
            ->whereBetween('created_at', [$startOfLast, $endOfLast])
            ->count();

        $monthlyGrowth = $salesLastMonth > 0
            ? round((($salesThisMonth - $salesLastMonth) / $salesLastMonth) * 100, 2)
            : ($salesThisMonth > 0 ? 100.0 : 0.0);

        // Top 5 produits (par quantité vendue)
        $topProducts = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->where('sales.tenant_id', $tenantId)
            ->select(
                'products.id',
                'products.name',
                DB::raw('SUM(sale_items.quantity) as total_quantity'),
                DB::raw('SUM(sale_items.total_price) as total_revenue')
            )
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_quantity')
            ->limit(5)
            ->get();

        // Top 5 clients (par montant dépensé)
        $topClients = DB::table('sales')
            ->join('clients', 'sales.client_id', '=', 'clients.id')
            ->where('sales.tenant_id', $tenantId)
            ->whereNotNull('sales.client_id')
            ->select(
                'clients.id',
                'clients.name',
                DB::raw('COUNT(sales.id) as total_orders'),
                DB::raw('SUM(sales.total_price) as total_spent')
            )
            ->groupBy('clients.id', 'clients.name')
            ->orderByDesc('total_spent')
            ->limit(5)
            ->get();

        // Distribution du stock
        $stockDist = Product::where('tenant_id', $tenantId)
            ->selectRaw("
                SUM(CASE WHEN stock = 0 THEN 1 ELSE 0 END)                                   as out_of_stock,
                SUM(CASE WHEN stock > 0 AND stock <= stock_alert THEN 1 ELSE 0 END)          as low_stock,
                SUM(CASE WHEN stock > stock_alert AND stock <= stock_alert * 3 THEN 1 ELSE 0 END) as medium_stock,
                SUM(CASE WHEN stock > stock_alert * 3 THEN 1 ELSE 0 END)                    as high_stock
            ")
            ->first();

        $totalProducts  = Product::where('tenant_id', $tenantId)->count();
        $totalClients   = Client::where('tenant_id', $tenantId)->count();
        $totalSuppliers = Supplier::where('tenant_id', $tenantId)->count();

        return [
            'salesThisMonth'   => $salesThisMonth,
            'salesLastMonth'   => $salesLastMonth,
            'monthlyGrowth'    => $monthlyGrowth,
            'topProducts'      => $topProducts,
            'topClients'       => $topClients,
            'stockDistribution' => [
                'out'    => (int) ($stockDist->out_of_stock ?? 0),
                'low'    => (int) ($stockDist->low_stock ?? 0),
                'medium' => (int) ($stockDist->medium_stock ?? 0),
                'high'   => (int) ($stockDist->high_stock ?? 0),
            ],
            'total_products'  => $totalProducts,
            'total_clients'   => $totalClients,
            'total_suppliers' => $totalSuppliers,
        ];
    }

    /**
     * Ventes par catégorie pour un tenant.
     *
     * @return \Illuminate\Support\Collection
     */
    public function salesByCategory(int $tenantId): \Illuminate\Support\Collection
    {
        return DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->where('sales.tenant_id', $tenantId)
            ->select(
                'categories.id',
                'categories.name as category_name',
                DB::raw('SUM(sale_items.quantity) as total_quantity'),
                DB::raw('SUM(sale_items.total_price) as total_revenue')
            )
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('total_revenue')
            ->get();
    }

    /**
     * Notifications dashboard pour un utilisateur.
     *
     * @return array<int, array{type: string, icon: string, message: string, link: string}>
     */
    public function notifications(User $user): array
    {
        $notifications = [];
        $tenantId      = $user->tenant_id;

        // ---- Super Admin Global : tenants qui expirent et nouveaux tenants ----
        if ($user->isSuperAdminGlobal()) {
            $expiringTenants = Tenant::where('is_active', true)
                ->where(function ($q) {
                    $q->where(function ($q2) {
                        $q2->where('payment_status', 'paid')
                           ->whereBetween('subscription_end_date', [now(), now()->addDays(7)]);
                    })->orWhere(function ($q2) {
                        $q2->where('payment_status', 'trial')
                           ->whereBetween('trial_ends_at', [now(), now()->addDays(7)]);
                    });
                })
                ->count();

            if ($expiringTenants > 0) {
                $notifications[] = [
                    'type'    => 'warning',
                    'icon'    => 'clock',
                    'message' => "{$expiringTenants} abonnement(s) expirent dans moins de 7 jours.",
                    'link'    => route('super-admin.tenants.index'),
                ];
            }

            $newTenantsToday = Tenant::whereDate('created_at', Carbon::today())->count();
            if ($newTenantsToday > 0) {
                $notifications[] = [
                    'type'    => 'info',
                    'icon'    => 'store',
                    'message' => "{$newTenantsToday} nouveau(x) tenant(s) inscrit(s) aujourd'hui.",
                    'link'    => route('super-admin.tenants.index'),
                ];
            }
        }

        // ---- Tous les utilisateurs : stock critique et ventes du jour ----
        $criticalStockQuery = Product::where('stock', '<=', 2);
        if (!$user->isSuperAdminGlobal()) {
            $criticalStockQuery->where('tenant_id', $tenantId);
        }
        $criticalStockCount = $criticalStockQuery->count();

        if ($criticalStockCount > 0) {
            $notifications[] = [
                'type'    => 'danger',
                'icon'    => 'alert-triangle',
                'message' => "{$criticalStockCount} produit(s) en stock critique (≤ 2).",
                'link'    => $user->isSuperAdminGlobal()
                    ? '#'
                    : route('products.index', ['filter' => 'critical']),
            ];
        }

        $salesTodayQuery = Sale::whereDate('created_at', Carbon::today());
        if (!$user->isSuperAdminGlobal()) {
            $salesTodayQuery->where('tenant_id', $tenantId);
        }
        $salesToday = $salesTodayQuery->count();

        if ($salesToday > 0) {
            $notifications[] = [
                'type'    => 'success',
                'icon'    => 'shopping-cart',
                'message' => "{$salesToday} vente(s) enregistrée(s) aujourd'hui.",
                'link'    => $user->isSuperAdminGlobal()
                    ? route('super-admin.dashboard')
                    : route('sales.index'),
            ];
        }

        return $notifications;
    }

    /**
     * Stats synthétiques pour le dashboard (total ventes, CA, quantité vendue, stock faible).
     */
    public function dashboardStats(int $tenantId, bool $isSuperAdmin): array
    {
        $salesQuery      = Sale::query();
        $saleItemsQuery  = SaleItem::query();
        $productsQuery   = Product::query();

        if (!$isSuperAdmin) {
            $salesQuery->where('tenant_id', $tenantId);
            $saleItemsQuery->where('tenant_id', $tenantId);
            $productsQuery->where('tenant_id', $tenantId);
        }

        $totalSales          = $salesQuery->count();
        $totalRevenue        = (float) $salesQuery->sum('total_price');
        $totalQuantitySold   = (int) $saleItemsQuery->sum('quantity');
        $lowStockCount       = $productsQuery->whereColumn('stock', '<=', 'stock_alert')->count();

        return [
            'total_sales'          => $totalSales,
            'total_revenue'        => $totalRevenue,
            'total_quantity_sold'  => $totalQuantitySold,
            'low_stock_count'      => $lowStockCount,
        ];
    }
}
