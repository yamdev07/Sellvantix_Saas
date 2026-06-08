<?php

namespace App\Policies;

use App\Models\Client;
use App\Models\User;

class ClientPolicy
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
        return $user->canManageSales();
    }

    public function view(User $user, Client $client): bool
    {
        return $user->canManageSales()
            && $user->tenant_id === $client->tenant_id;
    }

    public function create(User $user): bool
    {
        return $user->canManageSales();
    }

    public function update(User $user, Client $client): bool
    {
        return $user->canManageSales()
            && $user->tenant_id === $client->tenant_id;
    }

    public function delete(User $user, Client $client): bool
    {
        return $user->isSuperAdminOrAdmin()
            && $user->tenant_id === $client->tenant_id;
    }
}
