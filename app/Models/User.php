<?php

namespace App\Models;

use App\Enums\SubscriptionStatus;
use App\Enums\UserRole;
use App\Traits\HasSubscription;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasSubscription;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'tenant_id',
        'owner_id',
        'can_manage_users',
        'is_active',
        'last_login_at',
        'last_login_ip',
        // Nouveaux champs pour l'abonnement
        'trial_ends_at',
        'subscription_ends_at',
        'subscription_status',
        'fedapay_transaction_id',
        'last_payment_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'can_manage_users' => 'boolean',
        'is_active' => 'boolean',
        // Nouveaux casts
        'trial_ends_at' => 'datetime',
        'subscription_ends_at' => 'datetime',
        'last_payment_at' => 'datetime',
    ];

    /**
     * =====================================================
     * RELATIONS
     * =====================================================
     */
    
    /**
     * La quincaillerie (tenant) auquel appartient l'utilisateur
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Le propriétaire (super_admin) qui a créé cet utilisateur
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Les employés créés par cet utilisateur (si c'est un super_admin)
     */
    public function employees()
    {
        return $this->hasMany(User::class, 'owner_id');
    }

    /**
     * Ventes effectuées par cet utilisateur
     */
    public function sales()
    {
        return $this->hasMany(Sale::class, 'user_id');
    }

    /**
     * Mouvements de stock effectués par cet utilisateur
     */
    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class, 'user_id');
    }

    /**
     * Produits créés par cet utilisateur
     */
    public function products()
    {
        return $this->hasMany(Product::class, 'user_id');
    }

    /**
     * Clients créés par cet utilisateur
     */
    public function clients()
    {
        return $this->hasMany(Client::class, 'user_id');
    }

    /**
     * Fournisseurs créés par cet utilisateur
     */
    public function suppliers()
    {
        return $this->hasMany(Supplier::class, 'user_id');
    }

    /**
     * =====================================================
     * VÉRIFICATIONS DE RÔLE
     * =====================================================
     */
    public function isSuperAdminGlobal(): bool
    {
        return $this->role === 'super_admin_global';
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isManager(): bool
    {
        return $this->role === 'manager';
    }

    public function isCashier(): bool
    {
        return $this->role === 'cashier';
    }

    public function isStorekeeper(): bool
    {
        return $this->role === 'storekeeper';
    }

    /**
     * Vérifie si l'utilisateur est actif
     */
    public function isActive(): bool
    {
        return $this->is_active ?? true;
    }

    /**
     * =====================================================
     * VÉRIFICATIONS DE RÔLES ÉTENDUS
     * =====================================================
     */
    public function isSuperAdminOrAdmin(): bool
    {
        return $this->isSuperAdmin() || $this->isAdmin();
    }

    public function isManagerOrAbove(): bool
    {
        return $this->isSuperAdminGlobal() || 
               $this->isSuperAdmin() || 
               $this->isAdmin() || 
               $this->isManager();
    }

    /**
     * =====================================================
     * PERMISSIONS SPÉCIFIQUES
     * =====================================================
     */
    public function canManageUsers(): bool
    {
        return $this->isSuperAdminGlobal() || 
               $this->isSuperAdmin() || 
               ($this->isAdmin() && $this->can_manage_users);
    }

    public function canManageStock(): bool
    {
        return $this->isSuperAdminGlobal() || 
               $this->isSuperAdmin() || 
               $this->isAdmin() || 
               $this->isManager() || 
               $this->isStorekeeper();
    }

    public function canManageSales(): bool
    {
        return $this->isSuperAdminGlobal() || 
               $this->isSuperAdmin() || 
               $this->isAdmin() || 
               $this->isManager() || 
               $this->isCashier();
    }

    public function canViewReports(): bool
    {
        return $this->isSuperAdminGlobal() || 
               $this->isSuperAdmin() || 
               $this->isAdmin() || 
               $this->isManager();
    }

    /**
     * =====================================================
     * PERMISSIONS SPÉCIFIQUES AU SUPER ADMIN GLOBAL
     * =====================================================
     */
    public function canViewAllTenants(): bool
    {
        return $this->isSuperAdminGlobal();
    }

    public function canManageSubscriptions(): bool
    {
        return $this->isSuperAdminGlobal();
    }

    /**
     * Vérifie si l'utilisateur a accès à une ressource spécifique.
     * Règle : même tenant = accès. Le super_admin_global voit tout.
     */
    public function hasAccessTo($resource): bool
    {
        if ($this->isSuperAdminGlobal()) {
            return true;
        }

        if (!$resource) {
            return false;
        }

        if ($resource->tenant_id && $resource->tenant_id === $this->tenant_id) {
            return true;
        }

        // Fallback pour les ressources sans tenant_id (legacy)
        if (!$resource->tenant_id && $resource->owner_id) {
            return $this->id === $resource->owner_id
                || $this->owner_id === $resource->owner_id
                || $this->isSuperAdmin();
        }

        return false;
    }

    /**
     * Récupère le propriétaire racine (super_admin)
     */
    public function getRootOwnerAttribute()
    {
        if ($this->isSuperAdminGlobal()) {
            return null;
        }
        
        if ($this->isSuperAdmin()) {
            return $this;
        }
        
        return $this->owner;
    }

    /**
     * =====================================================
     * SCOPES - Version compatible PostgreSQL
     * =====================================================
     */
    
    /**
     * Récupère tous les utilisateurs de la même quincaillerie
     */
    public function scopeSameCompany($query)
    {
        if (auth()->check()) {
            $user = auth()->user();
            
            if ($user->isSuperAdminGlobal()) {
                return $query;
            }
            
            $ownerId = $user->isSuperAdmin() ? $user->id : $user->owner_id;
            return $query->where('owner_id', $ownerId);
        }
        return $query;
    }

    /**
     * Scope pour les utilisateurs d'un tenant spécifique
     */
    public function scopeByTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    /**
     * Scope pour les utilisateurs d'un rôle spécifique
     */
    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    /**
     * Scope pour les utilisateurs actifs
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope pour les utilisateurs inactifs
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope pour les utilisateurs en essai
     */
    public function scopeOnTrial($query)
    {
        return $query->where('subscription_status', 'trial')
                     ->whereNotNull('trial_ends_at')
                     ->where('trial_ends_at', '>', now());
    }

    /**
     * Scope pour les utilisateurs avec abonnement actif
     */
    public function scopeSubscribed($query)
    {
        return $query->where('subscription_status', 'active')
                     ->whereNotNull('subscription_ends_at')
                     ->where('subscription_ends_at', '>', now());
    }

    /**
     * Scope pour les utilisateurs avec abonnement expiré
     */
    public function scopeExpired($query)
    {
        return $query->where(function($q) {
            $q->where('subscription_status', 'expired')
              ->orWhere('subscription_status', 'trial')
              ->where(function($sub) {
                  $sub->whereNull('trial_ends_at')
                      ->orWhere('trial_ends_at', '<=', now());
              });
        });
    }

    /**
     * Scope pour la recherche
     */
    public function scopeSearch($query, $term)
    {
        return $query->where(function($q) use ($term) {
            $q->where('name', 'LIKE', "%{$term}%")
              ->orWhere('email', 'LIKE', "%{$term}%");
        });
    }

    /**
     * =====================================================
     * MÉTHODES UTILITAIRES
     * =====================================================
     */

    /**
     * Enregistrer la dernière connexion
     */
    public function recordLogin($ip = null): self
    {
        $this->last_login_at = now();
        $this->last_login_ip = $ip;
        $this->save();

        return $this;
    }

    /**
     * Récupère tous les tenants (quincailleries) accessibles
     */
    public function getAccessibleTenants()
    {
        if ($this->isSuperAdminGlobal()) {
            return Tenant::all();
        }
        
        return Tenant::where('id', $this->tenant_id)->get();
    }

    /**
     * Récupère le nom du rôle en français
     */
    public function getRoleLabelAttribute(): string
    {
        return UserRole::tryFrom($this->role)?->label() ?? 'Employé';
    }

    /**
     * Récupère la couleur du badge pour le rôle
     */
    public function getRoleColorAttribute(): string
    {
        return UserRole::tryFrom($this->role)?->color() ?? 'gray';
    }

    /**
     * Statut de l'utilisateur
     */
    public function getStatusLabelAttribute(): string
    {
        return $this->is_active ? 'Actif' : 'Inactif';
    }

    /**
     * Statut classe CSS
     */
    public function getStatusClassAttribute(): string
    {
        return $this->is_active ? 'text-green-600 bg-green-100' : 'text-red-600 bg-red-100';
    }

    /**
     * Initiales de l'utilisateur
     */
    public function getInitialsAttribute(): string
    {
        $words = explode(' ', $this->name);
        $initials = '';

        foreach ($words as $word) {
            $initials .= strtoupper(substr($word, 0, 1));
        }

        return substr($initials, 0, 2);
    }

    /**
     * Dernière connexion formatée
     */
    public function getLastLoginFormattedAttribute(): string
    {
        return $this->last_login_at ? $this->last_login_at->format('d/m/Y H:i') : 'Jamais';
    }

    /**
     * Récupère le nombre d'employés (si c'est un super_admin)
     */
    public function getEmployeesCountAttribute(): int
    {
        if ($this->isSuperAdmin()) {
            return $this->employees()->count();
        }
        return 0;
    }

    /**
     * Vérifie si l'utilisateur peut accéder au dashboard
     */
    public function canAccessDashboard(): bool
    {
        return $this->is_active ?? true;
    }

    /**
     * Récupère le dashboard approprié selon le rôle
     */
    public function getDashboardRoute(): string
    {
        if ($this->isSuperAdminGlobal()) {
            return route('super-admin.dashboard');
        }
        
        return route('dashboard');
    }

    /**
     * =====================================================
     * MÉTHODES POUR L'ABONNEMENT (surcharge du trait)
     * =====================================================
     */

    /**
     * Vérifie si l'utilisateur a accès (version multi-tenant)
     */
    public function hasAccess()
    {
        // Super admin global a toujours accès
        if ($this->isSuperAdminGlobal()) {
            return true;
        }
        
        // Vérifier l'abonnement du tenant
        if ($this->tenant && $this->tenant->hasActiveSubscription()) {
            return true;
        }
        
        // Vérifier l'abonnement individuel
        if ($this->isSubscribed()) {
            return true;
        }
        
        // Vérifier la période d'essai
        if ($this->isOnTrial()) {
            return true;
        }
        
        return false;
    }

    /**
     * Vérifie si l'utilisateur est en période d'essai
     */
    public function isOnTrial(): bool
    {
        return $this->subscription_status === 'trial' && 
               $this->trial_ends_at && 
               $this->trial_ends_at->isFuture();
    }

    /**
     * Vérifie si l'abonnement est actif
     */
    public function isSubscribed(): bool
    {
        return $this->subscription_status === 'active' && 
               $this->subscription_ends_at && 
               $this->subscription_ends_at->isFuture();
    }

    /**
     * Jours restants d'essai
     */
    public function trialDaysRemaining(): int
    {
        if (!$this->trial_ends_at) {
            return 0;
        }
        
        $days = now()->diffInDays($this->trial_ends_at, false);
        return $days > 0 ? $days : 0;
    }

    /**
     * Jours restants d'abonnement
     */
    public function subscriptionDaysRemaining(): int
    {
        if (!$this->subscription_ends_at) {
            return 0;
        }
        
        $days = now()->diffInDays($this->subscription_ends_at, false);
        return $days > 0 ? $days : 0;
    }

    /**
     * Activer l'abonnement après paiement
     */
    public function activateSubscription($transactionId, $months = 1): self
    {
        $this->update([
            'subscription_status' => 'active',
            'subscription_ends_at' => now()->addMonths($months),
            'fedapay_transaction_id' => $transactionId,
            'last_payment_at' => now(),
        ]);
        
        // Mettre à jour aussi le tenant si nécessaire
        if ($this->tenant) {
            $this->tenant->update([
                'subscription_status' => 'active',
                'subscription_ends_at' => now()->addMonths($months),
            ]);
        }
        
        return $this;
    }

    /**
     * Démarrer la période d'essai
     */
    public function startTrial($days = 14): self
    {
        $this->update([
            'subscription_status' => 'trial',
            'trial_ends_at' => now()->addDays($days),
            'subscription_ends_at' => null,
            'fedapay_transaction_id' => null,
            'last_payment_at' => null,
        ]);
        
        return $this;
    }

    /**
     * Expirer l'abonnement
     */
    public function expireSubscription(): self
    {
        $this->update([
            'subscription_status' => 'expired',
        ]);
        
        return $this;
    }

    /**
     * Vérifier si l'abonnement expire bientôt (dans 3 jours)
     */
    public function subscriptionExpiresSoon(): bool
    {
        if (!$this->subscription_ends_at) {
            return false;
        }
        
        return now()->diffInDays($this->subscription_ends_at, false) <= 3 && 
               $this->subscription_ends_at->isFuture();
    }

    /**
     * Récupérer le statut de l'abonnement en français
     */
    public function getSubscriptionStatusLabelAttribute(): string
    {
        return SubscriptionStatus::tryFrom($this->subscription_status)?->label() ?? 'Inconnu';
    }

    /**
     * Récupérer la couleur du badge de statut d'abonnement
     */
    public function getSubscriptionStatusColorAttribute(): string
    {
        return SubscriptionStatus::tryFrom($this->subscription_status)?->color() ?? 'gray';
    }

    /**
     * Vérifier si l'utilisateur a besoin de payer
     */
    public function needsPayment(): bool
    {
        return !$this->isOnTrial() && !$this->isSubscribed() && !$this->isSuperAdminGlobal();
    }

    /**
     * Récupérer l'URL de paiement
     */
    public function getPaymentUrlAttribute(): string
    {
        return route('payment.form');
    }

    /**
     * Vérifie si le tenant de l'utilisateur a un abonnement actif
     */
    public function hasActiveTenantSubscription(): bool
    {
        if ($this->isSuperAdminGlobal()) {
            return true;
        }
        
        return $this->tenant && $this->tenant->hasActiveSubscription();
    }

    /**
     * Récupère les jours restants de l'abonnement du tenant
     */
    public function getTenantDaysRemainingAttribute(): int
    {
        if ($this->isSuperAdminGlobal() || !$this->tenant) {
            return 365;
        }
        
        return $this->tenant->daysRemaining();
    }

    /**
     * =====================================================
     * MÉTHODES STATISTIQUES
     * =====================================================
     */

    /**
     * Récupérer les statistiques de l'utilisateur
     */
    public function getStatistics(): array
    {
        return [
            'total_sales' => $this->sales()->count(),
            'total_sales_amount' => $this->sales()->sum('final_price'),
            'formatted_sales_amount' => number_format($this->sales()->sum('final_price'), 0, ',', ' ') . ' FCFA',
            'total_stock_movements' => $this->stockMovements()->count(),
            'total_products_created' => $this->products()->count(),
            'total_clients_created' => $this->clients()->count(),
            'total_suppliers_created' => $this->suppliers()->count(),
            'average_sale_value' => $this->sales()->avg('final_price') ?? 0,
            'last_sale_date' => $this->sales()->latest()->first()?->created_at?->format('d/m/Y H:i'),
            'sales_this_month' => $this->sales()->whereMonth('created_at', now()->month)->count(),
            'sales_today' => $this->sales()->whereDate('created_at', today())->count(),
        ];
    }

    /**
     * Récupérer les statistiques globales des utilisateurs
     * Version optimisée pour PostgreSQL
     */
    public static function getGlobalUserStats($tenantId = null): array
    {
        $query = self::query();

        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }

        $total = (clone $query)->count();
        $active = (clone $query)->active()->count();
        $inactive = (clone $query)->inactive()->count();

        $statsByRole = (clone $query)
            ->select('role', DB::raw('COUNT(*) as count'))
            ->groupBy('role')
            ->get()
            ->mapWithKeys(function($item) {
                return [$item->role => $item->count];
            });

        return [
            'total_users' => $total,
            'active_users' => $active,
            'inactive_users' => $inactive,
            'stats_by_role' => $statsByRole,
            'super_admins_global' => $statsByRole['super_admin_global'] ?? 0,
            'super_admins' => $statsByRole['super_admin'] ?? 0,
            'admins' => $statsByRole['admin'] ?? 0,
            'managers' => $statsByRole['manager'] ?? 0,
            'cashiers' => $statsByRole['cashier'] ?? 0,
            'storekeepers' => $statsByRole['storekeeper'] ?? 0,
        ];
    }

    /**
     * Activer l'utilisateur
     */
    public function activate(): self
    {
        $this->is_active = true;
        $this->save();

        return $this;
    }

    /**
     * Désactiver l'utilisateur
     */
    public function deactivate(): self
    {
        $this->is_active = false;
        $this->save();

        return $this;
    }
}