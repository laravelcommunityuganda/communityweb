@extends('layouts.app')

@section('title', 'Home - ' . config('app.name'))

@section('content')
<div class="min-h-screen">
    <!-- Hero Section -->
    <section class="bg-gradient-to-r from-primary-600 to-primary-800 dark:from-primary-800 dark:to-primary-900 text-white py-16">
        <div class="container mx-auto px-4">
            <div class="max-w-3xl mx-auto text-center">
                <h1 class="text-4xl md:text-5xl font-bold mb-4">
                    Welcome to Laravel Uganda Community
                </h1>
                <p class="text-xl text-primary-100 mb-8">
                    Connect, learn, and grow with fellow Laravel Developers in Uganda. Ask questions, share resources, find jobs, and attend events.
                </p>
                @guest
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="{{ route('register') }}" class="px-6 py-3 bg-white text-primary-600 rounded-lg font-semibold hover:bg-primary-50 transition">
                            Join the Community
                        </a>
                        <a href="{{ route('login') }}" class="px-6 py-3 border-2 border-white text-white rounded-lg font-semibold hover:bg-white/10 transition">
                            Sign In
                        </a>
                    </div>
                @endguest
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="py-8 bg-white dark:bg-gray-800 border-b dark:border-gray-700">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                <div>
                    <div class="text-3xl font-bold text-primary-600 dark:text-primary-400">{{ number_format($stats['users']) }}</div>
                    <div class="text-gray-600 dark:text-gray-400">Developers</div>
                </div>
                <div>
                    <div class="text-3xl font-bold text-primary-600 dark:text-primary-400">{{ number_format($stats['posts']) }}</div>
                    <div class="text-gray-600 dark:text-gray-400">Posts</div>
                </div>
                <div>
                    <div class="text-3xl font-bold text-primary-600 dark:text-primary-400">{{ number_format($stats['jobs']) }}</div>
                    <div class="text-gray-600 dark:text-gray-400">Jobs</div>
                </div>
                <div>
                    <div class="text-3xl font-bold text-primary-600 dark:text-primary-400">{{ number_format($stats['events']) }}</div>
                    <div class="text-gray-600 dark:text-gray-400">Events</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <section class="py-12">
        <div class="container mx-auto px-4">
            <div class="grid lg:grid-cols-3 gap-8">
                <!-- Posts Feed -->
                <div class="lg:col-span-2">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Recent Posts</h2>
                        <a href="{{ route('community') }}" class="text-primary-600 dark:text-primary-400 hover:underline">
                            View All
                        </a>
                    </div>

                    @if($posts->isEmpty())
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 text-center">
                            <p class="text-gray-600 dark:text-gray-400">No posts yet. Be the first to share!</p>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($posts as $post)
                                <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm hover:shadow-md transition">
                                    <div class="flex items-start gap-4">
                                        <img src="{{ $post->user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($post->user->name) . '&background=4F46E5&color=fff' }}" 
                                             alt="{{ $post->user->name }}" 
                                             class="w-10 h-10 rounded-full">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2 mb-1">
                                                <a href="{{ route('profile', $post->user->username) }}" class="font-medium text-gray-900 dark:text-white hover:text-primary-600 dark:hover:text-primary-400">
                                                    {{ $post->user->name }}
                                                </a>
                                                <span class="text-gray-400 text-sm">•</span>
                                                <span class="text-gray-500 dark:text-gray-400 text-sm">{{ $post->created_at->diffForHumans() }}</span>
                                            </div>
                                            <a href="{{ route('post.show', $post->slug) }}" class="block">
                                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white hover:text-primary-600 dark:hover:text-primary-400 mb-2">
                                                    {{ $post->title }}
                                                </h3>
                                                <p class="text-gray-600 dark:text-gray-400 line-clamp-2">
                                                    {{ Str::limit(strip_tags($post->content), 200) }}
                                                </p>
                                            </a>
                                            <div class="flex items-center gap-4 mt-4">
                                                @if($post->category)
                                                    <span class="px-2 py-1 bg-primary-100 dark:bg-primary-900 text-primary-600 dark:text-primary-400 text-xs rounded">
                                                        {{ $post->category->name }}
                                                    </span>
                                                @endif
                                                <span class="text-gray-500 dark:text-gray-400 text-sm flex items-center gap-1">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                                    </svg>
                                                    {{ $post->comments_count ?? 0 }}
                                                </span>
                                                <span class="text-gray-500 dark:text-gray-400 text-sm flex items-center gap-1">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                    </svg>
                                                    {{ $post->views_count ?? 0 }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Sidebar -->
                <div class="space-y-8">
                    <!-- Categories -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Categories</h3>
                        <div class="space-y-2">
                            @foreach($categories as $category)
                                <a href="{{ route('community.category', $category->slug) }}" 
                                   class="flex items-center justify-between p-2 rounded hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                                    <span class="text-gray-700 dark:text-gray-300">{{ $category->name }}</span>
                                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ $category->posts_count }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <!-- Upcoming Events -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Upcoming Events</h3>
                        @if($events->isEmpty())
                            <p class="text-gray-500 dark:text-gray-400">No upcoming events</p>
                        @else
                            <div class="space-y-4">
                                @foreach($events as $event)
                                    <a href="{{ route('event.show', $event->slug) }}" class="block p-3 rounded-lg border border-gray-200 dark:border-gray-700 hover:border-primary-500 transition">
                                        <div class="flex items-center gap-3">
                                            <div class="text-center">
                                                <div class="text-2xl font-bold text-primary-600 dark:text-primary-400">{{ $event->start_date->format('d') }}</div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $event->start_date->format('M') }}</div>
                                            </div>
                                            <div>
                                                <h4 class="font-medium text-gray-900 dark:text-white">{{ $event->title }}</h4>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $event->location }}</p>
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        @endif
                        <a href="{{ route('events') }}" class="block mt-4 text-center text-primary-600 dark:text-primary-400 hover:underline">
                            View All Events
                        </a>
                    </div>

                    <!-- Top Users -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Top Contributors</h3>
                        <div class="space-y-3">
                            @foreach($topUsers as $index => $user)
                                <a href="{{ route('profile', $user->username) }}" 
                                   class="flex items-center gap-3 p-2 rounded hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400 w-6">#{{ $index + 1 }}</span>
                                    <img src="{{ $user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=4F46E5&color=fff' }}" 
                                         alt="{{ $user->name }}" 
                                         class="w-8 h-8 rounded-full">
                                    <div class="flex-1 min-w-0">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $user->name }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $user->reputation }} rep</div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
