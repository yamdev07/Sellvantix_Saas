<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Tenant;
use App\Models\User;
use App\Services\ClientService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\Support\CreatesTenant;
use Tests\TestCase;

class ClientServiceTest extends TestCase
{
    use RefreshDatabase, CreatesTenant;

    private ClientService $service;
    private Tenant $tenant;
    private User $admin;
    private int $tenantId;

    /** @var Client[] */
    private array $clients;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(ClientService::class);

        $this->tenant   = $this->makeTenant();
        $this->admin    = $this->makeAdminFor($this->tenant, ['role' => 'super_admin']);
        $this->tenantId = $this->tenant->id;

        $this->actingAs($this->admin);

        // Create 3 base clients belonging to the tenant
        $this->clients = [
            Client::create([
                'name'      => fake()->name(),
                'phone'     => fake()->numerify('06########'),
                'email'     => fake()->unique()->safeEmail(),
                'tenant_id' => $this->tenantId,
                'owner_id'  => $this->admin->id,
            ]),
            Client::create([
                'name'      => fake()->name(),
                'phone'     => fake()->numerify('07########'),
                'email'     => fake()->unique()->safeEmail(),
                'tenant_id' => $this->tenantId,
                'owner_id'  => $this->admin->id,
            ]),
            Client::create([
                'name'      => fake()->name(),
                'phone'     => fake()->numerify('05########'),
                'email'     => fake()->unique()->safeEmail(),
                'tenant_id' => $this->tenantId,
                'owner_id'  => $this->admin->id,
            ]),
        ];
    }

    // =========================================================
    // Helpers
    // =========================================================

    /**
     * Create a bare-minimum Sale row for a given client.
     */
    private function makeSaleFor(Client $client, float $totalPrice = 5000): Sale
    {
        return Sale::create([
            'client_id'   => $client->id,
            'user_id'     => $this->admin->id,
            'tenant_id'   => $this->tenantId,
            'owner_id'    => $this->admin->id,
            'total_price' => $totalPrice,
            'final_price' => $totalPrice,
        ]);
    }

    /**
     * Create a Product and a SaleItem linking it to the given Sale.
     */
    private function makeSaleItem(Sale $sale, int $quantity = 2, float $unitPrice = 1500): SaleItem
    {
        $product = Product::create([
            'name'           => fake()->word() . ' ' . fake()->word(),
            'sale_price'     => $unitPrice,
            'purchase_price' => $unitPrice * 0.6,
            'stock'          => 100,
            'tenant_id'      => $this->tenantId,
            'owner_id'       => $this->admin->id,
        ]);

        return SaleItem::create([
            'sale_id'     => $sale->id,
            'product_id'  => $product->id,
            'quantity'    => $quantity,
            'unit_price'  => $unitPrice,
            'total_price' => $quantity * $unitPrice,
            'tenant_id'   => $this->tenantId,
            'owner_id'    => $this->admin->id,
        ]);
    }

    // =========================================================
    // Tests
    // =========================================================

    #[\PHPUnit\Framework\Attributes\Test]
    public function create_new_client(): void
    {
        $this->service->create([
            'name'  => 'Test',
            'phone' => '0600000001',
        ], $this->tenantId);

        $this->assertDatabaseHas('clients', [
            'name'      => 'Test',
            'phone'     => '0600000001',
            'tenant_id' => $this->tenantId,
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function create_returns_client_model(): void
    {
        $result = $this->service->create([
            'name'  => 'Test Client',
            'phone' => '0600000002',
        ], $this->tenantId);

        $this->assertInstanceOf(Client::class, $result);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function update_client_name(): void
    {
        $client = Client::create([
            'name'      => 'Old Name',
            'phone'     => '0600000003',
            'tenant_id' => $this->tenantId,
            'owner_id'  => $this->admin->id,
        ]);

        $this->service->update($client, ['name' => 'New Name']);

        $this->assertSame('New Name', $client->fresh()->name);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function delete_client_without_sales(): void
    {
        $client = Client::create([
            'name'      => fake()->name(),
            'phone'     => '0600000004',
            'tenant_id' => $this->tenantId,
            'owner_id'  => $this->admin->id,
        ]);

        $this->service->delete($client);

        $this->assertSoftDeleted('clients', ['id' => $client->id]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function delete_client_with_sales_soft_deletes(): void
    {
        $client = Client::create([
            'name'      => fake()->name(),
            'phone'     => '0600000005',
            'tenant_id' => $this->tenantId,
            'owner_id'  => $this->admin->id,
        ]);

        $this->makeSaleFor($client, 10000);

        $this->service->delete($client);

        $this->assertSoftDeleted('clients', ['id' => $client->id]);
        // Sales must be preserved
        $this->assertDatabaseHas('sales', ['client_id' => $client->id]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function search_returns_matching_clients(): void
    {
        Client::create([
            'name'      => 'Alice Dupont',
            'tenant_id' => $this->tenantId,
            'owner_id'  => $this->admin->id,
        ]);

        Client::create([
            'name'      => 'Bob Martin',
            'tenant_id' => $this->tenantId,
            'owner_id'  => $this->admin->id,
        ]);

        $results = $this->service->search('Ali');

        $this->assertCount(1, $results);
        $this->assertSame('Alice Dupont', $results->first()->name);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function stats_returns_correct_totals(): void
    {
        $client = Client::create([
            'name'      => fake()->name(),
            'tenant_id' => $this->tenantId,
            'owner_id'  => $this->admin->id,
        ]);

        $this->makeSaleFor($client, 3000);
        $this->makeSaleFor($client, 7000);

        $stats = $this->service->stats($client);

        $this->assertArrayHasKey('total_sales', $stats);
        $this->assertArrayHasKey('total_spent', $stats);
        $this->assertSame(2, $stats['total_sales']);
        $this->assertEquals(10000.0, $stats['total_spent']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function favorite_products_returns_top_five(): void
    {
        $client = Client::create([
            'name'      => fake()->name(),
            'tenant_id' => $this->tenantId,
            'owner_id'  => $this->admin->id,
        ]);

        // Create 7 sale items across 2 sales to have more than 5 distinct products
        $sale1 = $this->makeSaleFor($client, 5000);
        $sale2 = $this->makeSaleFor($client, 8000);

        for ($i = 0; $i < 4; $i++) {
            $this->makeSaleItem($sale1, rand(1, 5));
        }

        for ($i = 0; $i < 3; $i++) {
            $this->makeSaleItem($sale2, rand(1, 5));
        }

        $favorites = $this->service->favoriteProducts($client);

        $this->assertLessThanOrEqual(5, $favorites->count());
        $this->assertGreaterThanOrEqual(1, $favorites->count());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function report_data_includes_top_clients(): void
    {
        $request = Request::create('/clients/report', 'GET');

        $data = $this->service->reportData($request, $this->tenantId);

        $this->assertArrayHasKey('reportData', $data);
        $this->assertArrayHasKey('total_clients', $data['reportData']);
        $this->assertArrayHasKey('clients', $data);
        $this->assertArrayHasKey('topClients', $data);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function top_clients_ordered_by_sales(): void
    {
        $highVolumeClient = Client::create([
            'name'      => 'High Volume Client',
            'tenant_id' => $this->tenantId,
            'owner_id'  => $this->admin->id,
        ]);

        $lowVolumeClient = Client::create([
            'name'      => 'Low Volume Client',
            'tenant_id' => $this->tenantId,
            'owner_id'  => $this->admin->id,
        ]);

        // High volume client gets 5 sales
        for ($i = 0; $i < 5; $i++) {
            $this->makeSaleFor($highVolumeClient, 1000);
        }

        // Low volume client gets 1 sale
        $this->makeSaleFor($lowVolumeClient, 1000);

        $topClients = $this->service->topClients($this->tenantId);

        $this->assertTrue($topClients->isNotEmpty());
        $this->assertSame('High Volume Client', $topClients->first()->name);
        $this->assertEquals(5, $topClients->first()->sales_count);
    }
}
