@extends('layouts.app')

@section('title', $resource->title . ' - ' . config('app.name'))

@section('content')
<div class="min-h-screen py-8">
    <div class="container mx-auto px-4">
        <!-- Back Button -->
        <a href="{{ route('resources') }}" class="inline-flex items-center text-gray-600 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 mb-6">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Resources
        </a>

        <div class="grid lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden">
                    <div class="p-6">
                        <!-- Type Badge -->
                        <div class="flex items-center gap-2 mb-4">
                            <span class="px-2 py-1 bg-primary-100 dark:bg-primary-900 text-primary-600 dark:text-primary-400 text-xs rounded uppercase">
                                {{ $resource->type }}
                            </span>
                            @if($resource->is_free)
                                <span class="px-2 py-1 bg-green-100 dark:bg-green-900 text-green-600 dark:text-green-400 text-xs rounded">
                                    Free
                                </span>
                            @elseif($resource->price)
                                <span class="px-2 py-1 bg-yellow-100 dark:bg-yellow-900 text-yellow-600 dark:text-yellow-400 text-xs rounded">
                                    ${{ number_format($resource->price, 2) }}
                                </span>
                            @endif
                        </div>

                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">{{ $resource->title }}</h1>

                        <div class="prose dark:prose-invert max-w-none">
                            {!! $resource->description !!}
                        </div>

                        @if($resource->content)
                            <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Content</h2>
                                <div class="prose dark:prose-invert max-w-none">
                                    {!! $resource->content !!}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <!-- Download -->
                <div class="bg-white dark:bg-gray-800 rounded-lg p-6 mb-6">
                    @auth
                        <a href="{{ route('resource.download', $resource->slug) }}" class="block w-full py-3 px-4 bg-primary-600 text-white text-center rounded-lg font-semibold hover:bg-primary-700 transition">
                            Download Resource
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="block w-full py-3 px-4 bg-primary-600 text-white text-center rounded-lg font-semibold hover:bg-primary-700 transition">
                            Sign in to Download
                        </a>
                    @endauth

                    <div class="mt-4 text-center text-sm text-gray-500 dark:text-gray-400">
                        {{ $resource->downloads_count ?? 0 }} downloads
                    </div>
                </div>

                <!-- Resource Info -->
                <div class="bg-white dark:bg-gray-800 rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Resource Info</h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500 dark:text-gray-400">Type</span>
                            <span class="text-gray-900 dark:text-white capitalize">{{ $resource->type }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500 dark:text-gray-400">Rating</span>
                            <div class="flex items-center gap-1">
                                <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                {{ number_format($resource->rating ?? 0, 1) }}
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500 dark:text-gray-400">Added</span>
                            <span class="text-gray-900 dark:text-white">{{ $resource->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                </div>

                <!-- Author -->
                <div class="bg-white dark:bg-gray-800 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Added By</h3>
                    <div class="flex items-center gap-3">
                        <img src="{{ $resource->user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($resource->user->name) . '&background=4F46E5&color=fff' }}" 
                             alt="{{ $resource->user->name }}" 
                             class="w-12 h-12 rounded-full">
                        <div>
                            <a href="{{ route('profile', $resource->user->username) }}" class="font-medium text-gray-900 dark:text-white hover:text-primary-600 dark:hover:text-primary-400">
                                {{ $resource->user->name }}
                            </a>
                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $resource->user->reputation ?? 0 }} reputation</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
