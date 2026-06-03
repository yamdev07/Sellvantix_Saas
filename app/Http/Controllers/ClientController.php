<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ClientController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Vérifier que le client appartient à la quincaillerie
     */
    private function authorizeClientAccess(Client $client)
    {
        if (!Auth::user()->hasAccessTo($client)) {
            abort(403, 'Vous n\'avez pas accès à ce client.');
        }
    }

    /**
     * Vérifier les permissions de gestion des ventes (pour voir les clients)
     */
    private function authorizeSalesAccess()
    {
        if (!Auth::user()->canManageSales()) {
            abort(403, 'Vous n\'avez pas les droits pour gérer les clients.');
        }
    }

    /**
     * Vérifier les permissions d'administration (pour créer/modifier/supprimer)
     */
    private function authorizeAdmin()
    {
        if (!Auth::user()->isSuperAdminOrAdmin()) {
            abort(403, 'Action réservée aux administrateurs.');
        }
    }

    /**
     * Afficher la liste des clients
     */
    public function index()
    {
        $this->authorizeSalesAccess();
        
        $clients = Client::withCount('sales')
                        ->withSum('sales', 'total_price')
                        ->withMax('sales', 'created_at')
                        ->latest()
                        ->paginate(10);

        return view('clients.index', compact('clients'));
    }

    /**
     * Formulaire de création
     */
    public function create()
    {
        $this->authorizeSalesAccess();

        return view('clients.create');
    }

    /**
     * Enregistrer un nouveau client
     */
    public function store(Request $request)
    {
        $this->authorizeSalesAccess();

        $tenantId = Auth::user()->tenant_id;

        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => [
                'nullable', 'email', 'max:255',
                Rule::unique('clients', 'email')->where('tenant_id', $tenantId),
            ],
            'phone' => 'nullable|string|max:20',
        ], [
            'email.unique' => 'Un client avec cet email existe déjà dans votre boutique.',
        ]);

        Client::create($request->only(['name', 'email', 'phone']));

        return redirect()->route('clients.index')
                         ->with('success', 'Client ajouté avec succès ✅');
    }

    /**
     * Création rapide d'un client (AJAX) depuis le formulaire de vente.
     * Accessible à tous ceux qui peuvent gérer les ventes (dont les caissiers).
     */
    public function quickStore(Request $request)
    {
        $this->authorizeSalesAccess();

        $tenantId = Auth::user()->tenant_id;

        // Convertir les chaînes vides en null pour que les règles "nullable" s'appliquent
        $request->merge([
            'email' => $request->filled('email') ? $request->email : null,
            'phone' => $request->filled('phone') ? $request->phone : null,
        ]);

        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => [
                'nullable', 'email', 'max:255',
                Rule::unique('clients', 'email')->where('tenant_id', $tenantId),
            ],
        ], [
            'name.required' => 'Le nom du client est obligatoire.',
            'email.unique'  => 'Un client avec cet email existe déjà dans votre boutique.',
        ]);

        $client = Client::create($validated);

        return response()->json([
            'success' => true,
            'client'  => [
                'id'    => $client->id,
                'name'  => $client->name,
                'phone' => $client->phone,
            ],
        ]);
    }

    /**
     * Afficher les détails d'un client
     */
    public function show(Client $client)
    {
        $this->authorizeSalesAccess();
        $this->authorizeClientAccess($client);
        
        // Charger les ventes du client avec leurs articles
        $client->load(['sales' => function($query) {
            $query->latest()->with('items.product');
        }]);
        
        // Statistiques du client
        $stats = [
            'total_sales' => $client->sales->count(),
            'total_spent' => $client->sales->sum('total_price'),
            'average_cart' => $client->sales->avg('total_price') ?? 0,
            'first_purchase' => $client->sales->min('created_at'),
            'last_purchase' => $client->sales->max('created_at'),
            'products_purchased' => $client->sales->flatMap->items->sum('quantity'),
        ];
        
        // Produits préférés (top 5)
        $favoriteProducts = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->where('sales.client_id', $client->id)
            ->select('products.id', 'products.name', DB::raw('SUM(sale_items.quantity) as total_quantity'))
            ->groupBy('products.id', 'products.name')
            ->orderBy('total_quantity', 'desc')
            ->limit(5)
            ->get();
        
        return view('clients.show', compact('client', 'stats', 'favoriteProducts'));
    }

    /**
     * Formulaire d'édition
     */
    public function edit(Client $client)
    {
        $this->authorizeAdmin();
        $this->authorizeClientAccess($client);
        
        return view('clients.edit', compact('client'));
    }

    /**
     * Mettre à jour un client
     */
    public function update(Request $request, Client $client)
    {
        $this->authorizeAdmin();
        $this->authorizeClientAccess($client);
        
        $tenantId = Auth::user()->tenant_id;

        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => [
                'nullable', 'email', 'max:255',
                Rule::unique('clients', 'email')->where('tenant_id', $tenantId)->ignore($client->id),
            ],
            'phone' => 'nullable|string|max:20',
        ], [
            'email.unique' => 'Un client avec cet email existe déjà dans votre boutique.',
        ]);

        $client->update($request->only(['name', 'email', 'phone']));

        return redirect()->route('clients.index')
                         ->with('success', 'Client mis à jour avec succès ✅');
    }

    /**
     * Archive (soft delete) un client
     */
    public function destroy(Client $client)
    {
        $this->authorizeAdmin();
        $this->authorizeClientAccess($client);

        $hasSales = $client->sales()->exists();

        // Soft delete : les ventes associées restent intactes
        $client->delete();

        $message = $hasSales
            ? "Client archivé (ses ventes sont préservées)."
            : "Client supprimé avec succès.";

        return redirect()->route('clients.index')->with('success', $message);
    }

    /**
     * Recherche de clients (AJAX)
     */
    public function search(Request $request)
    {
        $this->authorizeSalesAccess();
        
        $term = $request->get('q', '');

        $clients = Client::where(function ($q) use ($term) {
                        $q->where('name', 'LIKE', '%' . $term . '%')
                          ->orWhere('email', 'LIKE', '%' . $term . '%')
                          ->orWhere('phone', 'LIKE', '%' . $term . '%');
                    })
                    ->limit(10)
                    ->get(['id', 'name', 'email', 'phone']);
        
        return response()->json($clients);
    }

    /**
     * Historique des achats d'un client
     */
    public function history(Client $client)
    {
        $this->authorizeSalesAccess();
        $this->authorizeClientAccess($client);
        
        $sales = $client->sales()
                       ->with('items.product')
                       ->latest()
                       ->paginate(15);
        
        return view('clients.history', compact('client', 'sales'));
    }

    /**
     * Statistiques détaillées d'un client
     */
    public function statistics(Client $client)
    {
        $this->authorizeSalesAccess();
        $this->authorizeClientAccess($client);
        
        // Ventes par mois
        $salesByMonth = DB::table('sales')
            ->select(DB::raw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as count, SUM(total_price) as total'))
            ->where('client_id', $client->id)
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->limit(12)
            ->get();
        
        // Produits les plus achetés
        $topProducts = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->where('sales.client_id', $client->id)
            ->select('products.name', DB::raw('SUM(sale_items.quantity) as total_quantity'), DB::raw('SUM(sale_items.total_price) as total_spent'))
            ->groupBy('products.id', 'products.name')
            ->orderBy('total_quantity', 'desc')
            ->limit(10)
            ->get();
        
        return view('clients.statistics', compact('client', 'salesByMonth', 'topProducts'));
    }

    /**
     * Exporter les données d'un client
     */
    public function export(Client $client)
    {
        $this->authorizeSalesAccess();
        $this->authorizeClientAccess($client);
        
        $client->load('sales.items.product');
        
        $filename = 'client_' . $client->id . '_' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];
        
        $callback = function() use ($client) {
            $handle = fopen('php://output', 'w');
            
            // En-têtes CSV
            fputcsv($handle, ['Date', 'Vente #', 'Produit', 'Quantité', 'Prix unitaire', 'Total']);
            
            // Données
            foreach ($client->sales as $sale) {
                foreach ($sale->items as $item) {
                    fputcsv($handle, [
                        $sale->created_at->format('d/m/Y H:i'),
                        $sale->id,
                        $item->product->name ?? 'Produit inconnu',
                        $item->quantity,
                        number_format($item->unit_price, 0, ',', ''),
                        number_format($item->total_price, 0, ',', ''),
                    ]);
                }
            }
            
            // Total général
            fputcsv($handle, []);
            fputcsv($handle, ['TOTAL GÉNÉRAL', '', '', '', '', number_format($client->sales->sum('total_price'), 0, ',', '')]);
            
            fclose($handle);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    /**
     * Clients les plus fidèles (pour le dashboard)
     */
    public function topClients()
    {
        $this->authorizeSalesAccess();
        
        $topClients = Client::withCount('sales')
            ->withSum('sales', 'total_price')
            ->orderBy('sales_count', 'desc')
            ->limit(10)
            ->get();
        
        return response()->json($topClients);
    }

    /**
     * Rapport des clients
     */
    public function clientsReport(Request $request)
    {
        $userRole = Auth::user()->role;
        $reportRoles = ['super_admin_global', 'super_admin', 'admin', 'manager'];
        
        if (!in_array($userRole, $reportRoles)) {
            abort(403, 'Vous n\'avez pas les droits pour voir les rapports.');
        }
        
        $tenantId = Auth::user()->tenant_id;
        
        $query = Client::where('tenant_id', $tenantId);
        
        // Filtre recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                ->orWhere('email', 'LIKE', "%{$search}%")
                ->orWhere('phone', 'LIKE', "%{$search}%");
            });
        }
        
        // Filtre statut
        if ($request->filled('status')) {
            if ($request->status == 'active') {
                // Clients ayant acheté au moins une fois
                $query->has('sales');
            } elseif ($request->status == 'inactive') {
                // Clients n'ayant jamais acheté
                $query->doesntHave('sales');
            }
        }
        
        // Filtre période
        if ($request->filled('period')) {
            switch ($request->period) {
                case 'today':
                    $query->whereDate('created_at', today());
                    break;
                case 'week':
                    $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'month':
                    $query->whereMonth('created_at', now()->month);
                    break;
            }
        }
        
        // Charger les relations et les stats
        $clients = $query->withCount('sales')
                        ->withSum('sales', 'total_price')
                        ->orderBy('created_at', 'desc')
                        ->paginate(15);
        
        // Calculer les totaux
        $totalClients = Client::where('tenant_id', $tenantId)->count();
        $activeClients = Client::where('tenant_id', $tenantId)->has('sales')->count();
        $totalSpent = Client::where('tenant_id', $tenantId)->withSum('sales', 'total_price')->get()->sum('sales_sum_total_price');
        $averageSpent = $totalClients > 0 ? $totalSpent / $totalClients : 0;
        
        // Top 10 clients
        $topClients = Client::where('tenant_id', $tenantId)
            ->withSum('sales', 'total_price')
            ->withCount('sales')
            ->orderBy('sales_sum_total_price', 'desc')
            ->limit(10)
            ->get()
            ->map(function($client) {
                return [
                    'name' => $client->name,
                    'email' => $client->email,
                    'phone' => $client->phone,
                    'total_spent' => $client->sales_sum_total_price ?? 0,
                    'total_orders' => $client->sales_count,
                    'last_order' => $client->sales()->latest()->first()?->created_at?->format('d/m/Y')
                ];
            });
        
        $reportData = [
            'total_clients' => $totalClients,
            'active_clients' => $activeClients,
            'total_spent' => $totalSpent,
            'formatted_total_spent' => number_format($totalSpent, 0, ',', ' ') . ' FCFA',
            'average_spent' => $averageSpent,
            'formatted_average_spent' => number_format($averageSpent, 0, ',', ' ') . ' FCFA',
        ];
        
        return view('reports.clients', compact('clients', 'topClients', 'reportData'));
    }
}