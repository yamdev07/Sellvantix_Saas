<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Models\User;
use App\Services\PlanService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserService
{
    // =========================================================
    // LISTING
    // =========================================================

    /**
     * Return a paginated list of employees scoped to the current user's company.
     */
    public function list(User $currentUser): LengthAwarePaginator
    {
        $ownerId = $currentUser->isSuperAdmin()
            ? $currentUser->id
            : $currentUser->owner_id;

        $query = User::where('owner_id', $ownerId);

        if (!$currentUser->isSuperAdmin()) {
            $query->where('role', '!=', UserRole::SuperAdmin->value);
        }

        return $query->orderBy('created_at', 'desc')->paginate(10);
    }

    // =========================================================
    // ROLES
    // =========================================================

    /**
     * Return the roles that can be assigned by the current user as role => label pairs.
     */
    public function availableRoles(User $currentUser): array
    {
        $roles = $currentUser->isSuperAdmin()
            ? [
                UserRole::Admin,
                UserRole::Manager,
                UserRole::Cashier,
                UserRole::Storekeeper,
              ]
            : [
                UserRole::Manager,
                UserRole::Cashier,
                UserRole::Storekeeper,
              ];

        $result = [];
        foreach ($roles as $role) {
            $result[$role->value] = $role->label();
        }

        return $result;
    }

    // =========================================================
    // CREATE
    // =========================================================

    /**
     * Create a new employee after checking the plan's user limit.
     *
     * @throws \RuntimeException When the plan limit has been reached.
     */
    public function create(User $currentUser, array $data): User
    {
        // Resolve the tenant for the plan check
        $tenant = $currentUser->tenant
            ?? ($currentUser->owner ? $currentUser->owner->tenant : null);

        if ($tenant) {
            $ownerId       = $currentUser->isSuperAdmin() ? $currentUser->id : $currentUser->owner_id;
            $currentCount  = User::where('owner_id', $ownerId)->count();
            $planService   = PlanService::for($tenant);

            if (!$planService->canAddUser($currentCount)) {
                $max     = $planService->maxUsers();
                $message = "Votre plan ne permet pas d'ajouter plus de {$max} utilisateurs.";
                throw new \RuntimeException('PLAN_LIMIT:' . $message);
            }
        }

        $role    = $data['role'] ?? UserRole::Cashier->value;
        $ownerId = $currentUser->isSuperAdmin() ? $currentUser->id : $currentUser->owner_id;

        return User::create([
            'name'             => $data['name'],
            'email'            => $data['email'],
            'password'         => Hash::make($data['password']),
            'role'             => $role,
            'tenant_id'        => $currentUser->tenant_id,
            'owner_id'         => $ownerId,
            'can_manage_users' => $role === UserRole::Admin->value,
        ]);
    }

    // =========================================================
    // UPDATE
    // =========================================================

    /**
     * Update an employee's profile and optionally their password.
     */
    public function update(User $target, array $data): User
    {
        $role = $data['role'] ?? $target->role;

        $target->update([
            'name'             => $data['name']  ?? $target->name,
            'email'            => $data['email'] ?? $target->email,
            'role'             => $role,
            'can_manage_users' => $role === UserRole::Admin->value,
        ]);

        if (!empty($data['password'])) {
            $target->password = Hash::make($data['password']);
            $target->save();
        }

        return $target->fresh();
    }

    // =========================================================
    // DELETE
    // =========================================================

    /**
     * Delete an employee after performing safety checks.
     *
     * @throws \RuntimeException When deletion is forbidden.
     */
    public function delete(User $current, User $target): void
    {
        if ($current->id === $target->id) {
            throw new \RuntimeException('Vous ne pouvez pas supprimer votre propre compte.');
        }

        if ($target->isSuperAdmin()) {
            throw new \RuntimeException('Impossible de supprimer un super administrateur.');
        }

        if ($target->sales()->exists()) {
            throw new \RuntimeException(
                "Impossible de supprimer {$target->name} car cet utilisateur a des ventes enregistrées."
            );
        }

        $target->delete();
    }

    // =========================================================
    // ROLE PROMOTIONS / REVOCATIONS
    // =========================================================

    /**
     * Downgrade a user from admin to manager role.
     */
    public function revokeAdmin(User $target): User
    {
        $target->update([
            'role'             => UserRole::Manager->value,
            'can_manage_users' => false,
        ]);

        return $target->fresh();
    }

    /**
     * Promote a user to admin role.
     */
    public function promoteToAdmin(User $target): User
    {
        $target->update([
            'role'             => UserRole::Admin->value,
            'can_manage_users' => true,
        ]);

        return $target->fresh();
    }

    // =========================================================
    // STATISTICS
    // =========================================================

    /**
     * Return employee statistics for the current user's company.
     */
    public function statistics(User $currentUser): array
    {
        $ownerId = $currentUser->isSuperAdmin()
            ? $currentUser->id
            : $currentUser->owner_id;

        $employees = User::where('owner_id', $ownerId)
            ->withCount('sales')
            ->withSum('sales', 'final_price')
            ->get();

        $total        = $employees->count();
        $byRole       = $employees->groupBy('role')->map->count();
        $totalSales   = $employees->sum('sales_count');
        $totalRevenue = $employees->sum('sales_sum_final_price') ?? 0;

        $cashiers  = $employees->where('role', UserRole::Cashier->value);
        $managers  = $employees->where('role', UserRole::Manager->value);

        $bestCashier = $cashiers->sortByDesc('sales_sum_final_price')->first();
        $bestManager = $managers->sortByDesc('sales_sum_final_price')->first();

        $averagePerEmployee = $total > 0 ? round($totalRevenue / $total, 2) : 0;

        return [
            'employees' => $employees,
            'stats'     => [
                'total'                => $total,
                'by_role'              => $byRole,
                'total_sales'          => $totalSales,
                'total_revenue'        => $totalRevenue,
                'best_cashier'         => $bestCashier,
                'best_manager'         => $bestManager,
                'average_per_employee' => $averagePerEmployee,
            ],
        ];
    }

    // =========================================================
    // PERMISSION HELPERS
    // =========================================================

    /**
     * Determine whether $actor is allowed to manage $target.
     */
    public function canManage(User $actor, User $target): bool
    {
        if (!$actor->hasAccessTo($target)) {
            return false;
        }

        if (!$actor->isSuperAdmin() && $target->isSuperAdmin()) {
            return false;
        }

        if (!$actor->isSuperAdmin() && $target->isAdmin() && $target->id !== $actor->id) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether $actor is allowed to create a user with the given role.
     */
    public function canCreateRole(User $actor, string $role): bool
    {
        if ($role === UserRole::Admin->value && !$actor->isSuperAdmin()) {
            return false;
        }

        if ($actor->isAdmin() && !in_array($role, [
            UserRole::Manager->value,
            UserRole::Cashier->value,
            UserRole::Storekeeper->value,
        ], true)) {
            return false;
        }

        return true;
    }
}
