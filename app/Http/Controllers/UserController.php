<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class UserController extends Controller
{
    public function __construct(private UserService $userService)
    {
        $this->middleware('auth');
    }

    public function index(): View
    {
        $this->authorize('viewAny', User::class);

        $users = $this->userService->list(Auth::user());

        return view('users.index', compact('users'));
    }

    public function create(): View
    {
        $this->authorize('create', User::class);

        $availableRoles = $this->userService->availableRoles(Auth::user());

        return view('users.create', compact('availableRoles'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', User::class);

        $validated = $request->validate([
            'name'                  => 'required|string|max:255',
            'email'                 => 'required|email|unique:users,email',
            'password'              => 'required|string|min:6|confirmed',
            'role'                  => 'required|in:admin,manager,cashier,storekeeper',
        ]);

        try {
            $this->userService->create(Auth::user(), $validated);
        } catch (\RuntimeException $e) {
            if (str_starts_with($e->getMessage(), 'PLAN_LIMIT:')) {
                $detail = substr($e->getMessage(), strlen('PLAN_LIMIT:'));
                return back()->with('upgrade', $detail);
            }
            return back()->with('error', $e->getMessage())->withInput();
        }

        return redirect()->route('users.index')->with('success', 'Employé ajouté avec succès.');
    }

    public function edit(User $user): View
    {
        $this->authorize('update', $user);

        $availableRoles = $this->userService->availableRoles(Auth::user());

        return view('users.edit', compact('user', 'availableRoles'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $this->authorize('update', $user);

        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role'  => 'required|in:admin,manager,cashier,storekeeper',
        ]);

        try {
            $this->userService->update($user, $validated);
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }

        return redirect()->route('users.index')->with('success', 'Employé mis à jour avec succès.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->authorize('delete', $user);

        try {
            $this->userService->delete(Auth::user(), $user);
        } catch (\RuntimeException $e) {
            $message = $e->getMessage();
            $key = str_contains($message, 'ventes') ? 'warning' : 'error';
            return redirect()->route('users.index')->with($key, $message);
        }

        return redirect()->route('users.index')->with('success', 'Employé supprimé avec succès.');
    }

    public function statistics(): View
    {
        $this->authorize('viewAny', User::class);

        $data      = $this->userService->statistics(Auth::user());
        $stats     = $data['stats'];
        $employees = $data['employees'];

        return view('users.statistics', compact('stats', 'employees'));
    }

    public function getEmployees(Request $request): \Illuminate\Http\JsonResponse
    {
        $this->authorize('viewAny', User::class);

        $currentUser = Auth::user();
        $ownerId     = $currentUser->isSuperAdmin() ? $currentUser->id : $currentUser->owner_id;
        $search      = $request->get('q');

        $employees = User::where('owner_id', $ownerId)
            ->where('id', '!=', $currentUser->id)
            ->when($search, fn ($q) => $q->where('name', 'LIKE', "%{$search}%"))
            ->limit(20)
            ->get(['id', 'name', 'role']);

        return response()->json($employees);
    }

    public function revokeAdminRights(User $user): RedirectResponse
    {
        $this->authorize('update', $user);

        $this->userService->revokeAdmin($user);

        return redirect()->route('users.index')
            ->with('success', "Les droits d'administrateur de {$user->name} ont été révoqués.");
    }

    public function promoteToAdmin(User $user): RedirectResponse
    {
        $this->authorize('update', $user);

        $this->userService->promoteToAdmin($user);

        return redirect()->route('users.index')
            ->with('success', "{$user->name} a été promu administrateur.");
    }
}
