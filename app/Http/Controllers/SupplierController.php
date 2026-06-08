<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSupplierRequest;
use App\Http\Requests\UpdateSupplierRequest;
use App\Models\Supplier;
use App\Services\SupplierService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupplierController extends Controller
{
    public function __construct(private SupplierService $supplierService)
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $this->authorize('viewAny', Supplier::class);

        $suppliers = $this->supplierService->list();

        return view('suppliers.index', ['suppliers' => $suppliers]);
    }

    public function create()
    {
        $this->authorize('create', Supplier::class);

        return view('suppliers.create');
    }

    public function store(StoreSupplierRequest $request)
    {
        $this->authorize('create', Supplier::class);

        $this->supplierService->create($request->validated());

        return redirect()->route('suppliers.index')
            ->with('success', 'Fournisseur créé avec succès.');
    }

    public function show(Supplier $supplier)
    {
        $this->authorize('view', $supplier);

        $result           = $this->supplierService->stats($supplier);
        $stats            = $result;
        $lowStockProducts = $result['lowStockProducts'];

        return view('suppliers.show', compact('supplier', 'stats', 'lowStockProducts'));
    }

    public function edit(Supplier $supplier)
    {
        $this->authorize('update', $supplier);

        return view('suppliers.edit', compact('supplier'));
    }

    public function update(UpdateSupplierRequest $request, Supplier $supplier)
    {
        $this->authorize('update', $supplier);

        $this->supplierService->update($supplier, $request->validated());

        return redirect()->route('suppliers.index')
            ->with('success', 'Fournisseur mis à jour avec succès.');
    }

    public function destroy(Supplier $supplier)
    {
        $this->authorize('delete', $supplier);

        try {
            $this->supplierService->delete($supplier);

            return redirect()->route('suppliers.index')
                ->with('success', "Fournisseur \"{$supplier->name}\" supprimé avec succès.");
        } catch (\RuntimeException $e) {
            return redirect()->route('suppliers.index')
                ->with('error', $e->getMessage());
        }
    }

    public function products(Supplier $supplier)
    {
        $this->authorize('view', $supplier);

        $products = $supplier->products()->with('category')->paginate(15);

        return view('suppliers.products', compact('supplier', 'products'));
    }

    public function suppliersReport(Request $request)
    {
        if (!Auth::user()->canViewReports()) {
            abort(403, 'Vous n\'avez pas les droits pour voir les rapports.');
        }

        $data = $this->supplierService->reportData($request);

        return view('reports.suppliers', $data);
    }

    public function search(Request $request)
    {
        $this->authorize('viewAny', Supplier::class);

        $results = $this->supplierService->search($request->get('q', ''));

        return response()->json($results);
    }

    public function statistics(Supplier $supplier)
    {
        $this->authorize('view', $supplier);

        $data = $this->supplierService->statistics($supplier);

        return view('suppliers.statistics', compact('supplier') + $data);
    }

    public function export(Supplier $supplier)
    {
        $this->authorize('view', $supplier);

        $export = $this->supplierService->exportData($supplier);
        $rows   = $export['rows'];

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"fournisseur-{$supplier->id}-produits.csv\"",
            'Cache-Control'       => 'no-cache, no-store, must-revalidate',
        ];

        $callback = function () use ($rows) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM

            fputcsv($handle, ['Nom', 'Stock', 'Prix achat', 'Prix vente', 'Valeur stock', 'Categorie']);

            foreach ($rows as $row) {
                fputcsv($handle, [
                    $row['name'],
                    $row['stock'],
                    $row['purchase_price'],
                    $row['sale_price'],
                    $row['stock_value'],
                    $row['category_name'],
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function updateContact(Request $request, Supplier $supplier)
    {
        $this->authorize('update', $supplier);

        $validated = $request->validate([
            'contact' => 'nullable|string|max:255',
            'phone'   => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
        ]);

        $this->supplierService->updateContact($supplier, $validated);

        return redirect()->route('suppliers.show', $supplier)
            ->with('success', 'Informations de contact mises à jour.');
    }
}
