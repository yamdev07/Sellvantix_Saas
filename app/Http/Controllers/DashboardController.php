<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use App\Models\Client;
use App\Models\Supplier;
use App\Models\Tenant;
use App\Models\User;
use App\Services\DashboardService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct(private DashboardService $dashboardService)
    {
        $this->middleware('auth');
    }

    // =========================================================================
    // MAIN VIEW
    // =========================================================================

    public function index()
    {
        $user = Auth::user();

        if ($user->isSuperAdminGlobal()) {
            return $this->globalDashboard();
        }

        return $this->tenantDashboard($user);
    }

    private function tenantDashboard(User $user): \Illuminate\View\View
    {
        $s = $this->dashboardService->tenantStats($user->tenant_id);

        return view('dashboard', [
            'salesToday'            => $s['sales_today'],
            'totalRevenue'          => $s['revenue_today'],
            'totalRevenueAll'       => $s['revenue_all'],
            'lowStockProducts'      => $s['low_stock_products'],
            'criticalStockProducts' => $s['critical_stock_products'],
            'lowStockCount'         => $s['low_stock_count'],
            'activeClients'         => $s['active_clients'],
            'newClients'            => $s['new_clients'],
            'recentSales'           => $s['recent_sales'],
            'dates'                 => $s['chart_dates'],
            'totals'                => $s['chart_totals'],
            'lowSalesAlert'         => $s['low_sales_alert'],
            'totalProducts'         => $s['total_products'],
            'totalStockValue'       => $s['total_stock_value'],
            'totalSuppliers'        => $s['total_suppliers'],
            'suppliersWithProducts' => Supplier::where('tenant_id', $user->tenant_id)->has('products')->count(),
            'userRole'              => $user->role,
            'isAdmin'               => $user->isSuperAdminOrAdmin(),
            'employeesCount'        => $s['employees_count'],
        ]);
    }

    private function globalDashboard(): \Illuminate\View\View
    {
        $s = $this->dashboardService->globalStats();

        return view('dashboard-global', [
            'totalTenants'          => $s['total_tenants'],
            'activeTenants'         => $s['active_tenants'],
            'inactiveTenants'       => $s['inactive_tenants'],
            'expiringSoon'          => $s['expiring_soon'],
            'totalUsers'            => $s['total_users'],
            'totalSuperAdmins'      => $s['users_by_role']['super_admin'] ?? 0,
            'totalAdmins'           => $s['users_by_role']['admin'] ?? 0,
            'totalManagers'         => $s['users_by_role']['manager'] ?? 0,
            'totalCashiers'         => $s['users_by_role']['cashier'] ?? 0,
            'totalStorekeepers'     => $s['users_by_role']['storekeeper'] ?? 0,
            'newTenantsToday'       => $s['new_tenants_today'],
            'newTenantsThisWeek'    => $s['new_tenants_week'],
            'newTenantsThisMonth'   => $s['new_tenants_month'],
            'totalRevenueAllTime'   => $s['revenue_all_time'],
            'totalRevenueToday'     => $s['revenue_today'],
            'totalRevenueThisMonth' => $s['revenue_this_month'],
            'months'                => $s['months'],
            'registrations'         => $s['registrations'],
            'chartDates'            => $s['chart_dates'],
            'chartRevenues'         => $s['chart_revenues'],
            'topTenants'            => $s['top_tenants'],
            'totalProducts'         => $s['total_products'],
            'totalStockValue'       => $s['total_stock_value'],
            'outOfStock'            => $s['out_of_stock'],
        ]);
    }

    // =========================================================================
    // AJAX ENDPOINTS
    // =========================================================================

    public function chartData(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate(['period' => 'integer|min:1|max:90']);

        $period      = (int) $request->get('period', 7);
        $user        = Auth::user();
        $isSuperAdmin = $user->isSuperAdminGlobal();

        return response()->json(
            $this->dashboardService->chartData($user->tenant_id, $period, $isSuperAdmin)
        );
    }

    public function stats(): \Illuminate\Http\JsonResponse
    {
        return response()->json(
            $this->dashboardService->ajaxStats(Auth::user())
        );
    }

    public function recentSales(): \Illuminate\Http\JsonResponse
    {
        return response()->json(
            $this->dashboardService->recentSales(Auth::user()->tenant_id)
        );
    }

    public function lowStock(): \Illuminate\Http\JsonResponse
    {
        return response()->json(
            $this->dashboardService->lowStock(Auth::user()->tenant_id)
        );
    }

    public function advancedStats(): \Illuminate\Http\JsonResponse
    {
        if (!Auth::user()->canViewReports()) {
            abort(403);
        }

        return response()->json(
            $this->dashboardService->advancedStats(Auth::user()->tenant_id)
        );
    }

    public function salesByCategory(): \Illuminate\Http\JsonResponse
    {
        if (!Auth::user()->canViewReports()) {
            abort(403);
        }

        return response()->json(
            $this->dashboardService->salesByCategory(Auth::user()->tenant_id)
        );
    }

    public function employeeStats(): \Illuminate\Http\JsonResponse
    {
        if (!Auth::user()->canManageUsers()) {
            abort(403);
        }

        $employees = Auth::user()
            ->employees()
            ->withCount('sales')
            ->withSum('sales', 'total_price')
            ->get();

        return response()->json([
            'total_employees'            => $employees->count(),
            'total_sales_by_employees'   => $employees->sum('sales_count'),
            'total_revenue_by_employees' => $employees->sum('sales_sum_total_price'),
            'best_employee'              => $employees->sortByDesc('sales_sum_total_price')->first(),
            'employees_by_role'          => $employees->groupBy('role')->map->count(),
        ]);
    }

    public function notifications(): \Illuminate\Http\JsonResponse
    {
        return response()->json(
            $this->dashboardService->notifications(Auth::user())
        );
    }

    public function dashboardStats(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = Auth::user();

        return response()->json(
            $this->dashboardService->dashboardStats($user->tenant_id, $user->isSuperAdminGlobal())
        );
    }
}
