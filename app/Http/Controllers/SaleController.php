<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use App\Models\Client;
use App\Models\User;
use App\Services\SaleService;
use App\Http\Requests\StoreSaleRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SaleController extends Controller
{
    public function __construct(private SaleService $saleService)
    {
        $this->middleware('auth');
    }

    // ----------------------
    // Liste des ventes
    // ----------------------
    public function index(Request $request)
    {
        $tenantId = Auth::user()->tenant_id;

        $query = Sale::with(['items', 'client:id,name', 'user:id,name'])
                     ->withSum('items', 'quantity')
                     ->where('tenant_id', $tenantId);

        if ($request->filled('client_id'))   $query->where('client_id', $request->client_id);
        if ($request->filled('user_id'))     $query->where('user_id', $request->user_id);
        if ($request->filled('date_from'))   $query->whereDate('created_at', '>=', $request->date_from);
        if ($request->filled('date_to'))     $query->whereDate('created_at', '<=', $request->date_to);
        if ($request->filled('min_amount'))  $query->where('total_price', '>=', $request->min_amount);
        if ($request->filled('max_amount'))  $query->where('total_price', '<=', $request->max_amount);

        $sales   = $query->latest()->paginate(10);
        $clients = Client::where('tenant_id', $tenantId)->get(['id', 'name']);
        $users   = User::where('tenant_id', $tenantId)->where('role', '!=', 'super_admin_global')->get(['id', 'name']);

        return view('sales.index', compact('sales', 'clients', 'users'));
    }

    // ----------------------
    // Formulaire de création
    // ----------------------
    public function create()
    {
        $this->authorize('create', Sale::class);

        $tenantId = Auth::user()->tenant_id;
        $products = Product::where('stock', '>', 0)->where('tenant_id', $tenantId)->orderBy('name')->get();
        $clients  = Client::where('tenant_id', $tenantId)->get(['id', 'name']);

        return view('sales.create', compact('products', 'clients'));
    }

    // ----------------------
    // Enregistrer une vente
    // ----------------------
    public function store(StoreSaleRequest $request)
    {
        $tenantId = Auth::user()->tenant_id;

        if ($request->client_id) {
            $client = Client::where('tenant_id', $tenantId)->find($request->client_id);
            if (!$client) {
                return back()->with('error', 'Client invalide.')->withInput();
            }
        }

        try {
            $this->saleService->create($request->validated(), $tenantId, Auth::id());
            return redirect()->route('sales.index')->with('success', 'Vente enregistrée avec succès.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    // ----------------------
    // Afficher une vente
    // ----------------------
    public function show($id)
    {
        $tenantId = Auth::user()->tenant_id;
        $sale = Sale::with(['items.product', 'client', 'user'])
                    ->where('tenant_id', $tenantId)
                    ->findOrFail($id);

        return view('sales.show', [
            'sale'          => $sale,
            'totalQuantity' => $sale->items->sum('quantity'),
        ]);
    }

    // ----------------------
    // ANNULER une vente (DELETE /sales/{sale})
    // ----------------------
    public function destroy(Sale $sale)
    {
        $this->authorize('cancel', $sale);

        $sale->loadMissing('items');

        try {
            $this->saleService->cancel($sale, Auth::id());
            return redirect()->route('sales.index')->with('success', 'Vente annulée et stock restauré.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // ----------------------
    // ANNULER une vente (POST /sales/{id}/cancel — route legacy)
    // ----------------------
    public function cancel($id)
    {
        $tenantId = Auth::user()->tenant_id;

        $sale = Sale::with('items')->where('tenant_id', $tenantId)->findOrFail($id);

        $this->authorize('cancel', $sale);

        try {
            $this->saleService->cancel($sale, Auth::id());
            return redirect()->route('sales.index')->with('success', 'Vente annulée et stock restauré.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // ----------------------
    // Générer une facture
    // ----------------------
    public function invoice($id)
    {
        $tenantId = Auth::user()->tenant_id;
        $sale = Sale::with(['items.product', 'client', 'user'])
                    ->where('tenant_id', $tenantId)
                    ->findOrFail($id);

        $tenant = Auth::user()->tenant;

        // Embed logo as base64 so it renders correctly in any environment
        // (no symlink dependency, works for print and PDF export too)
        $logoBase64 = null;
        if ($tenant?->logo) {
            $path = storage_path('app/public/' . $tenant->logo);
            if (file_exists($path)) {
                $mime = mime_content_type($path);
                $logoBase64 = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($path));
            }
        }

        return view('sales.invoice', [
            'sale'          => $sale,
            'totalQuantity' => $sale->items->sum('quantity'),
            'tenant'        => $tenant,
            'logoBase64'    => $logoBase64,
        ]);
    }

    // ----------------------
    // API pour les statistiques
    // ----------------------
    public function getStats()
    {
        $tenantId = Auth::user()->tenant_id;

        return response()->json($this->saleService->getStats($tenantId));
    }

    // ----------------------
    // Rapport des ventes
    // ----------------------
    public function salesReport(Request $request)
    {
        $userRole = Auth::user()->role;
        $reportRoles = ['super_admin_global', 'super_admin', 'admin', 'manager'];

        if (!in_array($userRole, $reportRoles)) {
            abort(403, 'Vous n\'avez pas les droits pour voir les rapports.');
        }

        $request->validate([
            'start_date' => 'nullable|date',
            'end_date'   => 'nullable|date|after_or_equal:start_date',
        ]);

        $tenantId = Auth::user()->tenant_id;

        $data = $this->saleService->reportData($request, $tenantId);

        return view('reports.sales', [
            'sales'      => $data['sales'],
            'reportData' => $data['reportData'],
            'clients'    => $data['clients'],
            'users'      => $data['users'],
        ]);
    }
}
