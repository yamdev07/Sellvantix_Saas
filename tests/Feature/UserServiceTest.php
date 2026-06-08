<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Sale;
use App\Models\Tenant;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\Support\CreatesTenant;
use Tests\TestCase;

class UserServiceTest extends TestCase
{
    use RefreshDatabase, CreatesTenant;

    private UserService $service;
    private Tenant $tenant;
    private User $superAdmin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(UserService::class);

        $this->tenant = $this->makeTenant(['subscription_price' => 20000]); // 'pro' plan → unlimited users

        $this->superAdmin = User::factory()->create([
            'role'      => UserRole::SuperAdmin->value,
            'tenant_id' => $this->tenant->id,
            'owner_id'  => null,
        ]);

        $this->actingAs($this->superAdmin);
    }

    // =========================================================
    // HELPERS
    // =========================================================

    private function makeCashier(array $overrides = []): User
    {
        return User::factory()->create(array_merge([
            'role'      => UserRole::Cashier->value,
            'tenant_id' => $this->tenant->id,
            'owner_id'  => $this->superAdmin->id,
        ], $overrides));
    }

    // =========================================================
    // TESTS
    // =========================================================

    public function test_create_user_as_super_admin(): void
    {
        $user = $this->service->create($this->superAdmin, [
            'name'     => 'Bob',
            'email'    => 'bob@test.com',
            'password' => 'pass',
            'role'     => UserRole::Cashier->value,
        ]);

        $this->assertDatabaseHas('users', [
            'id'        => $user->id,
            'name'      => 'Bob',
            'email'     => 'bob@test.com',
            'role'      => UserRole::Cashier->value,
            'tenant_id' => $this->tenant->id,
        ]);
    }

    public function test_create_user_hashes_password(): void
    {
        $user = $this->service->create($this->superAdmin, [
            'name'     => 'Bob',
            'email'    => 'bob@test.com',
            'password' => 'pass',
            'role'     => UserRole::Cashier->value,
        ]);

        $fresh = User::find($user->id);

        $this->assertTrue(
            Hash::check('pass', $fresh->password),
            'The stored password should be a bcrypt hash of the plain-text password.'
        );
    }

    public function test_update_user_name(): void
    {
        $cashier = $this->makeCashier(['email' => 'cashier@test.com']);

        $updated = $this->service->update($cashier, [
            'name'  => 'New Name',
            'email' => 'cashier@test.com',
            'role'  => UserRole::Cashier->value,
        ]);

        $this->assertEquals('New Name', $updated->name);
        $this->assertDatabaseHas('users', [
            'id'   => $cashier->id,
            'name' => 'New Name',
        ]);
    }

    public function test_delete_user_succeeds(): void
    {
        $cashier = $this->makeCashier();

        $this->service->delete($this->superAdmin, $cashier);

        // Sale model uses SoftDeletes; fall back to assertDeleted if the User
        // model does not use SoftDeletes (it does not at the time of writing).
        $this->assertDatabaseMissing('users', ['id' => $cashier->id]);
    }

    public function test_delete_own_account_throws(): void
    {
        $this->expectException(\RuntimeException::class);

        $this->service->delete($this->superAdmin, $this->superAdmin);
    }

    public function test_delete_user_with_sales_throws(): void
    {
        $cashier = $this->makeCashier();

        // Create a minimal sale linked to the cashier, bypassing the TenantScope
        // global scope by inserting directly with the model.
        Sale::withoutGlobalScopes()->create([
            'invoice_number' => 'INV-TEST-0001',
            'user_id'        => $cashier->id,
            'owner_id'       => $this->superAdmin->id,
            'tenant_id'      => $this->tenant->id,
            'total_price'    => 1000,
            'final_price'    => 1000,
            'discount'       => 0,
            'tax'            => 0,
            'payment_method' => 'cash',
            'payment_status' => 'paid',
            'status'         => 'completed',
        ]);

        $this->expectException(\RuntimeException::class);

        $this->service->delete($this->superAdmin, $cashier);
    }

    public function test_promote_to_admin(): void
    {
        $manager = User::factory()->create([
            'role'      => UserRole::Manager->value,
            'tenant_id' => $this->tenant->id,
            'owner_id'  => $this->superAdmin->id,
        ]);

        $promoted = $this->service->promoteToAdmin($manager);

        $this->assertEquals(UserRole::Admin->value, $promoted->role);
        $this->assertTrue((bool) $promoted->can_manage_users);
        $this->assertDatabaseHas('users', [
            'id'               => $manager->id,
            'role'             => UserRole::Admin->value,
            'can_manage_users' => true,
        ]);
    }

    public function test_revoke_admin_rights(): void
    {
        $adminUser = User::factory()->create([
            'role'             => UserRole::Admin->value,
            'can_manage_users' => true,
            'tenant_id'        => $this->tenant->id,
            'owner_id'         => $this->superAdmin->id,
        ]);

        $revoked = $this->service->revokeAdmin($adminUser);

        $this->assertEquals(UserRole::Manager->value, $revoked->role);
        $this->assertFalse((bool) $revoked->can_manage_users);
        $this->assertDatabaseHas('users', [
            'id'               => $adminUser->id,
            'role'             => UserRole::Manager->value,
            'can_manage_users' => false,
        ]);
    }

    public function test_can_manage_returns_false_for_cross_tenant(): void
    {
        $otherTenant = $this->makeTenant();

        $foreignUser = User::factory()->create([
            'role'      => UserRole::Cashier->value,
            'tenant_id' => $otherTenant->id,
            'owner_id'  => null,
        ]);

        $result = $this->service->canManage($this->superAdmin, $foreignUser);

        $this->assertFalse($result);
    }

    public function test_available_roles_for_super_admin_includes_admin(): void
    {
        $roles = $this->service->availableRoles($this->superAdmin);

        $this->assertArrayHasKey(UserRole::Admin->value, $roles);
    }
}
