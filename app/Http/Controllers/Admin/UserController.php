<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of users with their roles.
     */
    public function index(Request $request)
    {
        $query = User::query()->with('roles');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $users = $query->latest()->paginate(10)->withQueryString()
            ->through(fn (User $user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->getRoleNames()->first(),
                'created_at' => $user->created_at,
            ]);

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
            'filters' => $request->only(['search']),
            'roles' => Role::orderBy('name')->pluck('name'),
        ]);
    }

    /**
     * Update the role assigned to the given user.
     */
    public function updateRole(Request $request, User $user)
    {
        $validated = $request->validate([
            'role' => ['required', 'string', Rule::exists('roles', 'name')],
        ]);

        // Prevent an admin from demoting themselves and risking lockout.
        if ($user->is($request->user()) && $validated['role'] !== 'admin') {
            return redirect()->back()->with('message', 'You cannot change your own role.');
        }

        $user->syncRoles([$validated['role']]);

        return redirect()->back()->with('message', "Role updated for {$user->name}.");
    }
}
