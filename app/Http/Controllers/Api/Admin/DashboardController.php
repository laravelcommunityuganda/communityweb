<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Post;
use App\Models\Job;
use App\Models\Event;
use App\Models\Resource;
use App\Models\Report;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    /**
     * Get dashboard statistics.
     */
    public function statistics(): JsonResponse
    {
        $stats = Cache::remember('admin.dashboard.stats', 60, function () {
            return [
                'users' => [
                    'total' => User::count(),
                    'new_this_week' => User::where('created_at', '>=', now()->subWeek())->count(),
                    'new_this_month' => User::where('created_at', '>=', now()->subMonth())->count(),
                    'verified' => User::where('is_verified', true)->count(),
                ],
                'posts' => [
                    'total' => Post::count(),
                    'published' => Post::where('status', 'published')->count(),
                    'draft' => Post::where('status', 'draft')->count(),
                    'this_week' => Post::where('created_at', '>=', now()->subWeek())->count(),
                ],
                'jobs' => [
                    'total' => Job::count(),
                    'published' => Job::where('status', 'published')->count(),
                    'pending' => Job::where('status', 'pending')->count(),
                    'this_week' => Job::where('created_at', '>=', now()->subWeek())->count(),
                ],
                'events' => [
                    'total' => Event::count(),
                    'upcoming' => Event::where('start_date', '>=', now())->count(),
                    'this_week' => Event::where('created_at', '>=', now()->subWeek())->count(),
                ],
                'resources' => [
                    'total' => Resource::count(),
                    'approved' => Resource::where('status', 'approved')->count(),
                    'pending' => Resource::where('status', 'pending')->count(),
                ],
                'reports' => [
                    'total' => Report::count(),
                    'pending' => Report::where('status', 'pending')->count(),
                    'resolved' => Report::where('status', 'resolved')->count(),
                ],
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    /**
     * Get recent activity.
     */
    public function recentActivity(): JsonResponse
    {
        $activities = Cache::remember('admin.dashboard.activity', 30, function () {
            return ActivityLog::with('user.profile')
                ->orderBy('created_at', 'desc')
                ->take(20)
                ->get()
                ->toArray();
        });

        return response()->json([
            'success' => true,
            'data' => $activities,
        ]);
    }

    /**
     * Get growth chart data.
     */
    public function growthChart(Request $request): JsonResponse
    {
        $period = $request->get('period', '30'); // days

        $data = Cache::remember("admin.dashboard.growth.{$period}", 60, function () use ($period) {
            $users = User::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->where('created_at', '>=', now()->subDays($period))
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->toArray();

            $posts = Post::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->where('created_at', '>=', now()->subDays($period))
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->toArray();

            $jobs = Job::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->where('created_at', '>=', now()->subDays($period))
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->toArray();

            return [
                'users' => $users,
                'posts' => $posts,
                'jobs' => $jobs,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Get content overview.
     */
    public function contentOverview(): JsonResponse
    {
        $overview = Cache::remember('admin.dashboard.overview', 60, function () {
            return [
                'posts_by_category' => Post::selectRaw('category_id, COUNT(*) as count')
                    ->with('category:id,name,slug')
                    ->groupBy('category_id')
                    ->get()
                    ->toArray(),
                'jobs_by_type' => Job::selectRaw('type, COUNT(*) as count')
                    ->groupBy('type')
                    ->get()
                    ->toArray(),
                'events_by_type' => Event::selectRaw('type, COUNT(*) as count')
                    ->groupBy('type')
                    ->get()
                    ->toArray(),
                'resources_by_type' => Resource::selectRaw('type, COUNT(*) as count')
                    ->groupBy('type')
                    ->get()
                    ->toArray(),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $overview,
        ]);
    }

    /**
     * Get top users.
     */
    public function topUsers(): JsonResponse
    {
        $topUsers = Cache::remember('admin.dashboard.top_users', 60, function () {
            $topPosters = User::withCount('posts')
                ->orderBy('posts_count', 'desc')
                ->take(5)
                ->get()
                ->toArray();

            $topReputation = User::orderBy('reputation', 'desc')
                ->take(5)
                ->get()
                ->toArray();

            $topEmployers = User::withCount('jobs')
                ->whereHas('jobs')
                ->orderBy('jobs_count', 'desc')
                ->take(5)
                ->get()
                ->toArray();

            return [
                'top_posters' => $topPosters,
                'top_reputation' => $topReputation,
                'top_employers' => $topEmployers,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $topUsers,
        ]);
    }
}