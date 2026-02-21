<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Event;
use App\Models\Post;
use App\Models\User;
use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FrontendController extends Controller
{
    /**
     * Home page
     */
    public function home()
    {
        $stats = [
            'users' => User::count(),
            'posts' => Post::where('status', 'published')->count(),
            'jobs' => Job::where('status', 'published')->count(),
            'events' => Event::where('status', 'published')->count(),
        ];

        $posts = Post::with(['user', 'category', 'tags'])
            ->where('status', 'published')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $categories = Category::withCount('posts')
            ->orderBy('name')
            ->get();

        $events = Event::where('status', 'published')
            ->where('start_date', '>=', now())
            ->orderBy('start_date')
            ->take(3)
            ->get();

        $topUsers = User::with('profile')
            ->orderBy('reputation', 'desc')
            ->take(5)
            ->get();

        return view('home', compact('stats', 'posts', 'categories', 'events', 'topUsers'));
    }

    /**
     * Community posts list
     */
    public function community(Request $request, $categorySlug = null)
    {
        $query = Post::with(['user', 'category', 'tags'])
            ->where('status', 'published');

        if ($categorySlug) {
            $query->whereHas('category', function ($q) use ($categorySlug) {
                $q->where('slug', $categorySlug);
            });
        }

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            });
        }

        if ($request->has('tag')) {
            $query->whereHas('tags', function ($q) use ($request) {
                $q->where('slug', $request->get('tag'));
            });
        }

        $sortBy = $request->get('sort', 'latest');
        switch ($sortBy) {
            case 'popular':
                $query->orderBy('views_count', 'desc');
                break;
            case 'unanswered':
                $query->where('comments_count', 0)->orderBy('created_at', 'desc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }

        $posts = $query->paginate(15);

        $categories = Category::withCount('posts')->get();

        return view('community.index', compact('posts', 'categories', 'categorySlug'));
    }

    /**
     * Single post view
     */
    public function postShow($slug)
    {
        $post = Post::with(['user', 'category', 'tags', 'comments.user', 'comments.replies.user'])
            ->where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        $post->increment('views_count');

        return view('community.show', compact('post'));
    }

    /**
     * Post create form
     */
    public function postCreate()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $categories = Category::all();
        return view('community.create', compact('categories'));
    }

    /**
     * Post edit form
     */
    public function postEdit($slug)
    {
        $post = Post::where('slug', $slug)->firstOrFail();

        if (Auth::id() !== $post->user_id && !Auth::user()->hasRole('admin|moderator')) {
            abort(403);
        }

        $categories = Category::all();
        return view('community.edit', compact('post', 'categories'));
    }

    /**
     * Jobs list
     */
    public function jobs(Request $request)
    {
        $query = Job::with(['user', 'category'])
            ->where('status', 'published');

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('company', 'like', "%{$search}%");
            });
        }

        if ($request->has('type')) {
            $query->where('type', $request->get('type'));
        }

        if ($request->has('location')) {
            $query->where('location', 'like', "%{$request->get('location')}%");
        }

        if ($request->has('remote')) {
            $query->where('is_remote', true);
        }

        $jobs = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('jobs.index', compact('jobs'));
    }

    /**
     * Single job view
     */
    public function jobShow($slug)
    {
        $job = Job::with(['user', 'category'])
            ->where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        return view('jobs.show', compact('job'));
    }

    /**
     * Job create form
     */
    public function jobCreate()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $categories = Category::all();
        return view('jobs.create', compact('categories'));
    }

    /**
     * Events list
     */
    public function events(Request $request)
    {
        $query = Event::with(['user', 'category'])
            ->where('status', 'published');

        if ($request->has('upcoming') && $request->get('upcoming')) {
            $query->where('start_date', '>=', now());
        }

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $events = $query->orderBy('start_date', 'asc')->paginate(15);

        return view('events.index', compact('events'));
    }

    /**
     * Single event view
     */
    public function eventShow($slug)
    {
        $event = Event::with(['user', 'category', 'attendees'])
            ->where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        return view('events.show', compact('event'));
    }

    /**
     * Event create form
     */
    public function eventCreate()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $categories = Category::all();
        return view('events.create', compact('categories'));
    }

    /**
     * Resources list
     */
    public function resources(Request $request)
    {
        $query = \App\Models\Resource::with(['user', 'category'])
            ->where('status', 'published');

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->has('type')) {
            $query->where('type', $request->get('type'));
        }

        $resources = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('resources.index', compact('resources'));
    }

    /**
     * Single resource view
     */
    public function resourceShow($slug)
    {
        $resource = \App\Models\Resource::with(['user', 'category'])
            ->where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        return view('resources.show', compact('resource'));
    }

    /**
     * Donations page
     */
    public function donations()
    {
        $milestones = \App\Models\DonationMilestone::with('donations')
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('donations.index', compact('milestones'));
    }

    /**
     * Mentors list
     */
    public function mentors(Request $request)
    {
        $query = \App\Models\MentorProfile::with(['user', 'skills'])
            ->where('is_available', true);

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        if ($request->has('skill')) {
            $query->whereHas('skills', function ($q) use ($request) {
                $q->where('name', $request->get('skill'));
            });
        }

        $mentors = $query->orderBy('rating', 'desc')->paginate(12);

        return view('mentors.index', compact('mentors'));
    }

    /**
     * User profile
     */
    public function profile($username)
    {
        $user = User::with(['profile', 'posts', 'comments', 'followers', 'following'])
            ->where('username', $username)
            ->firstOrFail();

        $posts = Post::where('user_id', $user->id)
            ->where('status', 'published')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('profile.show', compact('user', 'posts'));
    }

    /**
     * Login page
     */
    public function login()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    /**
     * Register page
     */
    public function register()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.register');
    }

    /**
     * Forgot password page
     */
    public function forgotPassword()
    {
        return view('auth.forgot-password');
    }

    /**
     * Dashboard SPA entry point
     */
    public function dashboard()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        return view('dashboard');
    }

    /**
     * Admin SPA entry point
     */
    public function admin()
    {
        if (!Auth::check() || !Auth::user()->hasRole('admin|moderator')) {
            abort(403);
        }

        return view('dashboard');
    }
}