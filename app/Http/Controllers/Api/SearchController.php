<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\User;
use App\Models\Job;
use App\Models\Event;
use App\Models\Resource;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SearchController extends Controller
{
    /**
     * Global search across all content types.
     */
    public function search(Request $request): JsonResponse
    {
        $query = $request->get('q', '');
        $type = $request->get('type', 'all');

        if (strlen($query) < 2) {
            return response()->json([
                'success' => true,
                'data' => [],
            ]);
        }

        $results = [];

        if ($type === 'all' || $type === 'posts') {
            $results['posts'] = Post::search($query)
                ->with(['user.profile', 'category', 'tags'])
                ->published()
                ->take(10)
                ->get();
        }

        if ($type === 'all' || $type === 'users') {
            $results['users'] = User::search($query)
                ->with('profile')
                ->take(10)
                ->get();
        }

        if ($type === 'all' || $type === 'jobs') {
            $results['jobs'] = Job::search($query)
                ->with(['user.profile', 'category'])
                ->published()
                ->take(10)
                ->get();
        }

        if ($type === 'all' || $type === 'events') {
            $results['events'] = Event::search($query)
                ->with(['user.profile', 'category'])
                ->published()
                ->take(10)
                ->get();
        }

        if ($type === 'all' || $type === 'resources') {
            $results['resources'] = Resource::search($query)
                ->with(['user.profile', 'category'])
                ->approved()
                ->take(10)
                ->get();
        }

        if ($type === 'all' || $type === 'tags') {
            $results['tags'] = Tag::search($query)
                ->take(10)
                ->get();
        }

        return response()->json([
            'success' => true,
            'data' => $results,
        ]);
    }

    /**
     * Search posts.
     */
    public function searchPosts(Request $request): JsonResponse
    {
        $query = $request->get('q', '');

        if (strlen($query) < 2) {
            return response()->json([
                'success' => true,
                'data' => [],
            ]);
        }

        $posts = Post::search($query)
            ->with(['user.profile', 'category', 'tags'])
            ->published()
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => \App\Http\Resources\PostResource::collection($posts),
        ]);
    }

    /**
     * Search users.
     */
    public function searchUsers(Request $request): JsonResponse
    {
        $query = $request->get('q', '');

        if (strlen($query) < 2) {
            return response()->json([
                'success' => true,
                'data' => [],
            ]);
        }

        $users = User::search($query)
            ->with('profile')
            ->orderBy('reputation', 'desc')
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => \App\Http\Resources\UserResource::collection($users),
        ]);
    }

    /**
     * Search jobs.
     */
    public function searchJobs(Request $request): JsonResponse
    {
        $query = $request->get('q', '');

        $jobsQuery = Job::with(['user.profile', 'category'])
            ->published()
            ->orderBy('created_at', 'desc');

        if (strlen($query) >= 2) {
            $jobsQuery->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%")
                    ->orWhere('company_name', 'like', "%{$query}%");
            });
        }

        // Filter by location
        if ($request->has('location')) {
            $jobsQuery->where('location', $request->location);
        }

        // Filter by type
        if ($request->has('type')) {
            $jobsQuery->where('type', $request->type);
        }

        // Filter by remote
        if ($request->has('remote')) {
            $jobsQuery->where('is_remote', $request->remote);
        }

        // Filter by skills
        if ($request->has('skills')) {
            $skills = is_array($request->skills) ? $request->skills : explode(',', $request->skills);
            $jobsQuery->where(function ($q) use ($skills) {
                foreach ($skills as $skill) {
                    $q->orWhereJsonContains('required_skills', trim($skill));
                }
            });
        }

        $jobs = $jobsQuery->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => \App\Http\Resources\JobResource::collection($jobs),
        ]);
    }

    /**
     * Search events.
     */
    public function searchEvents(Request $request): JsonResponse
    {
        $query = $request->get('q', '');

        $eventsQuery = Event::with(['user.profile', 'category'])
            ->published()
            ->orderBy('start_date', 'asc');

        if (strlen($query) >= 2) {
            $eventsQuery->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%");
            });
        }

        // Filter by location
        if ($request->has('location')) {
            $eventsQuery->where('venue_city', $request->location);
        }

        // Filter by type
        if ($request->has('type')) {
            $eventsQuery->where('type', $request->type);
        }

        // Filter by format
        if ($request->has('format')) {
            $eventsQuery->where('format', $request->format);
        }

        // Filter upcoming only
        if ($request->has('upcoming') && $request->upcoming) {
            $eventsQuery->where('start_date', '>=', now());
        }

        $events = $eventsQuery->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => \App\Http\Resources\EventResource::collection($events),
        ]);
    }

    /**
     * Get search suggestions.
     */
    public function suggestions(Request $request): JsonResponse
    {
        $query = $request->get('q', '');

        if (strlen($query) < 2) {
            return response()->json([
                'success' => true,
                'data' => [],
            ]);
        }

        $suggestions = [
            'posts' => Post::search($query)
                ->published()
                ->take(3)
                ->get(['id', 'title', 'slug']),
            'users' => User::search($query)
                ->take(3)
                ->get(['id', 'name', 'username', 'avatar']),
            'tags' => Tag::search($query)
                ->take(5)
                ->get(['id', 'name', 'slug']),
        ];

        return response()->json([
            'success' => true,
            'data' => $suggestions,
        ]);
    }
}