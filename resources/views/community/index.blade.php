@extends('layouts.app')

@section('title', 'Community - ' . config('app.name'))

@section('content')
<div class="min-h-screen py-8">
    <div class="container mx-auto px-4">
        <div class="grid lg:grid-cols-4 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-3">
                <!-- Header -->
                <div class="flex items-center justify-between mb-6">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                        @if($categorySlug)
                            {{ App\Models\Category::where('slug', $categorySlug)->first()->name ?? 'Community' }}
                        @else
                            Community
                        @endif
                    </h1>
                    @auth
                        <a href="{{ route('post.create') }}" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition">
                            New Post
                        </a>
                    @endauth
                </div>

                <!-- Filters -->
                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 mb-6">
                    <form method="GET" class="flex flex-wrap gap-4">
                        <div class="flex-1 min-w-[200px]">
                            <input type="text" 
                                   name="search" 
                                   placeholder="Search posts..." 
                                   value="{{ request('search') }}"
                                   class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        </div>
                        <select name="sort" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500">
                            <option value="latest" {{ request('sort') === 'latest' ? 'selected' : '' }}>Latest</option>
                            <option value="popular" {{ request('sort') === 'popular' ? 'selected' : '' }}>Popular</option>
                            <option value="unanswered" {{ request('sort') === 'unanswered' ? 'selected' : '' }}>Unanswered</option>
                        </select>
                        <button type="submit" class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                            Filter
                        </button>
                    </form>
                </div>

                <!-- Posts List -->
                @if($posts->isEmpty())
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-8 text-center">
                        <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No posts found</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-4">Be the first to start a discussion!</p>
                        @auth
                            <a href="{{ route('post.create') }}" class="inline-block px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition">
                                Create Post
                            </a>
                        @endauth
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach($posts as $post)
                            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm hover:shadow-md transition">
                                <div class="flex gap-4">
                                    <!-- Vote Count -->
                                    <div class="flex flex-col items-center text-center min-w-[60px]">
                                        <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $post->votes_count ?? 0 }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">votes</div>
                                    </div>

                                    <!-- Content -->
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-2">
                                            @if($post->category)
                                                <a href="{{ route('community.category', $post->category->slug) }}" class="px-2 py-1 bg-primary-100 dark:bg-primary-900 text-primary-600 dark:text-primary-400 text-xs rounded hover:bg-primary-200 dark:hover:bg-primary-800 transition">
                                                    {{ $post->category->name }}
                                                </a>
                                            @endif
                                            @if($post->is_solved)
                                                <span class="px-2 py-1 bg-green-100 dark:bg-green-900 text-green-600 dark:text-green-400 text-xs rounded">
                                                    Solved
                                                </span>
                                            @endif
                                        </div>

                                        <a href="{{ route('post.show', $post->slug) }}" class="block">
                                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white hover:text-primary-600 dark:hover:text-primary-400 mb-2">
                                                {{ $post->title }}
                                            </h2>
                                            <p class="text-gray-600 dark:text-gray-400 line-clamp-2 mb-4">
                                                {{ Str::limit(strip_tags($post->content), 200) }}
                                            </p>
                                        </a>

                                        <!-- Tags -->
                                        @if($post->tags->count() > 0)
                                            <div class="flex flex-wrap gap-2 mb-4">
                                                @foreach($post->tags as $tag)
                                                    <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 text-xs rounded">
                                                        #{{ $tag->name }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @endif

                                        <!-- Meta -->
                                        <div class="flex items-center justify-between text-sm">
                                            <div class="flex items-center gap-2">
                                                <img src="{{ $post->user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($post->user->name) . '&background=4F46E5&color=fff' }}" 
                                                     alt="{{ $post->user->name }}" 
                                                     class="w-6 h-6 rounded-full">
                                                <a href="{{ route('profile', $post->user->username) }}" class="text-gray-600 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400">
                                                    {{ $post->user->name }}
                                                </a>
                                                <span class="text-gray-400">•</span>
                                                <span class="text-gray-500 dark:text-gray-400">{{ $post->created_at->diffForHumans() }}</span>
                                            </div>
                                            <div class="flex items-center gap-4 text-gray-500 dark:text-gray-400">
                                                <span class="flex items-center gap-1">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                                    </svg>
                                                    {{ $post->comments_count ?? 0 }}
                                                </span>
                                                <span class="flex items-center gap-1">
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
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="mt-8">
                        {{ $posts->links() }}
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <!-- Categories -->
                <div class="bg-white dark:bg-gray-800 rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Categories</h3>
                    <div class="space-y-2">
                        <a href="{{ route('community') }}" 
                           class="flex items-center justify-between p-2 rounded {{ !$categorySlug ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }} transition">
                            <span>All Posts</span>
                        </a>
                        @foreach($categories as $category)
                            <a href="{{ route('community.category', $category->slug) }}" 
                               class="flex items-center justify-between p-2 rounded {{ $categorySlug === $category->slug ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }} transition">
                                <span>{{ $category->name }}</span>
                                <span class="text-sm text-gray-500 dark:text-gray-400">{{ $category->posts_count }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>

                <!-- Quick Links -->
                @auth
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Quick Actions</h3>
                        <div class="space-y-2">
                            <a href="{{ route('post.create') }}" class="block p-2 rounded hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 transition">
                                Create a Post
                            </a>
                            <a href="{{ route('dashboard') }}" class="block p-2 rounded hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 transition">
                                My Dashboard
                            </a>
                            <a href="{{ route('profile', auth()->user()->username) }}" class="block p-2 rounded hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 transition">
                                My Profile
                            </a>
                        </div>
                    </div>
                @endauth
            </div>
        </div>
    </div>
</div>
@endsection
