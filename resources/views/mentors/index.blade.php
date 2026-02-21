@extends('layouts.app')

@section('title', 'Find a Mentor - ' . config('app.name'))

@section('content')
<div class="min-h-screen py-8">
    <div class="container mx-auto px-4">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">Find a Mentor</h1>
            <p class="text-lg text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
                Connect with experienced developers who can guide you on your journey
            </p>
        </div>

        <!-- Filters -->
        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 mb-6">
            <form method="GET" class="flex flex-wrap gap-4">
                <div class="flex-1 min-w-[200px]">
                    <input type="text" 
                           name="search" 
                           placeholder="Search mentors..." 
                           value="{{ request('search') }}"
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                </div>
                <input type="text" 
                       name="skill" 
                       placeholder="Skill (e.g., Laravel, Vue)" 
                       value="{{ request('skill') }}"
                       class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500">
                <button type="submit" class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                    Search
                </button>
            </form>
        </div>

        <!-- Mentors List -->
        @if($mentors->isEmpty())
            <div class="bg-white dark:bg-gray-800 rounded-lg p-8 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No mentors found</h3>
                <p class="text-gray-600 dark:text-gray-400">Try adjusting your search criteria</p>
            </div>
        @else
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($mentors as $mentor)
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm hover:shadow-md transition overflow-hidden">
                        <div class="p-6">
                            <!-- Mentor Header -->
                            <div class="flex items-center gap-4 mb-4">
                                <img src="{{ $mentor->user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($mentor->user->name) . '&background=4F46E5&color=fff' }}" 
                                     alt="{{ $mentor->user->name }}" 
                                     class="w-16 h-16 rounded-full">
                                <div>
                                    <a href="{{ route('profile', $mentor->user->username) }}" class="font-semibold text-gray-900 dark:text-white hover:text-primary-600 dark:hover:text-primary-400">
                                        {{ $mentor->user->name }}
                                    </a>
                                    @if($mentor->title)
                                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $mentor->title }}</p>
                                    @endif
                                    <div class="flex items-center gap-1 mt-1">
                                        <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ number_format($mentor->rating ?? 0, 1) }}</span>
                                        <span class="text-sm text-gray-400">({{ $mentor->sessions_count ?? 0 }} sessions)</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Bio -->
                            @if($mentor->bio)
                                <p class="text-gray-600 dark:text-gray-400 text-sm mb-4 line-clamp-3">
                                    {{ Str::limit($mentor->bio, 150) }}
                                </p>
                            @endif

                            <!-- Skills -->
                            @if($mentor->skills->count() > 0)
                                <div class="flex flex-wrap gap-2 mb-4">
                                    @foreach($mentor->skills->take(4) as $skill)
                                        <span class="px-2 py-1 bg-primary-100 dark:bg-primary-900 text-primary-600 dark:text-primary-400 text-xs rounded">
                                            {{ $skill->name }}
                                        </span>
                                    @endforeach
                                    @if($mentor->skills->count() > 4)
                                        <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 text-xs rounded">
                                            +{{ $mentor->skills->count() - 4 }} more
                                        </span>
                                    @endif
                                </div>
                            @endif

                            <!-- Availability -->
                            <div class="flex items-center justify-between">
                                <span class="flex items-center gap-1 text-sm {{ $mentor->is_available ? 'text-green-600 dark:text-green-400' : 'text-gray-400' }}">
                                    <span class="w-2 h-2 rounded-full {{ $mentor->is_available ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                                    {{ $mentor->is_available ? 'Available' : 'Unavailable' }}
                                </span>
                                @auth
                                    <a href="{{ route('dashboard') }}?mentor={{ $mentor->id }}" class="text-primary-600 dark:text-primary-400 hover:underline text-sm">
                                        Request Session
                                    </a>
                                @else
                                    <a href="{{ route('login') }}" class="text-primary-600 dark:text-primary-400 hover:underline text-sm">
                                        Sign in to Request
                                    </a>
                                @endauth
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $mentors->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
