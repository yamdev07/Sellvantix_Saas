<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Models\Client;
use App\Services\ClientService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller
{
    public function __construct(private ClientService $clientService)
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Client::class);
        $clients = $this->clientService->list($request)->paginate(10);
        return view('clients.index', compact('clients'));
    }

    public function create()
    {
        $this->authorize('create', Client::class);
        return view('clients.create');
    }

    public function store(StoreClientRequest $request)
    {
        $this->authorize('create', Client::class);
        $this->clientService->create($request->validated(), Auth::user()->tenant_id);
        return redirect()->route('clients.index')->with('success', 'Client ajouté avec succès.');
    }

    public function quickStore(Request $request)
    {
        $this->authorize('create', Client::class);

        $tenantId = Auth::user()->tenant_id;

        $request->merge([
            'email' => $request->filled('email') ? $request->email : null,
            'phone' => $request->filled('phone') ? $request->phone : null,
        ]);

        $data = $request->validate([
            'name'  => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255|unique:clients,email',
        ], [
            'name.required' => 'Le nom du client est obligatoire.',
            'email.unique'  => 'Un client avec cet email existe déjà dans votre boutique.',
        ]);

        $client = $this->clientService->quickCreate($data, $tenantId);

        return response()->json([
            'success' => true,
            'client'  => ['id' => $client->id, 'name' => $client->name, 'phone' => $client->phone],
        ]);
    }

    public function show(Client $client)
    {
        $this->authorize('view', $client);
        $stats            = $this->clientService->stats($client);
        $favoriteProducts = $this->clientService->favoriteProducts($client);
        return view('clients.show', compact('client', 'stats', 'favoriteProducts'));
    }

    public function edit(Client $client)
    {
        $this->authorize('update', $client);
        return view('clients.edit', compact('client'));
    }

    public function update(UpdateClientRequest $request, Client $client)
    {
        $this->authorize('update', $client);
        $this->clientService->update($client, $request->validated());
        return redirect()->route('clients.index')->with('success', 'Client mis à jour avec succès.');
    }

    public function destroy(Client $client)
    {
        $this->authorize('delete', $client);
        $message = $this->clientService->delete($client);
        return redirect()->route('clients.index')->with('success', $message);
    }

    public function search(Request $request)
    {
        $this->authorize('viewAny', Client::class);
        return response()->json($this->clientService->search($request->get('q', '')));
    }

    public function history(Client $client)
    {
        $this->authorize('view', $client);
        $sales = $client->sales()->with('items.product')->latest()->paginate(15);
        return view('clients.history', compact('client', 'sales'));
    }

    public function statistics(Client $client)
    {
        $this->authorize('view', $client);
        $salesByMonth = $this->clientService->salesByMonth($client);
        $topProducts  = $this->clientService->topProducts($client);
        return view('clients.statistics', compact('client', 'salesByMonth', 'topProducts'));
    }

    public function export(Client $client)
    {
        $this->authorize('view', $client);

        $client->load('sales.items.product');
        $filename = 'client_' . $client->id . '_' . date('Y-m-d') . '.csv';

        $callback = function () use ($client) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Date', 'Vente #', 'Produit', 'Quantité', 'Prix unitaire', 'Total']);

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

            fputcsv($handle, []);
            fputcsv($handle, ['TOTAL GÉNÉRAL', '', '', '', '',
                number_format($client->sales->sum('total_price'), 0, ',', '')]);
            fclose($handle);
        };

        return response()->stream($callback, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    public function topClients()
    {
        $this->authorize('viewAny', Client::class);
        return response()->json($this->clientService->topClients(Auth::user()->tenant_id));
    }

    public function clientsReport(Request $request)
    {
        if (!Auth::user()->canViewReports()) {
            abort(403, 'Vous n\'avez pas les droits pour voir les rapports.');
        }

        $data = $this->clientService->reportData($request, Auth::user()->tenant_id);

        return view('reports.clients', $data);
    }
}
