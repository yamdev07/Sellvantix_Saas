<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CategoryService
{
    // =========================================================
    // INDEX
    // =========================================================

    /**
     * Return paginated root categories with aggregated counts and totals
     * for the given tenant.
     */
    public function index(int $tenantId): array
    {
        $categories = Category::where('tenant_id', $tenantId)
            ->whereNull('parent_id')
            ->withCount('products')
            ->withSum('products', 'stock')
            ->distinct()
            ->paginate(15);

        $totalCategories = Category::where('tenant_id', $tenantId)->count();

        $categoriesWithChildren = Category::where('tenant_id', $tenantId)
            ->whereNull('parent_id')
            ->whereHas('children')
            ->count();

        $totalProductsInCategories = Product::where('tenant_id', $tenantId)
            ->whereNotNull('category_id')
            ->count();

        return [
            'categories'                 => $categories,
            'totalCategories'            => $totalCategories,
            'categoriesWithChildren'     => $categoriesWithChildren,
            'totalProductsInCategories'  => $totalProductsInCategories,
        ];
    }

    // =========================================================
    // SHOW
    // =========================================================

    /**
     * Return full detail for a single category, including stats and
     * low-stock products across the whole subtree.
     */
    public function show(Category $category, int $tenantId): array
    {
        $category->load(['children.products', 'products']);

        $mainCategories = Category::where('tenant_id', $tenantId)
            ->whereNull('parent_id')
            ->get();

        $allProducts = $category->getAllProducts();

        $lowStockProducts = $allProducts->filter(
            fn (Product $p) => $p->stock > 0 && $p->stock <= ($p->stock_alert ?? 10)
        )->values();

        $outOfStock   = $allProducts->where('stock', '=', 0)->count();
        $inStock      = $allProducts->where('stock', '>', 0)->count();
        $lowStockCount = $lowStockProducts->count();

        $totalStock   = $allProducts->sum('stock');
        $totalValue   = $allProducts->sum(fn (Product $p) => ($p->purchase_price ?? 0) * ($p->stock ?? 0));
        $potentialRev = $allProducts->sum(fn (Product $p) => ($p->sale_price ?? 0) * ($p->stock ?? 0));

        $stats = [
            'total_products'      => $allProducts->count(),
            'total_subcategories' => $category->children->count(),
            'total_stock'         => $totalStock,
            'total_value'         => $totalValue,
            'potential_revenue'   => $potentialRev,
            'low_stock'           => $lowStockCount,
            'out_of_stock'        => $outOfStock,
            'in_stock'            => $inStock,
            'direct_products'     => $category->products->count(),
        ];

        return [
            'category'         => $category,
            'mainCategories'   => $mainCategories,
            'stats'            => $stats,
            'lowStockProducts' => $lowStockProducts,
        ];
    }

    // =========================================================
    // CREATE
    // =========================================================

    /**
     * Create a new category, optionally nested under a parent that must
     * belong to the same tenant.
     */
    public function create(array $data, int $tenantId, int $userId): Category
    {
        if (!empty($data['parent_id'])) {
            $parent = Category::where('id', $data['parent_id'])
                ->where('tenant_id', $tenantId)
                ->first();

            if (!$parent) {
                throw new \RuntimeException(
                    'La catégorie parente est introuvable ou n\'appartient pas à ce tenant.'
                );
            }
        }

        return Category::create(array_merge($data, [
            'owner_id'  => $userId,
            'tenant_id' => $tenantId,
        ]));
    }

    // =========================================================
    // UPDATE
    // =========================================================

    /**
     * Update a category, validating that the new parent belongs to the
     * same tenant and that no circular reference would be introduced.
     */
    public function update(Category $category, array $data, int $tenantId): Category
    {
        if (!empty($data['parent_id'])) {
            $parent = Category::where('id', $data['parent_id'])
                ->where('tenant_id', $tenantId)
                ->first();

            if (!$parent) {
                throw new \RuntimeException(
                    'La catégorie parente est introuvable ou n\'appartient pas à ce tenant.'
                );
            }

            if ($this->isCircular($category, $parent)) {
                throw new \RuntimeException(
                    'Cette modification créerait une référence circulaire dans l\'arborescence des catégories.'
                );
            }
        }

        $category->update($data);

        return $category->fresh();
    }

    // =========================================================
    // DELETE (simple)
    // =========================================================

    /**
     * Delete a leaf category.
     * Throws RuntimeException if the category still has products attached.
     * Transfers children to the category's own parent (or makes them root).
     */
    public function delete(Category $category): void
    {
        $productsCount = $category->products()->count();

        if ($productsCount > 0) {
            throw new \RuntimeException(
                "Impossible de supprimer la catégorie \"{$category->name}\" : elle contient {$productsCount} produit(s). Utilisez la suppression avec transfert."
            );
        }

        // Re-parent children to this category's parent (or make them root)
        $newParentId = $category->parent_id; // null → root
        $category->children()->update(['parent_id' => $newParentId]);

        $category->delete();
    }

    // =========================================================
    // DELETE WITH TRANSFER
    // =========================================================

    /**
     * Delete a category after transferring its products (and optionally its
     * subcategories) to another existing category.
     */
    public function deleteWithTransfer(Category $category, int $newCategoryId, bool $moveSubs): void
    {
        DB::transaction(function () use ($category, $newCategoryId, $moveSubs) {
            // Transfer direct products
            $category->products()->update(['category_id' => $newCategoryId]);

            if ($moveSubs) {
                // Re-parent children to the target category
                $category->children()->update(['parent_id' => $newCategoryId]);
            } else {
                // Make children root (detach from this category)
                $category->children()->update(['parent_id' => $category->parent_id]);
            }

            $category->delete();
        });
    }

    // =========================================================
    // ADD PRODUCT
    // =========================================================

    /**
     * Assign an existing product (same tenant) to the given category.
     */
    public function addProduct(Category $category, int $productId, int $tenantId): void
    {
        $product = Product::where('id', $productId)
            ->where('tenant_id', $tenantId)
            ->firstOrFail();

        $product->update(['category_id' => $category->id]);
    }

    // =========================================================
    // TRANSFER PRODUCT
    // =========================================================

    /**
     * Move a product from one category to another within the same tenant.
     */
    public function transferProduct(int $productId, Category $from, Category $to, int $tenantId): void
    {
        $product = Product::where('id', $productId)
            ->where('tenant_id', $tenantId)
            ->where('category_id', $from->id)
            ->firstOrFail();

        $product->update(['category_id' => $to->id]);
    }

    // =========================================================
    // DETAILED STATS
    // =========================================================

    /**
     * Return granular statistics for a category, broken down by
     * subcategory and stock bucket.
     */
    public function detailedStats(Category $category): array
    {
        $category->load(['children.products', 'products']);

        $allProducts     = $category->getAllProducts();
        $directProducts  = $category->products;
        $subProducts     = $allProducts->diff($directProducts);

        // Breakdown by subcategory
        $bySubcategory = $category->children->map(function (Category $child) {
            $products    = $child->getAllProducts();
            $totalStock  = $products->sum('stock');
            $totalValue  = $products->sum(fn (Product $p) => ($p->sale_price ?? 0) * ($p->stock ?? 0));

            return [
                'id'           => $child->id,
                'name'         => $child->name,
                'product_count'=> $products->count(),
                'total_stock'  => $totalStock,
                'total_value'  => $totalValue,
            ];
        })->values();

        // Stock distribution across the full subtree
        $stockDistribution = [
            'in_stock'     => $allProducts->where('stock', '>', 0)->count(),
            'out_of_stock' => $allProducts->where('stock', '=', 0)->count(),
            'low_stock'    => $allProducts->filter(
                fn (Product $p) => $p->stock > 0 && $p->stock <= ($p->stock_alert ?? 10)
            )->count(),
        ];

        // Value aggregated by subcategory (for quick chart use)
        $valueBySubcategory = $bySubcategory->pluck('total_value', 'name');

        return [
            'total_products'      => $allProducts->count(),
            'direct_products'     => $directProducts->count(),
            'subcategory_products'=> $subProducts->count(),
            'by_subcategory'      => $bySubcategory,
            'stock_distribution'  => $stockDistribution,
            'value_by_subcategory'=> $valueBySubcategory,
        ];
    }

    // =========================================================
    // PRIVATE HELPERS
    // =========================================================

    /**
     * Walk up the ancestor chain of $descendant to check whether
     * $potential already appears in it.
     *
     * Returns true when making $descendant a child of $potential would
     * create a cycle (i.e. $potential IS $descendant or is already
     * reachable from $descendant going upward).
     */
    private function isCircular(Category $potential, Category $descendant): bool
    {
        // A category cannot be its own parent
        if ($potential->id === $descendant->id) {
            return true;
        }

        $current = $descendant;

        while ($current->parent_id !== null) {
            $current = $current->parent()->first();

            if ($current === null) {
                break;
            }

            if ($current->id === $potential->id) {
                return true;
            }
        }

        return false;
    }
}
