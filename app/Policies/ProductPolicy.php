<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    public function before(User $user): ?bool
    {
        if ($user->isSuperAdminGlobal()) {
            return true;
        }
        return null;
    }

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Product $product): bool
    {
        return $user->tenant_id === $product->tenant_id;
    }

    public function create(User $user): bool
    {
        return $user->canManageStock();
    }

    public function update(User $user, Product $product): bool
    {
        return $user->canManageStock() && $user->tenant_id === $product->tenant_id;
    }

    public function delete(User $user, Product $product): bool
    {
        return $user->isSuperAdminOrAdmin() && $user->tenant_id === $product->tenant_id;
    }
}
