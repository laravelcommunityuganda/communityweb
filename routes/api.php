<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\TagController;
use App\Http\Controllers\Api\JobController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\ResourceController;
use App\Http\Controllers\Api\MentorshipController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\VoteController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\DonationController;
use App\Http\Controllers\Api\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Api\Admin\UserController as AdminUserController;
use App\Http\Controllers\Api\Admin\SettingsController as AdminSettingsController;
use App\Http\Controllers\Api\Admin\DonationController as AdminDonationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// API v1
Route::prefix('v1')->group(function () {

    // Public routes
    Route::get('/stats', function () {
        return response()->json([
            'success' => true,
            'data' => [
                'users' => \App\Models\User::count(),
                'posts' => \App\Models\Post::where('status', 'published')->count(),
                'jobs' => \App\Models\Job::where('status', 'published')->count(),
                'events' => \App\Models\Event::where('status', 'published')->count(),
            ],
        ]);
    });

    // Authentication routes
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
        Route::post('/reset-password', [AuthController::class, 'resetPassword']);
        
        // Social login
        Route::get('/{provider}', [AuthController::class, 'redirectToProvider']);
        Route::get('/{provider}/callback', [AuthController::class, 'handleProviderCallback']);

        Route::middleware('auth:sanctum')->group(function () {
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::get('/user', [AuthController::class, 'user']);
            Route::post('/email/verification-notification', [AuthController::class, 'resendVerification']);
            Route::get('/user/dashboard', [DashboardController::class, 'index']);
        });
    });

    // Categories (public)
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{slug}', [CategoryController::class, 'show']);
    Route::get('/categories/{slug}/posts', [CategoryController::class, 'posts']);

    // Tags (public)
    Route::get('/tags', [TagController::class, 'index']);
    Route::get('/tags/popular', [TagController::class, 'popular']);
    Route::get('/tags/{slug}', [TagController::class, 'show']);

    // Posts (public read)
    Route::get('/posts', [PostController::class, 'index']);
    Route::get('/posts/{slug}', [PostController::class, 'show']);

    // Jobs (public read)
    Route::get('/jobs', [JobController::class, 'index']);
    Route::get('/jobs/{slug}', [JobController::class, 'show']);

    // Events (public read)
    Route::get('/events', [EventController::class, 'index']);
    Route::get('/events/upcoming', [EventController::class, 'upcoming']);
    Route::get('/events/{slug}', [EventController::class, 'show']);

    // Resources (public read)
    Route::get('/resources', [ResourceController::class, 'index']);
    Route::get('/resources/{slug}', [ResourceController::class, 'show']);

    // Mentors (public read)
    Route::get('/mentors', [MentorshipController::class, 'mentors']);

    // Users (public read)
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/leaderboard', [UserController::class, 'leaderboard']);
    Route::get('/users/{username}', [UserController::class, 'show']);
    Route::get('/users/{username}/followers', [UserController::class, 'followers']);
    Route::get('/users/{username}/following', [UserController::class, 'following']);

    // Search (public)
    Route::get('/search', [SearchController::class, 'search']);
    Route::get('/search/posts', [SearchController::class, 'searchPosts']);
    Route::get('/search/users', [SearchController::class, 'searchUsers']);
    Route::get('/search/jobs', [SearchController::class, 'searchJobs']);
    Route::get('/search/events', [SearchController::class, 'searchEvents']);
    Route::get('/search/suggestions', [SearchController::class, 'suggestions']);

    // Donations (public)
    Route::get('/donations/milestones', [DonationController::class, 'milestones']);
    Route::get('/donations/milestones/{milestone}', [DonationController::class, 'milestone']);
    Route::get('/donations/milestones/{milestone}/donations', [DonationController::class, 'recentDonations']);

    // Authenticated routes
    Route::middleware('auth:sanctum')->group(function () {
        // User profile
        Route::put('/user', [UserController::class, 'update']);
        Route::put('/user/profile', [UserController::class, 'updateProfile']);
        Route::post('/user/avatar', [UserController::class, 'uploadAvatar']);
        Route::delete('/user', [UserController::class, 'destroy']);

        // Follow/Block
        Route::post('/users/{user}/follow', [UserController::class, 'follow']);
        Route::post('/users/{user}/unfollow', [UserController::class, 'unfollow']);
        Route::post('/users/{user}/block', [UserController::class, 'block']);
        Route::post('/users/{user}/unblock', [UserController::class, 'unblock']);

        // Posts
        Route::post('/posts', [PostController::class, 'store']);
        Route::put('/posts/{post}', [PostController::class, 'update']);
        Route::delete('/posts/{post}', [PostController::class, 'destroy']);
        Route::post('/posts/{post}/bookmark', [PostController::class, 'bookmark']);
        Route::post('/posts/{post}/solve', [PostController::class, 'markAsSolved']);

        // Comments
        Route::get('/posts/{post}/comments', [CommentController::class, 'index']);
        Route::post('/posts/{post}/comments', [CommentController::class, 'store']);
        Route::put('/comments/{comment}', [CommentController::class, 'update']);
        Route::delete('/comments/{comment}', [CommentController::class, 'destroy']);
        Route::post('/comments/{comment}/accept', [CommentController::class, 'acceptAsAnswer']);
        Route::get('/comments/{comment}/replies', [CommentController::class, 'replies']);

        // Votes
        Route::post('/posts/{post}/vote', [VoteController::class, 'votePost']);
        Route::get('/posts/{post}/vote', [VoteController::class, 'getPostVote']);
        Route::post('/comments/{comment}/vote', [VoteController::class, 'voteComment']);
        Route::get('/comments/{comment}/vote', [VoteController::class, 'getCommentVote']);

        // Jobs
        Route::post('/jobs', [JobController::class, 'store']);
        Route::put('/jobs/{job}', [JobController::class, 'update']);
        Route::delete('/jobs/{job}', [JobController::class, 'destroy']);
        Route::post('/jobs/{job}/apply', [JobController::class, 'apply']);
        Route::post('/jobs/{job}/save', [JobController::class, 'save']);

        // Events
        Route::post('/events', [EventController::class, 'store']);
        Route::put('/events/{event}', [EventController::class, 'update']);
        Route::delete('/events/{event}', [EventController::class, 'destroy']);
        Route::post('/events/{event}/rsvp', [EventController::class, 'rsvp']);
        Route::get('/events/{event}/attendees', [EventController::class, 'attendees']);

        // Resources
        Route::post('/resources', [ResourceController::class, 'store']);
        Route::put('/resources/{resource}', [ResourceController::class, 'update']);
        Route::delete('/resources/{resource}', [ResourceController::class, 'destroy']);
        Route::get('/resources/{resource}/download', [ResourceController::class, 'download']);
        Route::post('/resources/{resource}/rate', [ResourceController::class, 'rate']);
        Route::post('/resources/{resource}/bookmark', [ResourceController::class, 'bookmark']);

        // Mentorship
        Route::post('/mentorship/become', [MentorshipController::class, 'becomeMentor']);
        Route::put('/mentorship/profile', [MentorshipController::class, 'updateMentorProfile']);
        Route::post('/mentorship/{mentorship}/request', [MentorshipController::class, 'requestMentorship']);
        Route::post('/mentorship/requests/{requestId}/accept', [MentorshipController::class, 'acceptRequest']);
        Route::post('/mentorship/requests/{requestId}/decline', [MentorshipController::class, 'declineRequest']);
        Route::post('/mentorship/sessions/{requestId}', [MentorshipController::class, 'scheduleSession']);
        Route::post('/mentorship/sessions/{session}/complete', [MentorshipController::class, 'completeSession']);
        Route::post('/mentorship/sessions/{session}/rate', [MentorshipController::class, 'rateSession']);
        Route::get('/mentorship/my-requests', [MentorshipController::class, 'myRequests']);
        Route::get('/mentorship/my-mentorships', [MentorshipController::class, 'myMentorships']);

        // Messages
        Route::get('/conversations', [MessageController::class, 'conversations']);
        Route::post('/conversations', [MessageController::class, 'getConversation']);
        Route::get('/conversations/{conversation}/messages', [MessageController::class, 'messages']);
        Route::post('/messages', [MessageController::class, 'send']);
        Route::delete('/messages/{message}', [MessageController::class, 'destroy']);
        Route::post('/conversations/{conversation}/read', [MessageController::class, 'markAsRead']);
        Route::get('/messages/unread-count', [MessageController::class, 'unreadCount']);
        Route::post('/conversations/group', [MessageController::class, 'createGroup']);
        Route::post('/conversations/{conversation}/add-participant', [MessageController::class, 'addParticipant']);
        Route::post('/conversations/{conversation}/leave', [MessageController::class, 'leaveGroup']);

        // Notifications
        Route::get('/notifications', [NotificationController::class, 'index']);
        Route::get('/notifications/unread', [NotificationController::class, 'unread']);
        Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount']);
        Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
        Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);
        Route::delete('/notifications/{id}', [NotificationController::class, 'destroy']);
        Route::delete('/notifications', [NotificationController::class, 'clearAll']);

        // Reports
        Route::post('/reports', [ReportController::class, 'store']);

        // Donations
        Route::post('/donations/initialize', [DonationController::class, 'initialize']);
        Route::post('/donations/callback', [DonationController::class, 'callback']);
        Route::get('/donations/history', [DonationController::class, 'history']);

        // Admin routes
        Route::middleware(['role:admin|moderator'])->prefix('admin')->group(function () {
            // Dashboard
            Route::get('/statistics', [AdminDashboardController::class, 'statistics']);
            Route::get('/activity', [AdminDashboardController::class, 'recentActivity']);
            Route::get('/growth', [AdminDashboardController::class, 'growthChart']);
            Route::get('/content-overview', [AdminDashboardController::class, 'contentOverview']);
            Route::get('/top-users', [AdminDashboardController::class, 'topUsers']);

            // Users management
            Route::get('/users', [AdminUserController::class, 'index']);
            Route::get('/users/statistics', [AdminUserController::class, 'statistics']);
            Route::get('/users/{user}', [AdminUserController::class, 'show']);
            Route::put('/users/{user}', [AdminUserController::class, 'update']);
            Route::post('/users/{user}/ban', [AdminUserController::class, 'ban']);
            Route::post('/users/{user}/unban', [AdminUserController::class, 'unban']);
            Route::post('/users/{user}/verify', [AdminUserController::class, 'verify']);
            Route::delete('/users/{user}', [AdminUserController::class, 'destroy']);
            Route::post('/users/{id}/restore', [AdminUserController::class, 'restore']);
            Route::delete('/users/{id}/force', [AdminUserController::class, 'forceDelete']);
            Route::post('/users/{user}/assign-role', [AdminUserController::class, 'assignRole']);
            Route::post('/users/{user}/remove-role', [AdminUserController::class, 'removeRole']);

            // Reports management
            Route::get('/reports', [ReportController::class, 'index']);
            Route::get('/reports/statistics', [ReportController::class, 'statistics']);
            Route::post('/reports/{report}/resolve', [ReportController::class, 'resolve']);
            Route::post('/reports/{report}/dismiss', [ReportController::class, 'dismiss']);

            // Categories management
            Route::post('/categories', [CategoryController::class, 'store']);
            Route::put('/categories/{category}', [CategoryController::class, 'update']);
            Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);

            // Tags management
            Route::post('/tags', [TagController::class, 'store']);
            Route::delete('/tags/{tag}', [TagController::class, 'destroy']);

            // Jobs moderation
            Route::post('/jobs/{job}/approve', [JobController::class, 'approve']);
            Route::post('/jobs/{job}/feature', [JobController::class, 'feature']);

            // Resources moderation
            Route::post('/resources/{resource}/approve', [ResourceController::class, 'approve']);

            // Settings (admin only)
            Route::middleware(['role:admin'])->group(function () {
                Route::get('/settings', [AdminSettingsController::class, 'index']);
                Route::put('/settings', [AdminSettingsController::class, 'update']);
                Route::get('/system-info', [AdminSettingsController::class, 'systemInfo']);
                Route::post('/cache/clear', [AdminSettingsController::class, 'clearCache']);
                Route::post('/cache/clear-views', [AdminSettingsController::class, 'clearViewCache']);
                Route::post('/cache/clear-config', [AdminSettingsController::class, 'clearConfigCache']);

                // Donations management
                Route::get('/donations', [AdminDonationController::class, 'donations']);
                Route::get('/donations/statistics', [AdminDonationController::class, 'statistics']);
                Route::get('/donations/export', [AdminDonationController::class, 'export']);
                Route::get('/donations/milestones', [AdminDonationController::class, 'milestones']);
                Route::post('/donations/milestones', [AdminDonationController::class, 'storeMilestone']);
                Route::put('/donations/milestones/{milestone}', [AdminDonationController::class, 'updateMilestone']);
                Route::delete('/donations/milestones/{milestone}', [AdminDonationController::class, 'deleteMilestone']);
            });
        });
    });
});