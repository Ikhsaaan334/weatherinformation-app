<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Paginated list of users with their roles.
     */
    public function index(Request $request): JsonResponse
    {
        $query = User::query()->with('roles');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->search.'%')
                    ->orWhere('email', 'like', '%'.$request->search.'%');
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

        return response()->json([
            'users' => $users,
            'filters' => $request->only(['search']),
            'roles' => Role::orderBy('name')->pluck('name'),
        ]);
    }

    /**
     * Update the role assigned to a user.
     */
    public function updateRole(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate([
            'role' => ['required', 'string', Rule::exists('roles', 'name')],
        ]);

        // Prevent an admin from demoting themselves and risking lockout.
        if ($user->is($request->user()) && $validated['role'] !== 'admin') {
            return response()->json([
                'message' => 'You cannot change your own role.',
            ], 422);
        }

        $user->syncRoles([$validated['role']]);

        return response()->json([
            'message' => "Role updated for {$user->name}.",
        ]);
    }
}
