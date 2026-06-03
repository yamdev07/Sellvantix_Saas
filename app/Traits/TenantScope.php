<?php
// app/Traits/TenantScope.php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait TenantScope
{
    /**
     * Boot the tenant scope trait.
     */
    protected static function bootTenantScope()
    {
        // Appliquer automatiquement le filtre sur TOUTES les requêtes
        static::addGlobalScope('tenant', function (Builder $builder) {
            if (Auth::check()) {
                $user = Auth::user();
                
                // Super Admin Global voit tout
                if ($user->isSuperAdminGlobal()) {
                    return;
                }
                
                // Filtrer par tenant_id (tous les utilisateurs de la même quincaillerie)
                if ($user->tenant_id) {
                    $builder->where($builder->getModel()->getTable() . '.tenant_id', $user->tenant_id);
                }
            }
        });

        // Assigner automatiquement tenant_id et owner_id à la création
        static::creating(function ($model) {
            if (Auth::check()) {
                $user = Auth::user();

                $model->tenant_id = $user->tenant_id;

                // N'assigner owner_id que si la table le supporte
                if (in_array('owner_id', $model->getFillable())) {
                    $ownerId = $user->isSuperAdmin() ? $user->id : ($user->owner_id ?? $user->id);
                    $model->owner_id = $ownerId;
                }
            }
        });
    }

    /**
     * Désactiver temporairement le scope tenant (pour super admin global)
     */
    public function scopeWithoutTenant($query)
    {
        return $query->withoutGlobalScope('tenant');
    }

    /**
     * Voir toutes les données (super admin global uniquement)
     */
    public function scopeAllData($query)
    {
        if (Auth::check() && Auth::user()->isSuperAdminGlobal()) {
            return $query->withoutGlobalScope('tenant');
        }
        return $query;
    }

    /**
     * Scope pour filtrer par utilisateur (owner_id)
     */
    public function scopeOwnedBy($query, $userId = null)
    {
        $userId = $userId ?? Auth::id();
        return $query->where($query->getModel()->getTable() . '.owner_id', $userId);
    }

    /**
     * Vérifier si l'utilisateur a accès à ce modèle
     */
    public function isAccessibleBy($user = null)
    {
        $user = $user ?? Auth::user();
        
        if (!$user) return false;
        
        // Super Admin Global voit tout
        if ($user->isSuperAdminGlobal()) {
            return true;
        }
        
        // Même tenant = accessible
        return $this->tenant_id === $user->tenant_id;
    }

    /**
     * Vérifier si l'utilisateur est le propriétaire (créateur)
     */
    public function isOwnedBy($user = null)
    {
        $user = $user ?? Auth::user();
        
        if (!$user) return false;
        
        return $this->owner_id === ($user->owner_id ?? $user->id) || 
               $this->owner_id === $user->id;
    }

    /**
     * Relation avec le propriétaire (créateur)
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Relation avec le tenant (quincaillerie)
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }
}