<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\SuperAdminController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ApiModalController;
use App\Models\Sale;
use App\Models\Product;
use App\Models\Client;
use App\Mail\NewTenantWelcomeMail;
use Illuminate\Support\Facades\Mail;
use App\Models\User;

// ======================
// ROUTES PUBLIQUES (VITRINE) - PAS DE MIDDLEWARE
// ======================
Route::get('/', [LandingController::class, 'index'])->name('landing');
Route::get('/demo', [LandingController::class, 'demo'])->name('demo');
Route::get('/tarifs', [LandingController::class, 'pricing'])->name('pricing');
Route::get('/faq', [LandingController::class, 'faq'])->name('faq');
Route::get('/fonctionnalites', [LandingController::class, 'features'])->name('features');
Route::get('/guide', [LandingController::class, 'guide'])->name('guide');


// Routes d'inscription
Route::get('/inscription', [LandingController::class, 'registerForm'])->name('register.form');
Route::post('/inscription', [LandingController::class, 'register'])->name('register.tenant');
Route::get('/inscription/succes', [LandingController::class, 'registerSuccess'])->name('register.success');

// Route de test
Route::view('/welcome', 'welcome');

// ROUTES DE PAIEMENT FEDAPAY
Route::middleware(['auth', 'throttle:10,1'])->prefix('payment')->name('payment.')->group(function () {
    Route::get('/form', [PaymentController::class, 'showPaymentForm'])->name('form');
    Route::post('/initialize', [PaymentController::class, 'initializePayment'])->name('initialize');
    Route::match(['get', 'post'], '/callback', [PaymentController::class, 'paymentCallback'])->name('callback');
});

// ======================
// Routes AJAX pour le dashboard - PROTÉGÉES PAR AUTH + TRIAL
// ======================
Route::middleware(['auth', 'check.trial'])->prefix('ajax/dashboard')->name('ajax.dashboard.')->group(function () {
    Route::get('/chart-data', [DashboardController::class, 'chartData'])->name('chart');
    Route::get('/stats', [DashboardController::class, 'stats'])->name('stats');
    Route::get('/recent-sales', [DashboardController::class, 'recentSales'])->name('recent-sales');
    Route::get('/low-stock', [DashboardController::class, 'lowStock'])->name('low-stock');
});

// ======================
// Routes SUPER ADMIN GLOBAL (toi - le créateur) - PAS DE TRIAL (toujours accès)
// ======================
Route::middleware(['auth', 'super_admin_global'])->prefix('super-admin')->name('super-admin.')->group(function () {
    Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/tenants', [SuperAdminController::class, 'tenants'])->name('tenants');
    Route::get('/tenants/create', [SuperAdminController::class, 'createTenant'])->name('tenants.create');
    Route::post('/tenants', [SuperAdminController::class, 'storeTenant'])->name('tenants.store');
    Route::get('/tenants/{tenant}', [SuperAdminController::class, 'showTenant'])->name('tenants.show');
    Route::patch('/tenants/{tenant}/toggle', [SuperAdminController::class, 'toggleTenant'])->name('tenants.toggle');
    Route::delete('/tenants/{tenant}', [SuperAdminController::class, 'destroyTenant'])->name('tenants.destroy');
    
    // Routes paiements
    Route::get('/tenants/{tenant}/payments', [SuperAdminController::class, 'managePayments'])->name('tenants.payments');
    Route::post('/tenants/{tenant}/payments', [SuperAdminController::class, 'markPaymentReceived'])->name('tenants.payments.store');
    Route::post('/tenants/{tenant}/extend', [SuperAdminController::class, 'extendSubscription'])->name('tenants.extend');
    
    // ROUTES POUR GÉRER LES UTILISATEURS DES TENANTS
    Route::prefix('tenants/{tenant}')->name('tenants.')->group(function () {
        Route::get('/users', [SuperAdminController::class, 'users'])->name('users');
        Route::get('/users/create', [SuperAdminController::class, 'createUser'])->name('users.create');
        Route::post('/users', [SuperAdminController::class, 'storeUser'])->name('users.store');
        Route::get('/users/{user}/edit', [SuperAdminController::class, 'editUser'])->name('users.edit');
        Route::put('/users/{user}', [SuperAdminController::class, 'updateUser'])->name('users.update');
        Route::delete('/users/{user}', [SuperAdminController::class, 'destroyUser'])->name('users.destroy');
    });

     // Routes pour les actions sur les tenants
    Route::post('/tenants/{tenant}/mark-paid', [SuperAdminController::class, 'markAsPaid'])->name('tenants.mark-paid');
    Route::post('/tenants/{tenant}/extend-subscription', [SuperAdminController::class, 'extendSubscriptionAjax'])->name('tenants.extend-subscription');
    Route::post('/tenants/{tenant}/send-reminder', [SuperAdminController::class, 'sendReminder'])->name('tenants.send-reminder');
});

