@extends('layouts.app')

@section('title', $job->title . ' at ' . $job->company . ' - ' . config('app.name'))

@section('content')
<div class="min-h-screen py-8">
    <div class="container mx-auto px-4">
        <!-- Back Button -->
        <a href="{{ route('jobs') }}" class="inline-flex items-center text-gray-600 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 mb-6">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Jobs
        </a>

        <div class="grid lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden">
                    <!-- Header -->
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-start gap-4">
                            <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center flex-shrink-0">
                                @if($job->company_logo)
                                    <img src="{{ $job->company_logo }}" alt="{{ $job->company }}" class="w-12 h-12 rounded">
                                @else
                                    <span class="text-2xl font-bold text-gray-400">{{ strtoupper(substr($job->company, 0, 1)) }}</span>
                                @endif
                            </div>
                            <div class="flex-1">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $job->title }}</h1>
                                        <p class="text-lg text-gray-600 dark:text-gray-400">{{ $job->company }}</p>
                                    </div>
                                    @if($job->is_featured)
                                        <span class="px-3 py-1 bg-yellow-100 dark:bg-yellow-900 text-yellow-600 dark:text-yellow-400 text-sm rounded">
                                            Featured
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Job Details -->
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">Location</div>
                                <div class="font-medium text-gray-900 dark:text-white">{{ $job->location }}</div>
                            </div>
                            <div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">Type</div>
                                <div class="font-medium text-gray-900 dark:text-white">{{ ucfirst($job->type) }}</div>
                            </div>
                            <div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">Experience</div>
                                <div class="font-medium text-gray-900 dark:text-white">{{ $job->experience_level ?? 'Not specified' }}</div>
                            </div>
                            <div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">Posted</div>
                                <div class="font-medium text-gray-900 dark:text-white">{{ $job->created_at->diffForHumans() }}</div>
                            </div>
                        </div>

                        @if($job->salary_min || $job->salary_max)
                            <div class="mt-4 p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
                                <div class="text-sm text-green-600 dark:text-green-400">Salary</div>
                                <div class="text-lg font-semibold text-green-700 dark:text-green-300">
                                    @if($job->salary_min && $job->salary_max)
                                        {{ number_format($job->salary_min) }} - {{ number_format($job->salary_max) }} {{ $job->salary_currency }}/year
                                    @elseif($job->salary_min)
                                        From {{ number_format($job->salary_min) }} {{ $job->salary_currency }}/year
                                    @else
                                        Up to {{ number_format($job->salary_max) }} {{ $job->salary_currency }}/year
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Description -->
                    <div class="p-6">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Job Description</h2>
                        <div class="prose dark:prose-invert max-w-none">
                            {!! $job->description !!}
                        </div>

                        @if($job->requirements)
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mt-6 mb-4">Requirements</h2>
                            <div class="prose dark:prose-invert max-w-none">
                                {!! $job->requirements !!}
                            </div>
                        @endif

                        @if($job->benefits)
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mt-6 mb-4">Benefits</h2>
                            <div class="prose dark:prose-invert max-w-none">
                                {!! $job->benefits !!}
                            </div>
                        @endif

                        <!-- Tags -->
                        @if($job->tags && count($job->tags) > 0)
                            <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Skills</h3>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($job->tags as $tag)
                                        <span class="px-3 py-1 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 text-sm rounded-full">
                                            {{ $tag }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <!-- Apply Button -->
                <div class="bg-white dark:bg-gray-800 rounded-lg p-6 mb-6">
                    @auth
                        @if($job->apply_url)
                            <a href="{{ $job->apply_url }}" target="_blank" class="block w-full py-3 px-4 bg-primary-600 text-white text-center rounded-lg font-semibold hover:bg-primary-700 transition">
                                Apply Now
                            </a>
                        @else
                            <form action="{{ route('job.apply', $job->slug) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full py-3 px-4 bg-primary-600 text-white rounded-lg font-semibold hover:bg-primary-700 transition">
                                    Apply Now
                                </button>
                            </form>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="block w-full py-3 px-4 bg-primary-600 text-white text-center rounded-lg font-semibold hover:bg-primary-700 transition">
                            Sign in to Apply
                        </a>
                    @endauth

                    <div class="mt-4 text-center text-sm text-gray-500 dark:text-gray-400">
                        Applications close {{ $job->deadline ? $job->deadline->diffForHumans() : 'Open' }}
                    </div>
                </div>

                <!-- Job Info -->
                <div class="bg-white dark:bg-gray-800 rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Job Overview</h3>
                    <div class="space-y-4">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">Location</div>
                                <div class="text-gray-900 dark:text-white">{{ $job->location }}</div>
                            </div>
                        </div>
                        @if($job->is_remote)
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064"/>
                                </svg>
                                <div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">Work Type</div>
                                    <div class="text-green-600 dark:text-green-400">Remote Available</div>
                                </div>
                            </div>
                        @endif
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">Job Type</div>
                                <div class="text-gray-900 dark:text-white">{{ ucfirst($job->type) }}</div>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">Posted</div>
                                <div class="text-gray-900 dark:text-white">{{ $job->created_at->format('M d, Y') }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Company Info -->
                <div class="bg-white dark:bg-gray-800 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">About {{ $job->company }}</h3>
                    @if($job->company_description)
                        <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">{{ $job->company_description }}</p>
                    @endif
                    @if($job->company_website)
                        <a href="{{ $job->company_website }}" target="_blank" class="text-primary-600 dark:text-primary-400 hover:underline text-sm">
                            Visit Company Website →
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
