<?php

namespace App\Http\Controllers;

use App\Exceptions\InsufficientStockException;
use App\Http\Requests\AdjustStockRequest;
use App\Http\Requests\MergeProductsRequest;
use App\Http\Requests\RestockProductRequest;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Services\PlanService;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class ProductController extends Controller
{
    public function __construct(private ProductService $productService)
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $products = $this->productService->applyFilters($request)->paginate(10);

        foreach ($products as $product) {
            $product->stock_summary        = $product->getStockTotals();
            $product->has_multiple_batches = $product->hasMultipleBatches();
        }

        $stats = $this->productService->globalStats();

        return view('products.index', [
            'products'                    => $products,
            'totalProductsGlobal'         => $stats['total'],
            'totalStockGlobal'            => $stats['total_stock'],
            'totalValueGlobal'            => $stats['total_value'],
            'productsWithMultipleBatches' => $stats['multi_batches'],
            'totalStockFiltered'          => $products->sum('stock'),
            'totalValueFiltered'          => $products->sum(fn ($p) => ($p->sale_price ?? 0) * ($p->stock ?? 0)),
        ]);
    }

    public function search(Request $request)
    {
        return $this->index($request);
    }

    public function create()
    {
        $this->authorize('create', Product::class);

        return view('products.create', [
            'categories' => Category::sameCompany()->get(),
            'suppliers'  => Supplier::sameCompany()->get(),
        ]);
    }

    public function store(StoreProductRequest $request)
    {
        $user   = Auth::user();
        $tenant = $user->tenant;

        if (!$user->isSuperAdminGlobal() && $tenant) {
            $plan = PlanService::for($tenant);
            if (!$plan->canAddProduct(Product::where('tenant_id', $tenant->id)->count())) {
                return back()->with('upgrade', "Limite atteinte : votre plan {$plan->planLabel()} est limité à {$plan->maxProducts()} produits. Passez au plan Business pour continuer.");
            }
        }

        if (!Category::sameCompany()->find($request->category_id)) {
            return back()->with('error', 'Catégorie invalide.');
        }
        if (!Supplier::sameCompany()->find($request->supplier_id)) {
            return back()->with('error', 'Fournisseur invalide.');
        }

        try {
            $result  = $this->productService->createOrCumulate($request->validated(), $user->tenant_id, $user->id);
            $message = $result['type'] === 'restocked'
                ? 'Produit existant mis à jour : stock et prix réapprovisionnés.'
                : 'Nouveau produit ajouté avec succès.';

            return redirect()->route('products.index')->with('success', $message);
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur : ' . $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        $product = Product::with(['category', 'supplier'])->withCount('stockMovements')->findOrFail($id);

        $this->authorize('view', $product);

        $details         = $this->productService->productDetails($product);
        $recentMovements = $product->stockMovements()->orderBy('created_at', 'desc')->limit(10)->get();

        return view('products.show', [
            'product'          => $product,
            'stockByPrice'     => $details['stockByPrice'],
            'stockSummary'     => $details['stockSummary'],
            'stockConsistency' => $details['stockConsistency'],
            'recentMovements'  => $recentMovements,
        ]);
    }

    public function edit(Product $product)
    {
        $this->authorize('update', $product);

        if (Schema::hasColumn('products', 'has_been_cumulated') && $product->has_been_cumulated) {
            return redirect()->route('products.show', $product)
                ->with('warning', 'Ce produit a été cumulé et ne peut plus être modifié directement.');
        }

        return view('products.edit', [
            'product'    => $product,
            'categories' => Category::sameCompany()->get(),
            'suppliers'  => Supplier::sameCompany()->get(),
        ]);
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $this->authorize('update', $product);

        if (Schema::hasColumn('products', 'has_been_cumulated') && $product->has_been_cumulated) {
            return redirect()->route('products.show', $product)
                ->with('warning', 'Ce produit a été cumulé et ne peut plus être modifié.');
        }

        if (!Category::sameCompany()->find($request->category_id)) {
            return back()->with('error', 'Catégorie invalide.');
        }
        if (!Supplier::sameCompany()->find($request->supplier_id)) {
            return back()->with('error', 'Fournisseur invalide.');
        }

        try {
            $updated = $this->productService->update($product, $request->validated(), Auth::id());

            return redirect()->route('products.index')
                ->with('success', 'Produit mis à jour avec succès. Stock : ' . $updated->stock);
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la mise à jour : ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Product $product)
    {
        $this->authorize('delete', $product);

        $hasSales = $product->saleItems()->exists();
        $product->delete();

        $message = $hasSales
            ? "« {$product->name} » archivé (présent dans des ventes, données préservées)."
            : "Produit « {$product->name} » supprimé.";

        return redirect()->route('products.index')->with('success', $message);
    }

    // =========================================================
    // STOCK OPERATIONS
    // =========================================================

    public function adjustStock(AdjustStockRequest $request, Product $product)
    {
        $this->authorize('update', $product);

        if (Schema::hasColumn('products', 'has_been_cumulated') && $product->has_been_cumulated) {
            return redirect()->route('products.show', $product)
                ->with('warning', 'Ce produit a été cumulé et ne peut plus être modifié.');
        }

        try {
            $updated = $this->productService->adjustStock(
                $product,
                $request->adjustment_type,
                (int) $request->amount,
                (float) ($request->purchase_price ?? $product->purchase_price),
                (float) ($request->sale_price ?? $product->sale_price),
                $request->reason,
                $request->reference_document,
                Auth::id()
            );

            return redirect()->route('products.index')
                ->with('success', "Stock ajusté avec succès. Stock actuel : {$updated->stock}");
        } catch (InsufficientStockException $e) {
            return back()->with('error', $e->getMessage())->withInput();
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function restock(RestockProductRequest $request, Product $product)
    {
        $this->authorize('update', $product);

        if (Schema::hasColumn('products', 'has_been_cumulated') && $product->has_been_cumulated) {
            return redirect()->route('products.show', $product)
                ->with('warning', 'Ce produit a été cumulé et ne peut plus être réapprovisionné.');
        }

        if ($request->filled('supplier_id') && !Supplier::sameCompany()->find($request->supplier_id)) {
            return back()->with('error', 'Fournisseur invalide.');
        }

        $updated = $this->productService->restock(
            $product,
            (int) $request->amount,
            (float) ($request->purchase_price ?? $product->purchase_price),
            (float) ($request->sale_price ?? $product->sale_price),
            $request->supplier_id,
            $request->motif ?? 'Réapprovisionnement',
            $request->reference_document,
            Auth::id()
        );

        return redirect()->route('products.index')
            ->with('success', "Réapprovisionnement réussi : +{$request->amount} unités. Stock actuel : {$updated->stock}");
    }

    public function quickSale(Request $request, Product $product)
    {
        $this->authorize('view', $product);

        if (!Auth::user()->canManageSales()) {
            abort(403, 'Vous n\'avez pas les droits pour effectuer des ventes.');
        }

        if (Schema::hasColumn('products', 'has_been_cumulated') && $product->has_been_cumulated) {
            $cumulatedProduct = Product::find($product->cumulated_to);
            if ($cumulatedProduct) {
                return redirect()->route('products.show', $cumulatedProduct)
                    ->with('warning', 'Ce produit a été cumulé. Veuillez effectuer la vente sur le produit cumulé.');
            }
        }

        $request->validate([
            'quantity'    => 'required|integer|min:1',
            'client_name' => 'nullable|string|max:255',
            'reference'   => 'nullable|string|max:100',
        ]);

        try {
            $updated = $this->productService->quickSale(
                $product,
                (int) $request->quantity,
                $request->client_name ?? 'Client',
                $request->reference,
                Auth::id()
            );

            return redirect()->route('products.history', $product)
                ->with('success', "Vente enregistrée : -{$request->quantity} unités. Stock actuel : {$updated->stock}");
        } catch (InsufficientStockException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function checkStockConsistency(Product $product)
    {
        $this->authorize('view', $product);

        return response()->json($this->productService->checkConsistency($product));
    }

    public function syncStock(Product $product)
    {
        $this->authorize('update', $product);

        $consistency = $this->productService->checkConsistency($product);

        if ($consistency['is_consistent']) {
            return redirect()->route('products.show', $product)->with('info', 'Le stock est déjà cohérent.');
        }

        $result = $this->productService->syncStock($product, Auth::id());

        return redirect()->route('products.show', $product)
            ->with('success', "Stock synchronisé : {$consistency['difference']} unités corrigées. Nouveau stock : {$result['actual']}");
    }

    // =========================================================
    // CUMULATION
    // =========================================================

    public function mergeProducts(MergeProductsRequest $request)
    {
        if (!Category::sameCompany()->find($request->category_id)) {
            return back()->with('error', 'Catégorie invalide.');
        }
        if (!Supplier::sameCompany()->find($request->supplier_id)) {
            return back()->with('error', 'Fournisseur invalide.');
        }

        try {
            $merged = $this->productService->merge(
                $request->product_ids,
                $request->name,
                (int) $request->category_id,
                (int) $request->supplier_id,
                $request->batch_reference,
                Auth::id()
            );

            return redirect()->route('products.show', $merged)
                ->with('success', count($request->product_ids) . ' produits fusionnés avec succès.');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la fusion : ' . $e->getMessage())->withInput();
        }
    }

    public function uncumulateProduct(Product $product)
    {
        $this->authorize('update', $product);

        if (!Schema::hasColumn('products', 'is_cumulated') || !$product->is_cumulated) {
            return back()->with('error', 'Ce produit n\'est pas un produit cumulé.');
        }

        try {
            $this->productService->uncumulate($product, Auth::id());

            return redirect()->route('products.index')
                ->with('success', 'Cumul défait avec succès. Les produits originaux ont été restaurés.');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors du dé-cumul : ' . $e->getMessage());
        }
    }

    // =========================================================
    // HISTORY
    // =========================================================

    public function history(Product $product, Request $request)
    {
        $this->authorize('view', $product);

        $request->validate([
            'start_date' => 'nullable|date',
            'end_date'   => 'nullable|date|after_or_equal:start_date',
            'type'       => 'nullable|in:entree,sortie',
            'per_page'   => 'nullable|integer|min:5|max:100',
        ]);

        $query = $product->stockMovements()->with('user:id,name')->orderBy('created_at', 'desc');

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $movements = $query->paginate($request->get('per_page', 20));

        $totals = $product->stockMovements()
            ->selectRaw('type, SUM(quantity) as total_quantity, COUNT(*) as count')
            ->when($request->filled('start_date'), fn ($q) => $q->whereDate('created_at', '>=', $request->start_date))
            ->when($request->filled('end_date'), fn ($q) => $q->whereDate('created_at', '<=', $request->end_date))
            ->groupBy('type')
            ->get()
            ->keyBy('type');

        return view('products.history', [
            'product'     => $product,
            'movements'   => $movements,
            'totals'      => $totals,
            'stockTotals' => $product->getStockTotals(),
        ]);
    }

    public function globalHistory(Request $request)
    {
        if (!Auth::user()->canViewReports()) {
            abort(403, 'Vous n\'avez pas les droits pour voir l\'historique global.');
        }

        $request->validate([
            'product_id' => 'nullable|exists:products,id',
            'start_date' => 'nullable|date',
            'end_date'   => 'nullable|date',
            'type'       => 'nullable|in:entree,sortie',
            'search'     => 'nullable|string',
        ]);

        $query = StockMovement::with(['product:id,name', 'user:id,name'])->orderBy('created_at', 'desc');

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }
        if ($request->filled('search')) {
            $query->whereHas('product', fn ($q) => $q->where('name', 'LIKE', "%{$request->search}%"));
        }

        $movements = $query->paginate($request->get('per_page', 50));

        $stats = StockMovement::selectRaw('
            COUNT(*) as total_movements,
            SUM(CASE WHEN type = "entree" THEN quantity ELSE 0 END) as total_entrees,
            SUM(CASE WHEN type = "sortie" THEN quantity ELSE 0 END) as total_sorties,
            AVG(purchase_price) as avg_purchase_price,
            AVG(sale_price) as avg_sale_price
        ')
        ->when($request->filled('start_date'), fn ($q) => $q->whereDate('created_at', '>=', $request->start_date))
        ->when($request->filled('end_date'), fn ($q) => $q->whereDate('created_at', '<=', $request->end_date))
        ->first();

        return view('products.global-history', [
            'movements' => $movements,
            'stats'     => $stats,
            'products'  => Product::select('id', 'name')->get(),
        ]);
    }

    // =========================================================
    // REPORTS
    // =========================================================

    public function productsReport(Request $request)
    {
        if (!Auth::user()->canViewReports()) {
            abort(403, 'Vous n\'avez pas les droits pour voir les rapports.');
        }

        $data = $this->productService->reportData($request);

        return view('reports.products', $data);
    }

    public function groupedStocksReport(Request $request)
    {
        if (!Auth::user()->canViewReports()) {
            abort(403, 'Vous n\'avez pas les droits pour voir ce rapport.');
        }

        $request->validate([
            'category_id' => 'nullable|exists:categories,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'sort_by'     => 'nullable|in:name,total_value,batches_count',
        ]);

        $data = $this->productService->groupedStocksData($request);

        return view('reports.grouped-stocks', $data);
    }

    public function inventoryReport(Request $request)
    {
        if (!Auth::user()->canViewReports()) {
            abort(403, 'Vous n\'avez pas les droits pour voir les rapports.');
        }

        $data = $this->productService->inventoryData($request);

        return view('reports.inventory', $data);
    }

    public function getQuickStats()
    {
        return response()->json($this->productService->quickStats());
    }
}