// ======================
// Routes ADMIN (super_admin et admin de la quincaillerie) - AVEC TRIAL
// ======================

// 1. CATÉGORIES - CRUD admin
Route::middleware(['auth', 'check.trial', 'admin'])->prefix('categories')->name('categories.')->group(function () {
    Route::get('/', [CategoryController::class, 'index'])->name('index');
    Route::get('/create', [CategoryController::class, 'create'])->name('create');
    Route::post('/', [CategoryController::class, 'store'])->name('store');
    Route::get('/{category}', [CategoryController::class, 'show'])->name('show');
    Route::get('/{category}/edit', [CategoryController::class, 'edit'])->name('edit');
    Route::put('/{category}', [CategoryController::class, 'update'])->name('update');
    Route::delete('/{category}', [CategoryController::class, 'destroy'])->name('destroy');
    Route::get('/{category}/products', [CategoryController::class, 'products'])->name('products');
    Route::get('/{category}/stats', [CategoryController::class, 'detailedStats'])->name('stats');
});

// 2. PRODUITS - Gestion complète
Route::middleware(['auth', 'check.trial', 'stock.manager'])->prefix('admin/products')->name('admin.products.')->group(function () {
    Route::get('/', [ProductController::class, 'index'])->name('index');
    Route::get('/create', [ProductController::class, 'create'])->name('create');
    Route::post('/', [ProductController::class, 'store'])->name('store');
    Route::get('/{product}/edit', [ProductController::class, 'edit'])->name('edit');
    Route::put('/{product}', [ProductController::class, 'update'])->name('update');
    Route::delete('/{product}', [ProductController::class, 'destroy'])->name('destroy');
    
    // Gestion du stock
    Route::post('/{product}/restock', [ProductController::class, 'restock'])->name('restock');
    Route::post('/{product}/adjust-stock', [ProductController::class, 'adjustStock'])->name('adjust-stock');
    
    // Export
    Route::get('/{product}/history/export', [ProductController::class, 'exportHistory'])->name('history.export');
    Route::get('/global-history/export', [ProductController::class, 'exportGlobalHistory'])->name('global-history.export');
    
    // Gestion des cumuls et fusion
    Route::post('/merge', [ProductController::class, 'mergeProducts'])->name('merge');
    Route::post('/{product}/uncumulate', [ProductController::class, 'uncumulateProduct'])->name('uncumulate');
});

// 3. FOURNISSEURS - CRUD admin
Route::middleware(['auth', 'check.trial', 'admin'])->prefix('admin/suppliers')->name('admin.suppliers.')->group(function () {
    Route::get('/', [SupplierController::class, 'index'])->name('index');
    Route::get('/create', [SupplierController::class, 'create'])->name('create');
    Route::post('/', [SupplierController::class, 'store'])->name('store');
    Route::get('/{supplier}', [SupplierController::class, 'show'])->name('show');
    Route::get('/{supplier}/edit', [SupplierController::class, 'edit'])->name('edit');
    Route::put('/{supplier}', [SupplierController::class, 'update'])->name('update');
    Route::delete('/{supplier}', [SupplierController::class, 'destroy'])->name('destroy');
    Route::get('/{supplier}/products', [SupplierController::class, 'products'])->name('products');
    Route::get('/{supplier}/orders', [SupplierController::class, 'orders'])->name('orders');
});

// 4. Gestion des utilisateurs
Route::middleware(['auth', 'check.trial', 'admin'])->prefix('users')->name('users.')->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('index');
    Route::get('/create', [UserController::class, 'create'])->name('create');
    Route::post('/', [UserController::class, 'store'])->name('store');
    Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
    Route::put('/{user}', [UserController::class, 'update'])->name('update');
    Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
    Route::get('/statistics', [UserController::class, 'statistics'])->name('statistics');
});

