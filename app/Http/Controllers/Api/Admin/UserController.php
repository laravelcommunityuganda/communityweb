<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(Request $request): JsonResponse
    {
        $query = User::with('profile', 'roles')
            ->withCount('posts', 'followers', 'following');

        // Filter by role
        if ($request->has('role')) {
            $query->whereHas('roles', function ($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        // Filter by status
        if ($request->has('status')) {
            if ($request->status === 'verified') {
                $query->where('is_verified', true);
            } elseif ($request->status === 'banned') {
                $query->where('is_banned', true);
            }
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%");
            });
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $users = $query->paginate($request->get('per_page', 20));

        return response()->json([
            'success' => true,
            'data' => UserResource::collection($users),
        ]);
    }

    /**
     * Display the specified user.
     */
    public function show(User $user): JsonResponse
    {
        $user->load('profile', 'roles', 'badges');
        $user->loadCount('posts', 'comments', 'followers', 'following', 'jobs');

        return response()->json([
            'success' => true,
            'data' => new UserResource($user),
        ]);
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'username' => ['sometimes', 'string', 'max:255', 'alpha_dash', Rule::unique('users')->ignore($user->id)],
            'email' => ['sometimes', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => ['sometimes', 'string', 'in:admin,moderator,verified_developer,recruiter,member'],
            'is_verified' => ['sometimes', 'boolean'],
            'is_banned' => ['sometimes', 'boolean'],
        ]);

        // Update role
        if (isset($validated['role'])) {
            $user->syncRoles([$validated['role']]);
            $validated['role'] = $validated['role'];
        }

        $user->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully',
            'data' => new UserResource($user->fresh()),
        ]);
    }

    /**
     * Ban a user.
     */
    public function ban(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:500'],
        ]);

        $user->update([
            'is_banned' => true,
            'banned_at' => now(),
            'banned_reason' => $validated['reason'],
        ]);

        // Log activity
        activity()
            ->performedOn($user)
            ->causedBy($request->user())
            ->withProperties(['reason' => $validated['reason']])
            ->log('banned');

        return response()->json([
            'success' => true,
            'message' => 'User banned successfully',
        ]);
    }

    /**
     * Unban a user.
     */
    public function unban(User $user): JsonResponse
    {
        $user->update([
            'is_banned' => false,
            'banned_at' => null,
            'banned_reason' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User unbanned successfully',
        ]);
    }

    /**
     * Verify a user.
     */
    public function verify(User $user): JsonResponse
    {
        $user->update([
            'is_verified' => true,
            'email_verified_at' => $user->email_verified_at ?? now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User verified successfully',
        ]);
    }

    /**
     * Delete a user.
     */
    public function destroy(User $user): JsonResponse
    {
        // Soft delete
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully',
        ]);
    }

    /**
     * Restore a deleted user.
     */
    public function restore(int $id): JsonResponse
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->restore();

        return response()->json([
            'success' => true,
            'message' => 'User restored successfully',
        ]);
    }

    /**
     * Force delete a user permanently.
     */
    public function forceDelete(int $id): JsonResponse
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->forceDelete();

        return response()->json([
            'success' => true,
            'message' => 'User permanently deleted',
        ]);
    }

    /**
     * Get user statistics.
     */
    public function statistics(): JsonResponse
    {
        $stats = [
            'total' => User::count(),
            'verified' => User::where('is_verified', true)->count(),
            'banned' => User::where('is_banned', true)->count(),
            'by_role' => User::selectRaw('role, COUNT(*) as count')
                ->groupBy('role')
                ->pluck('count', 'role'),
            'registrations' => [
                'today' => User::whereDate('created_at', today())->count(),
                'this_week' => User::where('created_at', '>=', now()->subWeek())->count(),
                'this_month' => User::where('created_at', '>=', now()->subMonth())->count(),
            ],
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    /**
     * Assign role to user.
     */
    public function assignRole(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate([
            'role' => ['required', 'string', 'exists:roles,name'],
        ]);

        $user->syncRoles([$validated['role']]);

        return response()->json([
            'success' => true,
            'message' => 'Role assigned successfully',
        ]);
    }

    /**
     * Remove role from user.
     */
    public function removeRole(User $user): JsonResponse
    {
        $user->syncRoles(['member']);

        return response()->json([
            'success' => true,
            'message' => 'Role removed successfully',
        ]);
    }
}