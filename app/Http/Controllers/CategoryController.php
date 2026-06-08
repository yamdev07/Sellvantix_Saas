<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use App\Models\Product;
use App\Services\CategoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    public function __construct(private CategoryService $categoryService)
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $this->authorize('viewAny', Category::class);

        return view('categories.index', $this->categoryService->index(Auth::user()->tenant_id));
    }

    public function show($id)
    {
        $category = Category::findOrFail($id);
        $this->authorize('view', $category);

        return view('categories.show', $this->categoryService->show($category, Auth::user()->tenant_id));
    }

    public function create()
    {
        $this->authorize('create', Category::class);

        $mainCategories = $this->tenantRootCategories();

        return view('categories.create', compact('mainCategories'));
    }

    public function store(StoreCategoryRequest $request)
    {
        $this->authorize('create', Category::class);

        $user = Auth::user();

        try {
            $this->categoryService->create($request->validated(), $user->tenant_id, $user->id);

            return redirect()->route('categories.index')
                ->with('success', $request->parent_id
                    ? 'Sous-catégorie créée avec succès.'
                    : 'Catégorie principale créée avec succès.'
                );
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function edit($id)
    {
        $category = Category::findOrFail($id);
        $this->authorize('update', $category);

        $mainCategories = $this->tenantRootCategories();

        return view('categories.edit', compact('category', 'mainCategories'));
    }

    public function update(UpdateCategoryRequest $request, $id)
    {
        $category = Category::findOrFail($id);
        $this->authorize('update', $category);

        try {
            $this->categoryService->update($category, $request->validated(), Auth::user()->tenant_id);

            return redirect()->route('categories.index')
                ->with('success', 'Catégorie mise à jour avec succès.');
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $this->authorize('delete', $category);

        try {
            $this->categoryService->delete($category);

            return redirect()->route('categories.index')
                ->with('success', 'Catégorie supprimée avec succès.');
        } catch (\RuntimeException $e) {
            return redirect()->route('categories.index')
                ->with('warning', $e->getMessage());
        }
    }

    public function destroyWithTransfer(Request $request, $id)
    {
        $request->validate([
            'new_category_id'    => 'required|exists:categories,id|different:' . $id,
            'move_subcategories' => 'sometimes|boolean',
        ]);

        $category = Category::findOrFail($id);
        $this->authorize('delete', $category);

        $this->categoryService->deleteWithTransfer(
            $category,
            (int) $request->new_category_id,
            (bool) $request->move_subcategories
        );

        return redirect()->route('categories.index')
            ->with('success', 'Catégorie supprimée et produits transférés avec succès.');
    }

    public function addProduct(Request $request, $id)
    {
        $category = Category::findOrFail($id);
        $this->authorize('update', $category);

        $request->validate(['product_id' => 'required|exists:products,id']);

        $this->categoryService->addProduct($category, (int) $request->product_id, Auth::user()->tenant_id);

        return redirect()->route('categories.show', $id)
            ->with('success', 'Produit ajouté à la catégorie avec succès.');
    }

    public function transferProduct(Request $request, $id, $productId)
    {
        $category = Category::findOrFail($id);
        $this->authorize('update', $category);

        $request->validate(['target_category_id' => 'required|exists:categories,id|different:' . $id]);

        $target = Category::findOrFail($request->target_category_id);

        $this->categoryService->transferProduct(
            (int) $productId,
            $category,
            $target,
            Auth::user()->tenant_id
        );

        return redirect()->route('categories.show', $id)
            ->with('success', 'Produit transféré avec succès.');
    }

    public function products($id)
    {
        $category = Category::findOrFail($id);
        $this->authorize('view', $category);

        $allProducts = $category->getAllProducts();

        return view('categories.products', compact('category', 'allProducts'));
    }

    public function detailedStats($id)
    {
        $category = Category::findOrFail($id);
        $this->authorize('view', $category);

        return response()->json($this->categoryService->detailedStats($category));
    }

    // -------------------------------------------------------------------------

    private function tenantRootCategories()
    {
        return Category::where('tenant_id', Auth::user()->tenant_id)
            ->whereNull('parent_id')
            ->orderBy('name')
            ->get();
    }
}
