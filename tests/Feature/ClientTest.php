<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\CreatesTenant;
use Tests\TestCase;

class ClientTest extends TestCase
{
    use RefreshDatabase, CreatesTenant;

    private User $admin;
    private User $cashier;
    private Tenant $tenant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant  = $this->makeTenant();
        $this->admin   = $this->makeAdminFor($this->tenant);
        $this->cashier = $this->makeCashierFor($this->tenant);
    }

    public function test_admin_can_create_client(): void
    {
        $response = $this->actingAs($this->admin)->post(route('clients.store'), [
            'name'  => 'Jean Dupont',
            'email' => 'jean@example.com',
            'phone' => '97000001',
        ]);

        $response->assertRedirect(route('clients.index'));
        $this->assertDatabaseHas('clients', [
            'name'      => 'Jean Dupont',
            'email'     => 'jean@example.com',
            'tenant_id' => $this->tenant->id,
        ]);
    }

    public function test_cashier_can_create_client(): void
    {
        $response = $this->actingAs($this->cashier)->post(route('clients.store'), [
            'name'  => 'Jean Dupont',
            'phone' => '97000001',
        ]);

        $response->assertRedirect(route('clients.index'));
        $this->assertDatabaseHas('clients', [
            'name'      => 'Jean Dupont',
            'tenant_id' => $this->tenant->id,
        ]);
    }

    public function test_cashier_can_quick_create_client(): void
    {
        $response = $this->actingAs($this->cashier)->postJson(route('clients.quick-store'), [
            'name'  => 'Quick Client',
            'phone' => '97000002',
        ]);

        $response->assertOk();
        $response->assertJson(['success' => true]);
        $this->assertDatabaseHas('clients', [
            'name'      => 'Quick Client',
            'tenant_id' => $this->tenant->id,
        ]);
    }

    public function test_duplicate_email_within_same_tenant_is_rejected(): void
    {
        Client::create([
            'name'      => 'Premier Client',
            'email'     => 'same@example.com',
            'tenant_id' => $this->tenant->id,
        ]);

        $response = $this->actingAs($this->admin)->post(route('clients.store'), [
            'name'  => 'Deuxième Client',
            'email' => 'same@example.com',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertDatabaseCount('clients', 1);
    }

    public function test_same_email_allowed_across_different_tenants(): void
    {
        $otherTenant = $this->makeTenant(['email' => 'other@tenant.com']);

        Client::create([
            'name'      => 'Client A',
            'email'     => 'shared@example.com',
            'tenant_id' => $otherTenant->id,
        ]);

        $response = $this->actingAs($this->admin)->post(route('clients.store'), [
            'name'  => 'Client B',
            'email' => 'shared@example.com',
        ]);

        $response->assertRedirect(route('clients.index'));
        $this->assertDatabaseCount('clients', 2);
    }

    public function test_admin_can_update_client(): void
    {
        $client = Client::create([
            'name'      => 'Ancien Nom',
            'tenant_id' => $this->tenant->id,
            'owner_id'  => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin)->put(route('clients.update', $client), [
            'name'  => 'Nouveau Nom',
            'email' => null,
            'phone' => null,
        ]);

        $response->assertRedirect(route('clients.index'));
        $this->assertDatabaseHas('clients', ['id' => $client->id, 'name' => 'Nouveau Nom']);
    }

    public function test_admin_can_soft_delete_client_with_sales(): void
    {
        $client = Client::create([
            'name'      => 'Client avec ventes',
            'tenant_id' => $this->tenant->id,
            'owner_id'  => $this->admin->id,
        ]);

        \App\Models\Sale::create([
            'client_id'   => $client->id,
            'user_id'     => $this->admin->id,
            'tenant_id'   => $this->tenant->id,
            'total_price' => 5000,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('clients.destroy', $client));

        $response->assertRedirect(route('clients.index'));
        // Soft delete : ligne toujours en DB mais avec deleted_at renseigné
        $this->assertSoftDeleted('clients', ['id' => $client->id]);
        // Les ventes sont préservées
        $this->assertDatabaseHas('sales', ['client_id' => $client->id]);
    }

    public function test_client_search_returns_json(): void
    {
        Client::create(['name' => 'Kouame Yao', 'tenant_id' => $this->tenant->id, 'owner_id' => $this->cashier->id]);
        Client::create(['name' => 'Ama Koffi', 'tenant_id' => $this->tenant->id, 'owner_id' => $this->cashier->id]);

        $response = $this->actingAs($this->cashier)
            ->getJson('/clients/search?q=kouam');

        $response->assertOk();
        $response->assertJsonCount(1);
        $response->assertJsonFragment(['name' => 'Kouame Yao']);
    }
}
