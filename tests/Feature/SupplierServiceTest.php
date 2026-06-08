<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Services\SupplierService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\Support\CreatesTenant;
use Tests\TestCase;

class SupplierServiceTest extends TestCase
{
    use RefreshDatabase, CreatesTenant;

    private SupplierService $service;
    private int $tenantId;
    private int $userId;
    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(SupplierService::class);

        $tenant = $this->makeTenant();
        $admin  = $this->makeAdminFor($tenant);
        $this->actingAs($admin);

        $this->tenantId = $tenant->id;
        $this->userId   = $admin->id;

        $this->category = Category::create([
            'name'      => 'Catégorie Test',
            'tenant_id' => $tenant->id,
        ]);
    }

    // ---------------------------------------------------------------
    // Helpers
    // ---------------------------------------------------------------

    private function makeSupplier(array $overrides = []): Supplier
    {
        return Supplier::create(array_merge([
            'name'      => 'Fournisseur Test',
            'phone'     => '0600000000',
            'tenant_id' => $this->tenantId,
        ], $overrides));
    }

    private function attachProduct(Supplier $supplier, array $overrides = []): Product
    {
        return Product::create(array_merge([
            'name'           => 'Produit Test',
            'stock'          => 10,
            'purchase_price' => 100.0,
            'sale_price'     => 150.0,
            'category_id'    => $this->category->id,
            'supplier_id'    => $supplier->id,
            'tenant_id'      => $this->tenantId,
        ], $overrides));
    }

    // ---------------------------------------------------------------
    // 1. create_supplier
    // ---------------------------------------------------------------

    public function test_create_supplier(): void
    {
        $supplier = $this->service->create([
            'name'      => 'CFAO',
            'phone'     => '0600000001',
            'tenant_id' => $this->tenantId,
        ]);

        $this->assertInstanceOf(Supplier::class, $supplier);
        $this->assertDatabaseHas('suppliers', [
            'name'  => 'CFAO',
            'phone' => '0600000001',
        ]);
    }

    // ---------------------------------------------------------------
    // 2. update_supplier
    // ---------------------------------------------------------------

    public function test_update_supplier(): void
    {
        $supplier = $this->makeSupplier();

        $updated = $this->service->update($supplier, ['name' => 'New']);

        $this->assertEquals('New', $updated->name);
        $this->assertEquals('New', $supplier->fresh()->name);
        $this->assertDatabaseHas('suppliers', ['id' => $supplier->id, 'name' => 'New']);
    }

    // ---------------------------------------------------------------
    // 3. delete_supplier_without_products
    // ---------------------------------------------------------------

    public function test_delete_supplier_without_products(): void
    {
        $supplier = $this->makeSupplier();
        $id       = $supplier->id;

        $this->service->delete($supplier);

        $this->assertDatabaseMissing('suppliers', ['id' => $id]);
    }

    // ---------------------------------------------------------------
    // 4. delete_supplier_with_products_throws
    // ---------------------------------------------------------------

    public function test_delete_supplier_with_products_throws(): void
    {
        $supplier = $this->makeSupplier();
        $this->attachProduct($supplier);

        $this->expectException(\RuntimeException::class);

        $this->service->delete($supplier);
    }

    // ---------------------------------------------------------------
    // 5. stats_returns_correct_totals
    // ---------------------------------------------------------------

    public function test_stats_returns_correct_totals(): void
    {
        $supplier = $this->makeSupplier();
        $this->attachProduct($supplier, ['stock' => 20, 'purchase_price' => 100.0, 'sale_price' => 150.0]);
        $this->attachProduct($supplier, ['name' => 'Produit B', 'stock' => 30, 'purchase_price' => 50.0, 'sale_price' => 80.0]);

        $stats = $this->service->stats($supplier);

        $this->assertEquals(2, $stats['total_products']);
        $this->assertEquals(50, $stats['total_stock']);
        $this->assertArrayHasKey('total_value', $stats);
        $this->assertArrayHasKey('total_sale_value', $stats);
        $this->assertArrayHasKey('potential_profit', $stats);
        $this->assertArrayHasKey('out_of_stock', $stats);
        $this->assertArrayHasKey('low_stock', $stats);
        $this->assertArrayHasKey('lowStockProducts', $stats);
    }

    // ---------------------------------------------------------------
    // 6. search_returns_matching
    // ---------------------------------------------------------------

    public function test_search_returns_matching(): void
    {
        $this->makeSupplier(['name' => 'CFAO Motors']);
        $this->makeSupplier(['name' => 'Brico Depot']);

        $results = $this->service->search('CFA');

        $this->assertCount(1, $results);
        $this->assertEquals('CFAO Motors', $results->first()->name);
    }

    // ---------------------------------------------------------------
    // 7. report_data_contains_suppliers
    // ---------------------------------------------------------------

    public function test_report_data_contains_suppliers(): void
    {
        $this->makeSupplier(['name' => 'CFAO']);
        $this->makeSupplier(['name' => 'Brico Depot']);

        $request = Request::create('/', 'GET');

        $data = $this->service->reportData($request);

        $this->assertArrayHasKey('suppliers', $data);
        $this->assertArrayHasKey('reportData', $data);

        $reportData = $data['reportData'];
        $this->assertArrayHasKey('total_suppliers', $reportData);
        $this->assertArrayHasKey('suppliers_with_products', $reportData);
        $this->assertArrayHasKey('suppliers_without_products', $reportData);
        $this->assertArrayHasKey('average_products_per_supplier', $reportData);
        $this->assertArrayHasKey('total_products', $reportData);
        $this->assertArrayHasKey('total_stock_value', $reportData);

        $this->assertGreaterThanOrEqual(2, $data['suppliers']->count());
        $this->assertGreaterThanOrEqual(2, $reportData['total_suppliers']);
    }
}
