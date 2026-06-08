<?php

namespace App\Services;

use App\DTOs\CreateProductData;
use App\Enums\StockMovementType;
use App\Exceptions\InsufficientStockException;
use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ProductService
{
    // =========================================================
    // LISTING & FILTERS
    // =========================================================

    public function applyFilters(Request $request): \Illuminate\Database\Eloquent\Builder
    {
        $query = Product::with(['category', 'supplier']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('sale_price', 'LIKE', "%{$search}%")
                  ->orWhere('purchase_price', 'LIKE', "%{$search}%")
                  ->orWhere('stock', 'LIKE', "%{$search}%");
            });
        }

        match ($request->input('filter')) {
            'low_stock'     => $query->whereColumn('stock', '<=', 'stock_alert')->where('stock', '>', 0),
            'out_of_stock'  => $query->where('stock', 0),
            'available'     => $query->where('stock', '>', 0),
            'cumulated'     => Schema::hasColumn('products', 'is_cumulated')
                                   ? $query->where('is_cumulated', true)
                                   : $query,
            'non_cumulated' => Schema::hasColumn('products', 'has_been_cumulated')
                                   ? $query->where('has_been_cumulated', false)
                                   : $query,
            default         => null,
        };

        match ($request->input('sort_by', 'created_at')) {
            'name'          => $query->orderBy('name'),
            'stock'         => $query->orderBy('stock'),
            'sale_price'    => $query->orderBy('sale_price'),
            'profit_margin' => $query->orderByRaw('((sale_price - purchase_price) / purchase_price * 100) DESC'),
            default         => $query->orderBy('created_at', 'desc'),
        };

        return $query;
    }

    public function globalStats(): array
    {
        return [
            'total'         => Product::count(),
            'total_stock'   => Product::sum('stock'),
            'total_value'   => Product::sum(DB::raw('sale_price * stock')),
            'multi_batches' => Product::withMultipleBatches()->count(),
        ];
    }

    public function quickStats(): array
    {
        $query = Product::query();
        if (Schema::hasColumn('products', 'has_been_cumulated')) {
            $query->where('has_been_cumulated', false);
        }
        $products = $query->get();

        return [
            'total_products'            => $products->count(),
            'total_stock_value'         => $products->sum(fn ($p) => $p->purchase_price * $p->stock),
            'total_sale_value'          => $products->sum(fn ($p) => $p->sale_price * $p->stock),
            'low_stock_count'           => $products->filter(fn ($p) => $p->stock > 0 && $p->stock <= ($p->stock_alert ?? 10))->count(),
            'out_of_stock_count'        => $products->where('stock', '=', 0)->count(),
            'total_stock'               => $products->sum('stock'),
            'products_multiple_batches' => $products->filter(fn ($p) => $p->hasMultipleBatches())->count(),
            'total_batches'             => $products->sum(fn ($p) => $p->getStockTotals()['number_of_batches']),
            'cumulated_products'        => Schema::hasColumn('products', 'is_cumulated')
                                               ? Product::where('is_cumulated', true)->count()
                                               : 0,
        ];
    }

    // =========================================================
    // CREATE / UPDATE
    // =========================================================

    public function createOrCumulate(CreateProductData|array $data, int $tenantId, int $userId): array
    {
        if (is_array($data)) {
            $data = CreateProductData::fromArray($data);
        }

        $existing = Product::where('name', $data->name)
            ->where('category_id', $data->categoryId)
            ->where('supplier_id', $data->supplierId)
            ->first();

        return DB::transaction(function () use ($data, $tenantId, $userId, $existing) {
            if ($existing) {
                return $this->cumulateProduct($existing, $data, $userId);
            }
            return $this->createProduct($data, $userId);
        });
    }

    public function update(Product $product, array $validated, int $userId): Product
    {
        $oldStock = $product->stock;
        $newStock = (int) ($validated['stock'] ?? $oldStock);

        DB::transaction(function () use ($product, $validated, $oldStock, $newStock, $userId) {
            $product->update($validated);

            if ($oldStock !== $newStock) {
                $difference = $newStock - $oldStock;
                $type = $difference > 0
                    ? StockMovementType::Entry->value
                    : StockMovementType::Exit->value;

                $this->recordMovement(
                    $product,
                    $type,
                    abs($difference),
                    (float) $validated['purchase_price'],
                    (float) $validated['sale_price'],
                    'Modification manuelle du stock',
                    'EDIT-' . $product->id . '-' . time(),
                    $userId,
                    doUpdateStock: false
                );
            }
        });

        return $product->fresh();
    }

    // =========================================================
    // STOCK OPERATIONS
    // =========================================================

    public function adjustStock(
        Product $product,
        string  $adjustmentType,
        int     $amount,
        float   $purchasePrice,
        float   $salePrice,
        ?string $reason,
        ?string $reference,
        int     $userId
    ): Product {
        DB::transaction(function () use ($product, $adjustmentType, $amount, $purchasePrice, $salePrice, $reason, $reference, $userId) {
            $oldStock = $product->stock;

            match ($adjustmentType) {
                'add' => $this->recordMovement(
                    $product, StockMovementType::Entry->value, $amount,
                    $purchasePrice, $salePrice,
                    'Ajustement positif : ' . ($reason ?? ''), $reference ?? '', $userId
                ),
                'remove' => $this->recordMovement(
                    $product, StockMovementType::Exit->value, $amount,
                    $purchasePrice, $salePrice,
                    'Ajustement négatif : ' . ($reason ?? ''), $reference ?? '', $userId
                ),
                'set' => (function () use ($product, $amount, $oldStock, $purchasePrice, $salePrice, $reason, $reference, $userId) {
                    $difference = $amount - $oldStock;
                    if ($difference === 0) {
                        return;
                    }
                    $type = $difference > 0 ? StockMovementType::Entry->value : StockMovementType::Exit->value;
                    $this->recordMovement(
                        $product, $type, abs($difference),
                        $purchasePrice, $salePrice,
                        'Ajustement (définition stock) : ' . ($reason ?? ''), $reference ?? '', $userId
                    );
                })(),
                default => null,
            };
        });

        return $product->fresh();
    }

    public function restock(
        Product  $product,
        int      $amount,
        float    $purchasePrice,
        float    $salePrice,
        ?int     $supplierId,
        string   $motif,
        ?string  $reference,
        int      $userId
    ): Product {
        DB::transaction(function () use ($product, $amount, $purchasePrice, $salePrice, $supplierId, $motif, $reference, $userId) {
            $this->recordMovement(
                $product, StockMovementType::Entry->value, $amount,
                $purchasePrice, $salePrice, $motif, $reference ?? '', $userId
            );

            $updates = [];
            if ($purchasePrice !== (float) $product->purchase_price) {
                $updates['purchase_price'] = $purchasePrice;
            }
            if ($salePrice !== (float) $product->sale_price) {
                $updates['sale_price'] = $salePrice;
            }
            if ($supplierId) {
                $updates['supplier_id'] = $supplierId;
            }
            if ($updates) {
                $product->update($updates);
            }
        });

        return $product->fresh();
    }

    public function quickSale(Product $product, int $quantity, string $clientName, ?string $reference, int $userId): Product
    {
        DB::transaction(fn () => $this->recordMovement(
            $product, StockMovementType::Exit->value, $quantity,
            $product->purchase_price, $product->sale_price,
            'Vente à ' . $clientName, $reference ?? '', $userId
        ));

        return $product->fresh();
    }

    // =========================================================
    // CUMULATION / MERGE
    // =========================================================

    public function merge(
        array   $productIds,
        string  $name,
        int     $categoryId,
        int     $supplierId,
        ?string $batchReference,
        int     $userId
    ): Product {
        return DB::transaction(function () use ($productIds, $name, $categoryId, $supplierId, $batchReference, $userId) {
            $query = Product::whereIn('id', $productIds);
            if (Schema::hasColumn('products', 'has_been_cumulated')) {
                $query->where('has_been_cumulated', false);
            }
            $products = $query->get();

            if ($products->count() < 2) {
                throw new \RuntimeException('Sélectionnez au moins 2 produits non-cumulés à fusionner.');
            }

            $mergedData = [
                'name'           => $name,
                'stock'          => $products->sum('stock'),
                'quantity'       => $products->sum('quantity'),
                'purchase_price' => round($products->avg('purchase_price'), 2),
                'sale_price'     => round($products->avg('sale_price'), 2),
                'description'    => 'Produit fusionné de ' . $products->count() . ' produits',
                'category_id'    => $categoryId,
                'supplier_id'    => $supplierId,
                'batch_number'   => $batchReference ?? ('MERGE-' . time()),
            ];
            if (Schema::hasColumn('products', 'is_cumulated')) {
                $mergedData['is_cumulated'] = true;
            }

            $merged = Product::create($mergedData);

            foreach ($products as $product) {
                if ($product->stock > 0) {
                    $this->recordMovement($product, StockMovementType::Exit->value, $product->stock, $product->purchase_price, $product->sale_price, 'Transfert vers produit fusionné', 'MERGE-' . $merged->id, $userId);
                    $this->recordMovement($merged, StockMovementType::Entry->value, $product->stock, $product->purchase_price, $product->sale_price, 'Ajout depuis ' . $product->name, 'FROM-' . $product->id, $userId, doUpdateStock: false);
                }

                $updateData = ['stock' => 0, 'quantity' => 0];
                if (Schema::hasColumn('products', 'has_been_cumulated')) {
                    $updateData['has_been_cumulated'] = true;
                }
                if (Schema::hasColumn('products', 'cumulated_to')) {
                    $updateData['cumulated_to'] = $merged->id;
                }
                $product->update($updateData);
            }

            return $merged;
        });
    }

    public function uncumulate(Product $product, int $userId): void
    {
        DB::transaction(function () use ($product, $userId) {
            $originalProducts = collect();
            if (Schema::hasColumn('products', 'cumulated_to')) {
                $originalProducts = Product::where('cumulated_to', $product->id)->get();
            }
            if ($originalProducts->isEmpty() && Schema::hasColumn('products', 'parent_id')) {
                $originalProducts = Product::where('parent_id', $product->id)->get();
            }
            if ($originalProducts->isEmpty()) {
                throw new \RuntimeException('Aucun produit original trouvé pour ce cumul.');
            }

            foreach ($originalProducts as $original) {
                $originalStock = $original->getOriginal('stock') ?? 0;

                $updateData = ['stock' => $originalStock];
                if (Schema::hasColumn('products', 'has_been_cumulated')) {
                    $updateData['has_been_cumulated'] = false;
                }
                if (Schema::hasColumn('products', 'cumulated_to')) {
                    $updateData['cumulated_to'] = null;
                }
                $original->update($updateData);

                if ($originalStock > 0) {
                    $this->recordMovement($original, StockMovementType::Entry->value, $originalStock, $original->purchase_price, $original->sale_price, 'Restauration après dé-cumul', 'UNCUMUL-' . $product->id, $userId, doUpdateStock: false);
                }
            }

            $product->delete();
        });
    }

    // =========================================================
    // STOCK CONSISTENCY
    // =========================================================

    public function checkConsistency(Product $product): array
    {
        $totalEntries = $product->stockMovements()->entries()->sum('quantity');
        $totalExits   = $product->stockMovements()->exits()->sum('quantity');
        $calculated   = $totalEntries - $totalExits;

        return [
            'calculated'    => $calculated,
            'actual'        => $product->stock,
            'difference'    => $product->stock - $calculated,
            'is_consistent' => $product->stock == $calculated,
        ];
    }

    public function syncStock(Product $product, int $userId): array
    {
        $consistency = $this->checkConsistency($product);

        if (!$consistency['is_consistent']) {
            $difference = $consistency['calculated'] - $product->stock;
            $type = $difference > 0 ? StockMovementType::Entry->value : StockMovementType::Exit->value;

            DB::transaction(fn () => $this->recordMovement(
                $product, $type, abs($difference),
                $product->purchase_price, $product->sale_price,
                'Correction automatique de stock', 'SYNC-' . $product->id, $userId
            ));
        }

        return $this->checkConsistency($product->fresh());
    }

    // =========================================================
    // REPORTS
    // =========================================================

    public function productDetails(Product $product): array
    {
        $stockByPrice = DB::table('stock_movements')
            ->select('purchase_price', 'reference_document', DB::raw('SUM(quantity) as total_quantity'), DB::raw('MAX(created_at) as last_update'))
            ->where('product_id', $product->id)
            ->where('type', StockMovementType::Entry->value)
            ->whereNotNull('purchase_price')
            ->groupBy('purchase_price', 'reference_document')
            ->having('total_quantity', '>', 0)
            ->get();

        $totalStock        = $product->stock ?? 0;
        $totalValuePurch   = $stockByPrice->sum(fn ($b) => $b->total_quantity * $b->purchase_price);
        $avgPurchasePrice  = $totalStock > 0 ? $totalValuePurch / $totalStock : 0;

        return [
            'stockByPrice'     => $stockByPrice,
            'stockSummary'     => [
                'total_stock'            => $totalStock,
                'total_value'            => $totalStock * $product->sale_price,
                'average_purchase_price' => $avgPurchasePrice,
                'profit_potential'       => ($totalStock * $product->sale_price) - $totalValuePurch,
                'batches_count'          => $stockByPrice->count(),
                'has_multiple_batches'   => $stockByPrice->count() > 1,
                'total_value_purchase'   => $totalValuePurch,
            ],
            'stockConsistency' => $this->checkConsistency($product),
        ];
    }

    public function reportData(Request $request): array
    {
        $query = Product::query();
        if (Schema::hasColumn('products', 'has_been_cumulated')) {
            $query->where('has_been_cumulated', false);
        }
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        $products = $query->with(['category', 'supplier'])->orderBy('stock', 'asc')->get();
        foreach ($products as $product) {
            $product->stock_totals = $product->getStockTotals();
        }

        $cumulatedCount = Schema::hasColumn('products', 'is_cumulated')
            ? Product::where('is_cumulated', true)->count()
            : 0;

        return [
            'products'   => $products,
            'reportData' => [
                'total_products'            => $products->count(),
                'total_stock_value'         => $products->sum(fn ($p) => $p->stock * $p->purchase_price),
                'total_sale_value'          => $products->sum(fn ($p) => $p->stock * $p->sale_price),
                'low_stock'                 => $products->filter(fn ($p) => $p->stock > 0 && $p->stock <= ($p->stock_alert ?? 10))->count(),
                'out_of_stock'              => $products->where('stock', 0)->count(),
                'total_purchased'           => $products->sum('stock'),
                'products_multiple_batches' => $products->filter(fn ($p) => $p->hasMultipleBatches())->count(),
                'total_batches'             => $products->sum(fn ($p) => $p->getStockTotals()['number_of_batches']),
                'cumulated_products'        => $cumulatedCount,
            ],
            'categories' => Category::sameCompany()->get(),
            'suppliers'  => Supplier::sameCompany()->get(),
        ];
    }

    public function groupedStocksData(Request $request): array
    {
        $query = Product::query();
        if (Schema::hasColumn('products', 'has_been_cumulated')) {
            $query->where('has_been_cumulated', false);
        }
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        match ($request->get('sort_by', 'name')) {
            'total_value' => $query->orderByRaw('(sale_price * stock) DESC'),
            default       => $query->orderBy('name', 'asc'),
        };

        $products        = $query->get();
        $productsData    = [];
        $totalGlobalValue = 0;
        $totalBatches    = 0;

        foreach ($products as $product) {
            $stockTotals  = $product->getStockTotals();
            $summary      = $product->getStockSummary();
            $productsData[] = [
                'product'        => $product,
                'summary'        => $summary,
                'totals'         => $stockTotals,
                'grouped_stocks' => $stockTotals['grouped_stocks'],
            ];
            $totalGlobalValue += $summary['total_value'];
            $totalBatches     += $summary['batches_count'];
        }

        if ($request->get('sort_by') === 'batches_count') {
            usort($productsData, fn ($a, $b) => $b['summary']['batches_count'] <=> $a['summary']['batches_count']);
        }

        $cumulatedProducts = Schema::hasColumn('products', 'is_cumulated')
            ? Product::where('is_cumulated', true)->with('stockMovements')->get()
            : collect();

        return [
            'productsData'      => $productsData,
            'cumulatedProducts' => $cumulatedProducts,
            'reportStats'       => [
                'total_products'                => count($productsData),
                'total_cumulated_products'      => $cumulatedProducts->count(),
                'total_value'                   => $totalGlobalValue,
                'total_batches'                 => $totalBatches,
                'products_with_multiple_batches' => collect($productsData)->filter(fn ($p) => $p['summary']['has_multiple_batches'])->count(),
                'average_batches_per_product'   => count($productsData) > 0 ? round($totalBatches / count($productsData), 1) : 0,
            ],
            'categories'        => Category::sameCompany()->get(),
            'suppliers'         => Supplier::sameCompany()->get(),
        ];
    }

    public function inventoryData(Request $request): array
    {
        $query = Product::with(['category', 'supplier']);

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }
        if ($request->filled('stock_status')) {
            match ($request->stock_status) {
                'low' => $query->whereColumn('stock', '<=', 'stock_alert')->where('stock', '>', 0),
                'out' => $query->where('stock', 0),
                'in'  => $query->where('stock', '>', 0),
                default => null,
            };
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(fn ($q) => $q->where('name', 'LIKE', "%{$search}%")->orWhere('id', 'LIKE', "%{$search}%"));
        }

        $products    = $query->orderBy('name')->paginate(20);
        $statsQuery  = Product::query();

        if ($request->filled('category_id')) {
            $statsQuery->where('category_id', $request->category_id);
        }
        if ($request->filled('supplier_id')) {
            $statsQuery->where('supplier_id', $request->supplier_id);
        }

        $allForStats = $statsQuery->get();

        return [
            'products'   => $products,
            'stats'      => [
                'total_products'    => $statsQuery->count(),
                'total_stock'       => $statsQuery->sum('stock'),
                'total_value'       => $allForStats->sum(fn ($p) => ($p->purchase_price ?? 0) * ($p->stock ?? 0)),
                'total_sale_value'  => $allForStats->sum(fn ($p) => ($p->sale_price ?? 0) * ($p->stock ?? 0)),
                'low_stock_count'   => (clone $statsQuery)->whereColumn('stock', '<=', 'stock_alert')->where('stock', '>', 0)->count(),
                'out_of_stock_count'=> (clone $statsQuery)->where('stock', 0)->count(),
                'categories_count'  => $products->pluck('category_id')->unique()->count(),
                'suppliers_count'   => $products->pluck('supplier_id')->unique()->count(),
            ],
            'categories' => Category::sameCompany()->get(),
            'suppliers'  => Supplier::sameCompany()->get(),
        ];
    }

    // =========================================================
    // CORE MOVEMENT RECORDER
    // =========================================================

    public function recordMovement(
        Product $product,
        string  $type,
        int     $quantity,
        float   $purchasePrice,
        float   $salePrice,
        string  $motif        = '',
        string  $reference    = '',
        int     $userId       = 0,
        bool    $doUpdateStock = true
    ): StockMovement {
        if ($type === StockMovementType::Exit->value && $product->stock < $quantity) {
            throw new InsufficientStockException($product->name, $product->stock, $quantity);
        }

        $stockAfter = $type === StockMovementType::Entry->value
            ? $product->stock + $quantity
            : $product->stock - $quantity;

        $movement = StockMovement::create([
            'product_id'         => $product->id,
            'type'               => $type,
            'quantity'           => $quantity,
            'purchase_price'     => $purchasePrice,
            'sale_price'         => $salePrice,
            'stock_after'        => $doUpdateStock ? $stockAfter : $product->stock,
            'motif'              => $motif,
            'reference_document' => $reference,
            'user_id'            => $userId ?: auth()->id(),
            'tenant_id'          => $product->tenant_id,
        ]);

        if ($doUpdateStock) {
            $product->update(['stock' => $stockAfter, 'quantity' => $stockAfter]);
        }

        return $movement;
    }

    // =========================================================
    // PRIVATE HELPERS
    // =========================================================

    private function createProduct(CreateProductData $data, int $userId): array
    {
        $productData = [
            'name'           => $data->name,
            'stock'          => $data->stock,
            'quantity'       => $data->stock,
            'purchase_price' => $data->purchasePrice,
            'sale_price'     => $data->salePrice,
            'description'    => $data->description,
            'category_id'    => $data->categoryId,
            'supplier_id'    => $data->supplierId,
            'stock_alert'    => $data->stockAlert ?? 5,
        ];

        if (Schema::hasColumn('products', 'is_cumulated')) {
            $productData['is_cumulated'] = false;
        }

        $product = Product::create($productData);

        if ($data->stock > 0) {
            $this->recordMovement(
                $product, StockMovementType::Entry->value, $data->stock,
                $data->purchasePrice, $data->salePrice,
                'Stock initial', 'INITIAL-' . $product->id, $userId, doUpdateStock: false
            );
        }

        return ['type' => 'created', 'product' => $product];
    }

    private function cumulateProduct(Product $existing, CreateProductData $data, int $userId): array
    {
        $oldStock   = $existing->stock;
        $addedStock = $data->stock;
        $newStock   = $oldStock + $addedStock;

        $avgPurchase = $newStock > 0
            ? (($oldStock * $existing->purchase_price) + ($addedStock * $data->purchasePrice)) / $newStock
            : $data->purchasePrice;

        $updateData = [
            'stock'          => $newStock,
            'quantity'       => $newStock,
            'purchase_price' => round($avgPurchase, 2),
            'sale_price'     => $data->salePrice,
            'description'    => $data->description ?? $existing->description,
        ];
        if (Schema::hasColumn('products', 'is_cumulated')) {
            $updateData['is_cumulated'] = false;
        }

        $existing->update($updateData);

        if ($addedStock > 0) {
            $this->recordMovement(
                $existing, StockMovementType::Entry->value, $addedStock,
                $data->purchasePrice, $data->salePrice,
                'Réapprovisionnement', 'REAPPRO-' . time(),
                $userId, doUpdateStock: false
            );
        }

        return ['type' => 'restocked', 'product' => $existing];
    }
}