// ======================
// Routes ANALYSE IA
// ======================
Route::middleware(['auth', 'check.trial', 'admin'])->prefix('ai')->name('ai.')->group(function () {
    Route::get('/',         [App\Http\Controllers\AIAnalysisController::class, 'index'])->name('index');
    Route::post('/products', [App\Http\Controllers\AIAnalysisController::class, 'analyzeProducts'])->name('products');
    Route::post('/reports',  [App\Http\Controllers\AIAnalysisController::class, 'analyzeReports'])->name('reports');
});

// ======================
// Routes PARAMÈTRES ENTREPRISE (super_admin et admin uniquement)
// ======================
Route::middleware(['auth', 'check.trial', 'admin'])->prefix('company')->name('company.')->group(function () {
    Route::get('/settings', [App\Http\Controllers\CompanySettingsController::class, 'edit'])->name('settings');
    Route::put('/settings', [App\Http\Controllers\CompanySettingsController::class, 'update'])->name('settings.update');
    Route::get('/settings/delete-logo', [App\Http\Controllers\CompanySettingsController::class, 'deleteLogo'])->name('settings.delete-logo');
});

// ======================
// Routes protégées par authentification (pour tous les utilisateurs) - AVEC TRIAL
// ======================
Route::middleware(['auth', 'check.trial'])->group(function () {
    // ----------------------
    // TABLEAU DE BORD PRINCIPAL
    // ----------------------
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ----------------------
    // VENTES
    // ----------------------
    Route::prefix('sales')->name('sales.')->group(function () {
        Route::get('/', [SaleController::class, 'index'])->name('index');
        Route::get('/create', [SaleController::class, 'create'])->name('create');
        Route::post('/', [SaleController::class, 'store'])->name('store');
        Route::get('/{sale}', [SaleController::class, 'show'])->name('show');
        Route::get('/{sale}/edit', [SaleController::class, 'edit'])->name('edit');
        Route::put('/{sale}', [SaleController::class, 'update'])->name('update');
        Route::delete('/{sale}', [SaleController::class, 'destroy'])->name('destroy');
        Route::get('/{sale}/invoice', [SaleController::class, 'invoice'])->name('invoice');
        Route::post('/{sale}/status', [SaleController::class, 'updateStatus'])->name('status');
    });

    // ----------------------
    // CLIENTS
    // ----------------------
    Route::prefix('clients')->name('clients.')->group(function () {
        Route::get('/', [ClientController::class, 'index'])->name('index');
        Route::get('/search', [ClientController::class, 'search'])->name('search');
        Route::get('/create', [ClientController::class, 'create'])->name('create');
        Route::post('/', [ClientController::class, 'store'])->name('store');
        // Création rapide depuis le formulaire de vente (accessible aux caissiers)
        Route::post('/quick', [ClientController::class, 'quickStore'])->name('quick-store');
        Route::get('/{client}', [ClientController::class, 'show'])->name('show');
        Route::get('/{client}/edit', [ClientController::class, 'edit'])->name('edit');
        Route::put('/{client}', [ClientController::class, 'update'])->name('update');
        Route::delete('/{client}', [ClientController::class, 'destroy'])->name('destroy');
        Route::get('/{client}/sales', [ClientController::class, 'sales'])->name('sales');
        Route::get('/{client}/statistics', [ClientController::class, 'statistics'])->name('statistics');
        Route::get('/{client}/export', [ClientController::class, 'export'])->name('export');
    });

    // ----------------------
    // FOURNISSEURS - Lecture seule
    // ----------------------
    Route::prefix('suppliers')->name('suppliers.')->group(function () {
        Route::get('/', [SupplierController::class, 'index'])->name('index');
        Route::get('/{supplier}', [SupplierController::class, 'show'])->name('show');
        Route::get('/{supplier}/products', [SupplierController::class, 'products'])->name('products');
    });

    // ----------------------
    // PRODUITS - Lecture seule
    // ----------------------
    Route::prefix('products')->name('products.')->group(function () {
        Route::get('/', [ProductController::class, 'index'])->name('index');
        Route::get('/category/{category}', [ProductController::class, 'byCategory'])->name('byCategory');
        Route::get('/search', [ProductController::class, 'search'])->name('search');
        Route::get('/{product}', [ProductController::class, 'show'])->whereNumber('product')->name('show');
        
        // Historique et mouvements (lecture pour tous)
        Route::get('/{product}/history', [ProductController::class, 'history'])->name('history');
        Route::get('/global-history', [ProductController::class, 'globalHistory'])->name('global-history');
        
        // Actions rapides (vente rapide)
        Route::post('/{product}/quick-sale', [ProductController::class, 'quickSale'])->name('quick-sale');
    });

    // ----------------------
    // CATEGORIES - Lecture seule
    // ----------------------
    Route::prefix('categories')->name('categories.')->group(function () {
        Route::get('/', [CategoryController::class, 'index'])->name('index');
        Route::get('/{category}', [CategoryController::class, 'show'])->name('show');
    });

    // -----------------------
    // RAPPORTS ET STATISTIQUES
    // ----------------------
    Route::middleware(['stock.manager'])->prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        
        Route::get('/sales', [SaleController::class, 'salesReport'])->name('sales');
        Route::get('/clients', [ClientController::class, 'clientsReport'])->name('clients');
        Route::get('/products', [ProductController::class, 'productsReport'])->name('products');
        Route::get('/inventory', [ProductController::class, 'inventoryReport'])->name('inventory');
        Route::get('/grouped-stocks', [ProductController::class, 'groupedStocksReport'])->name('grouped-stocks');
        Route::get('/grouped-stocks/export/{format?}', [ReportController::class, 'exportGroupedStocks'])
            ->name('grouped-stocks.export');
        Route::get('/sales/export/{format?}', [ReportController::class, 'exportSales'])->name('sales.export');
        Route::get('/clients/export/{format?}', [ReportController::class, 'exportClients'])->name('clients.export');
        Route::get('/products/export/{format?}', [ReportController::class, 'exportProducts'])->name('products.export');

        Route::get('/invoice/{id}', [App\Http\Controllers\InvoiceController::class, 'downloadInvoice'])->name('invoice');
    });

    // ----------------------
    // API TEMPS RÉEL
    // ----------------------
    Route::prefix('api')->name('api.')->group(function () {
        Route::get('/dashboard-stats', [SaleController::class, 'dashboardStats'])->name('dashboard.stats');
        Route::get('/recent-sales', [SaleController::class, 'recentSales'])->name('recent.sales');
        Route::get('/top-products', [ProductController::class, 'topProducts'])->name('top.products');
        Route::get('/grouped-stocks-stats', [ProductController::class, 'getQuickStats'])->name('grouped-stocks.stats');
        
        // Routes pour le modal de fusion
        Route::get('/modal/categories', [ApiModalController::class, 'categories'])->name('modal.categories');
        Route::get('/modal/suppliers', [ApiModalController::class, 'suppliers'])->name('modal.suppliers');
    });
    
    // ----------------------
    // PROFIL UTILISATEUR
    // ----------------------
    Route::get('/profile', function () {
        return view('profile');
    })->name('profile');
});

// ======================
// PAGES D'EXPIRATION (ACCESSIBLES SANS CHECK.TRIAL)
// ======================
Route::get('/trial-expired', function () {
    return view('errors.trial-expired');
})->name('trial.expired');

Route::get('/subscription-expired', function () {
    return view('errors.subscription-expired');
})->name('subscription.expired');


// ======================
// Routes d'authentification
// ======================
require __DIR__ . '/auth.php';


// Routes abonnement
Route::middleware(['auth'])->prefix('subscription')->name('subscription.')->group(function () {
    Route::get('/', [App\Http\Controllers\SubscriptionController::class, 'show'])->name('show');
    Route::post('/cancel', [App\Http\Controllers\SubscriptionController::class, 'cancel'])->name('cancel');
});

// Routes factures
Route::middleware(['auth'])->prefix('invoices')->name('invoices.')->group(function () {
    Route::get('/', [App\Http\Controllers\InvoiceController::class, 'index'])->name('index');
    Route::get('/last', [App\Http\Controllers\InvoiceController::class, 'showLastInvoice'])->name('last');
    Route::get('/download/{id}', [App\Http\Controllers\InvoiceController::class, 'downloadInvoice'])->name('download');
});