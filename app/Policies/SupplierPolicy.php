<?php

namespace App\Policies;

use App\Models\Supplier;
use App\Models\User;

class SupplierPolicy
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
        return $user->canManageStock();
    }

    public function view(User $user, Supplier $supplier): bool
    {
        return $user->canManageStock()
            && $user->tenant_id === $supplier->tenant_id;
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdminOrAdmin();
    }

    public function update(User $user, Supplier $supplier): bool
    {
        return $user->isSuperAdminOrAdmin()
            && $user->tenant_id === $supplier->tenant_id;
    }

    public function delete(User $user, Supplier $supplier): bool
    {
        return $user->isSuperAdmin()
            && $user->tenant_id === $supplier->tenant_id;
    }
}
