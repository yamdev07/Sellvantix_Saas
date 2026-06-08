<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplierService
{
    // =========================================================
    // LISTING
    // =========================================================

    /**
     * Paginate all suppliers with product count and computed stock value.
     */
    public function list(): \Illuminate\Pagination\LengthAwarePaginator
    {
        $paginator = Supplier::withCount('products')
            ->latest()
            ->paginate(10);

        foreach ($paginator as $supplier) {
            $supplier->stock_value = $supplier->products()
                ->selectRaw('SUM(stock * purchase_price) as total')
                ->value('total') ?? 0;
        }

        return $paginator;
    }

    // =========================================================
    // STATS
    // =========================================================

    /**
     * Return key statistics for a single supplier.
     */
    public function stats(Supplier $supplier): array
    {
        $products = $supplier->products()->with('category')->get();

        $totalProducts  = $products->count();
        $totalStock     = $products->sum('stock');
        $totalValue     = $products->sum(fn ($p) => $p->stock * $p->purchase_price);
        $totalSaleValue = $products->sum(fn ($p) => $p->stock * $p->sale_price);
        $potentialProfit = $totalSaleValue - $totalValue;
        $outOfStock     = $products->where('stock', 0)->count();
        $lowStock       = $products->filter(fn ($p) => $p->stock > 0 && $p->stock <= 5)->count();

        $lowStockProducts = $supplier->products()
            ->where('stock', '>', 0)
            ->where('stock', '<=', 5)
            ->orderBy('stock', 'asc')
            ->limit(5)
            ->get();

        return [
            'total_products'   => $totalProducts,
            'total_stock'      => $totalStock,
            'total_value'      => $totalValue,
            'total_sale_value' => $totalSaleValue,
            'potential_profit' => $potentialProfit,
            'out_of_stock'     => $outOfStock,
            'low_stock'        => $lowStock,
            'lowStockProducts' => $lowStockProducts,
        ];
    }

    // =========================================================
    // CRUD
    // =========================================================

    /**
     * Create a new supplier.
     */
    public function create(array $data): Supplier
    {
        return Supplier::create($data);
    }

    /**
     * Update an existing supplier and return its refreshed state.
     */
    public function update(Supplier $supplier, array $data): Supplier
    {
        $supplier->update($data);

        return $supplier->fresh();
    }

    /**
     * Delete a supplier, throwing if it still has associated products.
     *
     * @throws \RuntimeException
     */
    public function delete(Supplier $supplier): void
    {
        if ($supplier->products()->exists()) {
            throw new \RuntimeException(
                "Impossible de supprimer le fournisseur \"{$supplier->name}\" car il possède des produits associés."
            );
        }

        $supplier->delete();
    }

    // =========================================================
    // REPORTS
    // =========================================================

    /**
     * Build report data for all suppliers, with optional filters.
     */
    public function reportData(Request $request): array
    {
        $query = Supplier::withCount('products');

        if ($request->filled('has_products')) {
            match ($request->input('has_products')) {
                'yes'   => $query->has('products', '>', 0),
                'no'    => $query->has('products', '=', 0),
                default => null,
            };
        }

        if ($request->filled('search')) {
            $term = $request->input('search');
            $query->where(function ($q) use ($term) {
                $q->where('name', 'LIKE', "%{$term}%");
            });
        }

        $suppliers = $query->latest()->get();

        $totalSuppliers           = $suppliers->count();
        $suppliersWithProducts    = $suppliers->where('products_count', '>', 0)->count();
        $suppliersWithoutProducts = $suppliers->where('products_count', 0)->count();
        $avgProductsPerSupplier   = $totalSuppliers > 0
            ? round($suppliers->avg('products_count'), 2)
            : 0;

        $supplierIds     = $suppliers->pluck('id');
        $totalProducts   = Product::whereIn('supplier_id', $supplierIds)->count();
        $totalStockValue = Product::whereIn('supplier_id', $supplierIds)
            ->sum(DB::raw('stock * purchase_price'));

        return [
            'suppliers'  => $suppliers,
            'reportData' => [
                'total_suppliers'             => $totalSuppliers,
                'suppliers_with_products'     => $suppliersWithProducts,
                'suppliers_without_products'  => $suppliersWithoutProducts,
                'average_products_per_supplier' => $avgProductsPerSupplier,
                'total_products'              => $totalProducts,
                'total_stock_value'           => $totalStockValue,
            ],
        ];
    }

    // =========================================================
    // SEARCH
    // =========================================================

    /**
     * Full-text search on name, contact and phone — at most 10 results.
     */
    public function search(string $term): \Illuminate\Database\Eloquent\Collection
    {
        return Supplier::where(function ($q) use ($term) {
                $q->where('name',    'LIKE', "%{$term}%")
                  ->orWhere('contact', 'LIKE', "%{$term}%")
                  ->orWhere('phone',   'LIKE', "%{$term}%");
            })
            ->limit(10)
            ->get();
    }

    // =========================================================
    // STATISTICS
    // =========================================================

    /**
     * Return per-category breakdown and stock-level distribution for a supplier.
     */
    public function statistics(Supplier $supplier): array
    {
        $products = $supplier->products()->with('category')->get();

        // Group by category
        $productsByCategory = $products
            ->groupBy(fn ($p) => $p->category?->name ?? 'Sans catégorie')
            ->map(fn ($group) => [
                'count'       => $group->count(),
                'total_stock' => $group->sum('stock'),
                'total_value' => $group->sum(fn ($p) => $p->stock * $p->purchase_price),
            ])
            ->toArray();

        // Stock distribution
        $outOfStock = $products->where('stock', 0)->count();
        $lowStock   = $products->filter(fn ($p) => $p->stock > 0 && $p->stock <= 5)->count();
        $medium     = $products->filter(fn ($p) => $p->stock > 5 && $p->stock <= 20)->count();
        $high       = $products->filter(fn ($p) => $p->stock > 20)->count();

        $stockDistribution = [
            'out'    => $outOfStock,
            'low'    => $lowStock,
            'medium' => $medium,
            'high'   => $high,
        ];

        return [
            'productsByCategory' => $productsByCategory,
            'stockDistribution'  => $stockDistribution,
        ];
    }

    // =========================================================
    // EXPORT
    // =========================================================

    /**
     * Prepare flat export data for a supplier's products.
     */
    public function exportData(Supplier $supplier): array
    {
        $supplier->load(['products.category']);

        $rows = $supplier->products->map(fn ($product) => [
            'name'           => $product->name,
            'stock'          => $product->stock,
            'purchase_price' => $product->purchase_price,
            'sale_price'     => $product->sale_price,
            'stock_value'    => $product->stock * $product->purchase_price,
            'category_name'  => $product->category?->name ?? 'Sans catégorie',
        ])->values()->toArray();

        return [
            'supplier' => $supplier,
            'rows'     => $rows,
        ];
    }

    // =========================================================
    // CONTACT UPDATE
    // =========================================================

    /**
     * Update only the contact, phone and address fields (skips blank values).
     */
    public function updateContact(Supplier $supplier, array $data): Supplier
    {
        $filtered = array_filter(
            array_intersect_key($data, array_flip(['contact', 'phone', 'address'])),
            fn ($value) => $value !== null && $value !== ''
        );

        if (!empty($filtered)) {
            $supplier->update($filtered);
        }

        return $supplier->fresh();
    }
}
