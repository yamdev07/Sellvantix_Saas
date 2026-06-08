<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Services\CategoryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\Support\CreatesTenant;
use Tests\TestCase;

class CategoryServiceTest extends TestCase
{
    use RefreshDatabase, CreatesTenant;

    private CategoryService $service;
    private int $tenantId;
    private int $userId;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(CategoryService::class);

        $tenant = $this->makeTenant();
        $admin  = $this->makeAdminFor($tenant, ['role' => 'super_admin']);
        $this->actingAs($admin);

        $this->tenantId = $tenant->id;
        $this->userId   = $admin->id;
    }

    // =========================================================
    // Helper
    // =========================================================

    private function makeCategory(array $overrides = []): Category
    {
        return Category::create(array_merge([
            'name'      => 'Catégorie Test',
            'tenant_id' => $this->tenantId,
            'owner_id'  => $this->userId,
        ], $overrides));
    }

    private function makeProduct(Category $category, array $overrides = []): Product
    {
        return Product::create(array_merge([
            'name'           => 'Produit Test',
            'stock'          => 10,
            'purchase_price' => 100.0,
            'sale_price'     => 150.0,
            'category_id'    => $category->id,
            'tenant_id'      => $this->tenantId,
            'owner_id'       => $this->userId,
        ], $overrides));
    }

    // =========================================================
    // 1. create_main_category
    // =========================================================

    public function test_create_main_category(): void
    {
        $category = $this->service->create(
            ['name' => 'Outils', 'parent_id' => null, 'description' => null],
            $this->tenantId,
            $this->userId
        );

        $this->assertInstanceOf(Category::class, $category);
        $this->assertDatabaseHas('categories', [
            'name'      => 'Outils',
            'parent_id' => null,
            'tenant_id' => $this->tenantId,
            'owner_id'  => $this->userId,
        ]);
        $this->assertNull($category->parent_id);
    }

    // =========================================================
    // 2. create_subcategory
    // =========================================================

    public function test_create_subcategory(): void
    {
        $parent = $this->service->create(
            ['name' => 'Quincaillerie', 'parent_id' => null, 'description' => null],
            $this->tenantId,
            $this->userId
        );

        $child = $this->service->create(
            ['name' => 'Visserie', 'parent_id' => $parent->id, 'description' => null],
            $this->tenantId,
            $this->userId
        );

        $this->assertInstanceOf(Category::class, $child);
        $this->assertEquals($parent->id, $child->parent_id);
        $this->assertDatabaseHas('categories', [
            'name'      => 'Visserie',
            'parent_id' => $parent->id,
            'tenant_id' => $this->tenantId,
        ]);
    }

    // =========================================================
    // 3. update_category_name
    // =========================================================

    public function test_update_category_name(): void
    {
        $category = $this->makeCategory(['name' => 'Ancien Nom']);

        $updated = $this->service->update(
            $category,
            ['name' => 'Nouveau Nom'],
            $this->tenantId
        );

        $this->assertEquals('Nouveau Nom', $updated->name);
        $this->assertDatabaseHas('categories', [
            'id'   => $category->id,
            'name' => 'Nouveau Nom',
        ]);
        $this->assertDatabaseMissing('categories', [
            'id'   => $category->id,
            'name' => 'Ancien Nom',
        ]);
    }

    // =========================================================
    // 4. delete_empty_category
    // =========================================================

    public function test_delete_empty_category(): void
    {
        $category = $this->makeCategory(['name' => 'Vide']);

        $this->service->delete($category);

        $this->assertDatabaseMissing('categories', [
            'id' => $category->id,
        ]);
    }

    // =========================================================
    // 5. delete_category_with_products_throws
    // =========================================================

    public function test_delete_category_with_products_throws(): void
    {
        $category = $this->makeCategory(['name' => 'Avec Produits']);
        $this->makeProduct($category);

        $this->expectException(RuntimeException::class);

        $this->service->delete($category);
    }

    // =========================================================
    // 6. delete_transfers_children_to_parent
    // =========================================================

    public function test_delete_transfers_children_to_parent(): void
    {
        // grandparent → parent → child
        $grandparent = $this->makeCategory(['name' => 'Grand-parent']);
        $parent      = $this->makeCategory(['name' => 'Parent', 'parent_id' => $grandparent->id]);
        $child       = $this->makeCategory(['name' => 'Enfant', 'parent_id' => $parent->id]);

        // Delete the middle node (parent); it has no direct products
        $this->service->delete($parent);

        // child should now point to grandparent
        $this->assertEquals($grandparent->id, $child->fresh()->parent_id);

        // parent itself must be gone
        $this->assertDatabaseMissing('categories', ['id' => $parent->id]);
    }

    // =========================================================
    // 7. is_circular_detects_loop
    // =========================================================

    public function test_is_circular_detects_loop(): void
    {
        // Build: grandparent → parent → child
        $grandparent = $this->makeCategory(['name' => 'Grand-parent']);
        $parent      = $this->makeCategory(['name' => 'Parent',   'parent_id' => $grandparent->id]);
        $child       = $this->makeCategory(['name' => 'Enfant',   'parent_id' => $parent->id]);

        // Attempting to set grandparent's parent to child would create a cycle:
        // grandparent → parent → child → grandparent
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageMatches('/circulaire/i');

        $this->service->update(
            $grandparent,
            ['parent_id' => $child->id],
            $this->tenantId
        );
    }

    // =========================================================
    // 8. detailed_stats_returns_counts
    // =========================================================

    public function test_detailed_stats_returns_counts(): void
    {
        $category = $this->makeCategory(['name' => 'Électricité']);

        $this->makeProduct($category, ['name' => 'Câble 1.5mm', 'stock' => 20]);
        $this->makeProduct($category, ['name' => 'Câble 2.5mm', 'stock' => 15]);

        $stats = $this->service->detailedStats($category);

        $this->assertArrayHasKey('total_products', $stats);
        $this->assertEquals(2, $stats['total_products']);

        $this->assertArrayHasKey('direct_products', $stats);
        $this->assertEquals(2, $stats['direct_products']);

        $this->assertArrayHasKey('subcategory_products', $stats);
        $this->assertEquals(0, $stats['subcategory_products']);

        $this->assertArrayHasKey('stock_distribution', $stats);
        $this->assertEquals(2, $stats['stock_distribution']['in_stock']);
        $this->assertEquals(0, $stats['stock_distribution']['out_of_stock']);
    }
}
