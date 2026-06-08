<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\User;

class CategoryPolicy
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

    public function view(User $user, Category $category): bool
    {
        return $user->tenant_id === $category->tenant_id;
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdminOrAdmin() || $user->isManager();
    }

    public function update(User $user, Category $category): bool
    {
        return ($user->isSuperAdminOrAdmin() || $user->isManager())
            && $user->tenant_id === $category->tenant_id;
    }

    public function delete(User $user, Category $category): bool
    {
        return $user->isSuperAdminOrAdmin()
            && $user->tenant_id === $category->tenant_id;
    }
}
