<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Profile;
use App\Http\Resources\UserResource;
use App\Http\Resources\ProfileResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(Request $request): JsonResponse
    {
        $query = User::with('profile')
            ->withCount('posts', 'followers', 'following')
            ->orderBy('reputation', 'desc');

        // Filter by role
        if ($request->has('role')) {
            $query->where('role', $request->role);
        }

        // Filter by skill
        if ($request->has('skill')) {
            $query->whereHas('profile', function ($q) use ($request) {
                $q->whereJsonContains('skills', $request->skill);
            });
        }

        // Filter by location
        if ($request->has('location')) {
            $query->whereHas('profile', function ($q) use ($request) {
                $q->where('location', $request->location);
            });
        }

        // Search by name or username
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%");
            });
        }

        $users = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => UserResource::collection($users),
            'meta' => [
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
            ],
        ]);
    }

    /**
     * Display the specified user.
     */
    public function show(string $username): JsonResponse
    {
        $user = User::where('username', $username)
            ->with('profile')
            ->withCount('posts', 'followers', 'following', 'badges')
            ->firstOrFail();

        // Get user's posts
        $posts = $user->posts()
            ->with('category', 'tags')
            ->latest()
            ->take(5)
            ->get();

        // Get user's badges
        $badges = $user->badges()->get();

        return response()->json([
            'success' => true,
            'data' => new UserResource($user),
            'posts' => $posts,
            'badges' => $badges,
        ]);
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'username' => ['sometimes', 'string', 'max:255', 'alpha_dash', Rule::unique('users')->ignore($user->id)],
            'email' => ['sometimes', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => ['sometimes', 'string', 'min:8', 'confirmed'],
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully',
            'data' => new UserResource($user->fresh()),
        ]);
    }

    /**
     * Update user profile.
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();
        $profile = $user->profile;

        $validated = $request->validate([
            'bio' => ['nullable', 'string', 'max:1000'],
            'title' => ['nullable', 'string', 'max:255'],
            'company' => ['nullable', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'website_url' => ['nullable', 'url', 'max:255'],
            'github_url' => ['nullable', 'url', 'max:255'],
            'twitter_url' => ['nullable', 'url', 'max:255'],
            'linkedin_url' => ['nullable', 'url', 'max:255'],
            'skills' => ['nullable', 'array'],
            'skills.*' => ['string', 'max:50'],
            'is_available_for_work' => ['boolean'],
        ]);

        $profile->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'data' => new ProfileResource($profile->fresh()),
        ]);
    }

    /**
     * Upload profile picture.
     */
    public function uploadAvatar(Request $request): JsonResponse
    {
        $request->validate([
            'avatar' => ['required', 'image', 'max:2048'], // 2MB max
        ]);

        $user = $request->user();

        // Delete old avatar
        if ($user->avatar && !str_contains($user->avatar, 'gravatar')) {
            Storage::disk('public')->delete($user->avatar);
        }

        // Store new avatar
        $path = $request->file('avatar')->store('avatars', 'public');
        $user->update(['avatar' => $path]);

        return response()->json([
            'success' => true,
            'message' => 'Avatar uploaded successfully',
            'data' => [
                'avatar_url' => $user->avatar_url,
            ],
        ]);
    }

    /**
     * Follow a user.
     */
    public function follow(Request $request, User $user): JsonResponse
    {
        $currentUser = $request->user();

        if ($currentUser->id === $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot follow yourself',
            ], 400);
        }

        if ($currentUser->isFollowing($user)) {
            return response()->json([
                'success' => false,
                'message' => 'You are already following this user',
            ], 400);
        }

        $currentUser->following()->attach($user->id);

        return response()->json([
            'success' => true,
            'message' => "You are now following {$user->username}",
        ]);
    }

    /**
     * Unfollow a user.
     */
    public function unfollow(Request $request, User $user): JsonResponse
    {
        $currentUser = $request->user();

        if (!$currentUser->isFollowing($user)) {
            return response()->json([
                'success' => false,
                'message' => 'You are not following this user',
            ], 400);
        }

        $currentUser->following()->detach($user->id);

        return response()->json([
            'success' => true,
            'message' => "You have unfollowed {$user->username}",
        ]);
    }

    /**
     * Block a user.
     */
    public function block(Request $request, User $user): JsonResponse
    {
        $currentUser = $request->user();

        if ($currentUser->id === $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot block yourself',
            ], 400);
        }

        $currentUser->blockedUsers()->syncWithoutDetaching([$user->id]);

        // Remove from following if following
        $currentUser->following()->detach($user->id);
        $currentUser->followers()->detach($user->id);

        return response()->json([
            'success' => true,
            'message' => "You have blocked {$user->username}",
        ]);
    }

    /**
     * Unblock a user.
     */
    public function unblock(Request $request, User $user): JsonResponse
    {
        $currentUser = $request->user();

        $currentUser->blockedUsers()->detach($user->id);

        return response()->json([
            'success' => true,
            'message' => "You have unblocked {$user->username}",
        ]);
    }

    /**
     * Get user's followers.
     */
    public function followers(User $user): JsonResponse
    {
        $followers = $user->followers()
            ->with('profile')
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => UserResource::collection($followers),
        ]);
    }

    /**
     * Get users that the user is following.
     */
    public function following(User $user): JsonResponse
    {
        $following = $user->following()
            ->with('profile')
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => UserResource::collection($following),
        ]);
    }

    /**
     * Get leaderboard.
     */
    public function leaderboard(Request $request): JsonResponse
    {
        $period = $request->get('period', 'all_time');

        $query = User::with('profile')
            ->withCount('posts', 'comments')
            ->orderBy('reputation', 'desc');

        if ($period === 'week') {
            $query->whereHas('posts', function ($q) {
                $q->where('created_at', '>=', now()->subWeek());
            });
        } elseif ($period === 'month') {
            $query->whereHas('posts', function ($q) {
                $q->where('created_at', '>=', now()->subMonth());
            });
        }

        $users = $query->take(50)->get();

        return response()->json([
            'success' => true,
            'data' => UserResource::collection($users),
        ]);
    }

    /**
     * Delete user account.
     */
    public function destroy(Request $request): JsonResponse
    {
        $user = $request->user();

        // Validate password
        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid password',
            ], 400);
        }

        // Delete avatar
        if ($user->avatar && !str_contains($user->avatar, 'gravatar')) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Account deleted successfully',
        ]);
    }
}
