@extends('layouts.app')

@section('title', 'Jobs - ' . config('app.name'))

@section('content')
<div class="min-h-screen py-8">
    <div class="container mx-auto px-4">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Jobs</h1>
            @auth
                <a href="{{ route('job.create') }}" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition">
                    Post a Job
                </a>
            @endauth
        </div>

        <!-- Filters -->
        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 mb-6">
            <form method="GET" class="flex flex-wrap gap-4">
                <div class="flex-1 min-w-[200px]">
                    <input type="text" 
                           name="search" 
                           placeholder="Search jobs..." 
                           value="{{ request('search') }}"
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                </div>
                <select name="type" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500">
                    <option value="">All Types</option>
                    <option value="full-time" {{ request('type') === 'full-time' ? 'selected' : '' }}>Full-time</option>
                    <option value="part-time" {{ request('type') === 'part-time' ? 'selected' : '' }}>Part-time</option>
                    <option value="contract" {{ request('type') === 'contract' ? 'selected' : '' }}>Contract</option>
                    <option value="freelance" {{ request('type') === 'freelance' ? 'selected' : '' }}>Freelance</option>
                    <option value="internship" {{ request('type') === 'internship' ? 'selected' : '' }}>Internship</option>
                </select>
                <input type="text" 
                       name="location" 
                       placeholder="Location" 
                       value="{{ request('location') }}"
                       class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500">
                <label class="flex items-center gap-2 text-gray-700 dark:text-gray-300">
                    <input type="checkbox" name="remote" value="1" {{ request('remote') ? 'checked' : '' }} class="rounded text-primary-600 focus:ring-primary-500">
                    Remote
                </label>
                <button type="submit" class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                    Filter
                </button>
            </form>
        </div>

        <!-- Jobs List -->
        @if($jobs->isEmpty())
            <div class="bg-white dark:bg-gray-800 rounded-lg p-8 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No jobs found</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-4">Try adjusting your search filters</p>
            </div>
        @else
            <div class="space-y-4">
                @foreach($jobs as $job)
                    <a href="{{ route('job.show', $job->slug) }}" class="block bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm hover:shadow-md transition">
                        <div class="flex items-start gap-4">
                            <!-- Company Logo -->
                            <div class="w-12 h-12 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center flex-shrink-0">
                                @if($job->company_logo)
                                    <img src="{{ $job->company_logo }}" alt="{{ $job->company }}" class="w-10 h-10 rounded">
                                @else
                                    <span class="text-xl font-bold text-gray-400">{{ strtoupper(substr($job->company, 0, 1)) }}</span>
                                @endif
                            </div>

                            <!-- Job Info -->
                            <div class="flex-1">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white hover:text-primary-600 dark:hover:text-primary-400">
                                            {{ $job->title }}
                                        </h2>
                                        <p class="text-gray-600 dark:text-gray-400">{{ $job->company }}</p>
                                    </div>
                                    @if($job->is_featured)
                                        <span class="px-2 py-1 bg-yellow-100 dark:bg-yellow-900 text-yellow-600 dark:text-yellow-400 text-xs rounded">
                                            Featured
                                        </span>
                                    @endif
                                </div>

                                <div class="flex flex-wrap items-center gap-4 mt-3 text-sm text-gray-500 dark:text-gray-400">
                                    <span class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        {{ $job->location }}
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        {{ ucfirst($job->type) }}
                                    </span>
                                    @if($job->salary_min || $job->salary_max)
                                        <span class="flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            @if($job->salary_min && $job->salary_max)
                                                {{ number_format($job->salary_min) }} - {{ number_format($job->salary_max) }} {{ $job->salary_currency }}
                                            @elseif($job->salary_min)
                                                From {{ number_format($job->salary_min) }} {{ $job->salary_currency }}
                                            @else
                                                Up to {{ number_format($job->salary_max) }} {{ $job->salary_currency }}
                                            @endif
                                        </span>
                                    @endif
                                    @if($job->is_remote)
                                        <span class="flex items-center gap-1 text-green-600 dark:text-green-400">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064"/>
                                            </svg>
                                            Remote
                                        </span>
                                    @endif
                                    <span class="text-gray-400">{{ $job->created_at->diffForHumans() }}</span>
                                </div>

                                <!-- Tags -->
                                @if($job->tags && count($job->tags) > 0)
                                    <div class="flex flex-wrap gap-2 mt-3">
                                        @foreach($job->tags as $tag)
                                            <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 text-xs rounded">
                                                {{ $tag }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $jobs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
