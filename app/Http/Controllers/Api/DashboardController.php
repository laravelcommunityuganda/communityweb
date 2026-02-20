<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    /**
     * Get user dashboard data.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $profile = $user->profile;

        // Get stats
        $stats = [
            'posts' => $user->posts()->count(),
            'comments' => $user->comments()->count(),
            'followers' => $user->followers()->count(),
            'bookmarks' => $user->bookmarkedPosts()->count() + $user->savedJobs()->count(),
        ];

        // Get recent posts
        $posts = $user->posts()
            ->with('category', 'tags')
            ->latest()
            ->take(5)
            ->get();

        // Get recent activity
        $activities = $this->getRecentActivity($user);

        // Get badges
        $badges = $user->badges()->latest()->take(6)->get();

        // Get upcoming events
        $events = $user->attendingEvents()
            ->with('category')
            ->where('start_date', '>=', now())
            ->orderBy('start_date', 'asc')
            ->take(3)
            ->get();

        // Calculate profile completion
        $profileItems = [
            ['label' => 'Profile picture', 'completed' => !empty($user->avatar) && !str_contains($user->avatar, 'gravatar')],
            ['label' => 'Bio', 'completed' => !empty($profile->bio)],
            ['label' => 'Skills', 'completed' => !empty($profile->skills) && count($profile->skills ?? []) > 0],
            ['label' => 'Location', 'completed' => !empty($profile->location)],
            ['label' => 'GitHub profile', 'completed' => !empty($profile->github_url)],
        ];

        $completedItems = collect($profileItems)->where('completed', true)->count();
        $profileCompletion = round(($completedItems / count($profileItems)) * 100);

        return response()->json([
            'success' => true,
            'data' => [
                'stats' => $stats,
                'posts' => $posts,
                'activities' => $activities,
                'badges' => $badges,
                'events' => $events,
                'profile_completion' => $profileCompletion,
                'profile_items' => $profileItems,
            ],
        ]);
    }

    /**
     * Get recent activity for the user.
     */
    private function getRecentActivity($user): array
    {
        $activities = [];

        // Recent posts
        $recentPosts = $user->posts()->latest()->take(3)->get();
        foreach ($recentPosts as $post) {
            $activities[] = [
                'id' => 'post_' . $post->id,
                'type' => 'post',
                'message' => "You created a new post: <a href=\"/post/{$post->slug}\" class=\"text-laravel-600 hover:underline\">{$post->title}</a>",
                'time' => $post->created_at->diffForHumans(),
            ];
        }

        // Recent comments
        $recentComments = $user->comments()->with('post')->latest()->take(3)->get();
        foreach ($recentComments as $comment) {
            $activities[] = [
                'id' => 'comment_' . $comment->id,
                'type' => 'comment',
                'message' => "You commented on: <a href=\"/post/{$comment->post->slug}\" class=\"text-laravel-600 hover:underline\">{$comment->post->title}</a>",
                'time' => $comment->created_at->diffForHumans(),
            ];
        }

        // Recent badges
        $recentBadges = $user->badges()->withPivot('earned_at')->latest('user_badges.created_at')->take(2)->get();
        foreach ($recentBadges as $badge) {
            $activities[] = [
                'id' => 'badge_' . $badge->id,
                'type' => 'badge',
                'message' => "You earned the <strong>{$badge->name}</strong> badge!",
                'time' => $badge->pivot->earned_at->diffForHumans(),
            ];
        }

        // Sort by time
        usort($activities, function ($a, $b) {
            return strtotime($b['time']) - strtotime($a['time']);
        });

        return array_slice($activities, 0, 10);
    }
}