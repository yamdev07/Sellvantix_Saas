<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Client;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Supplier;
use App\Models\Tenant;
use App\Models\User;
use App\Observers\TenantObserver;
use App\Policies\CategoryPolicy;
use App\Policies\ClientPolicy;
use App\Policies\ProductPolicy;
use App\Policies\SalePolicy;
use App\Policies\SupplierPolicy;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Injection des services dans le container IoC
        $this->app->singleton(\App\Services\ProductService::class);
        $this->app->singleton(\App\Services\SaleService::class);
        $this->app->singleton(\App\Services\DashboardService::class);
        $this->app->singleton(\App\Services\PlanService::class);
        $this->app->singleton(\App\Services\SubscriptionService::class);
        $this->app->singleton(\App\Services\ClientService::class);
        $this->app->singleton(\App\Services\CategoryService::class);
        $this->app->singleton(\App\Services\SupplierService::class);
        $this->app->singleton(\App\Services\UserService::class);
    }

    public function boot(): void
    {
        // Observers
        Tenant::observe(TenantObserver::class);

        // Policies
        Gate::policy(Category::class, CategoryPolicy::class);
        Gate::policy(Client::class, ClientPolicy::class);
        Gate::policy(Product::class, ProductPolicy::class);
        Gate::policy(Sale::class, SalePolicy::class);
        Gate::policy(Supplier::class, SupplierPolicy::class);
        Gate::policy(User::class, UserPolicy::class);

        // Gate globale pour le super admin
        Gate::before(function (User $user, string $ability) {
            if ($user->isSuperAdminGlobal()) {
                return true;
            }
        });
    }
}
