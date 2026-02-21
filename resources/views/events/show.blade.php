@extends('layouts.app')

@section('title', $event->title . ' - ' . config('app.name'))

@section('content')
<div class="min-h-screen py-8">
    <div class="container mx-auto px-4">
        <!-- Back Button -->
        <a href="{{ route('events') }}" class="inline-flex items-center text-gray-600 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 mb-6">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Events
        </a>

        <div class="grid lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden">
                    <!-- Event Image -->
                    @if($event->image)
                        <img src="{{ $event->image }}" alt="{{ $event->title }}" class="w-full h-64 object-cover">
                    @else
                        <div class="w-full h-64 bg-gradient-to-r from-primary-500 to-primary-700 flex items-center justify-center">
                            <svg class="w-24 h-24 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    @endif

                    <div class="p-6">
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">{{ $event->title }}</h1>

                        <div class="prose dark:prose-invert max-w-none">
                            {!! $event->description !!}
                        </div>

                        @if($event->agenda)
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mt-6 mb-4">Agenda</h2>
                            <div class="prose dark:prose-invert max-w-none">
                                {!! $event->agenda !!}
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <!-- RSVP -->
                <div class="bg-white dark:bg-gray-800 rounded-lg p-6 mb-6">
                    @auth
                        <form action="{{ route('event.rsvp', $event->slug) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full py-3 px-4 bg-primary-600 text-white rounded-lg font-semibold hover:bg-primary-700 transition">
                                RSVP Now
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="block w-full py-3 px-4 bg-primary-600 text-white text-center rounded-lg font-semibold hover:bg-primary-700 transition">
                            Sign in to RSVP
                        </a>
                    @endauth

                    <div class="mt-4 text-center text-sm text-gray-500 dark:text-gray-400">
                        {{ $event->attendees_count ?? 0 }} people attending
                    </div>
                </div>

                <!-- Event Details -->
                <div class="bg-white dark:bg-gray-800 rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Event Details</h3>
                    <div class="space-y-4">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">Date & Time</div>
                                <div class="text-gray-900 dark:text-white">{{ $event->start_date->format('l, F j, Y') }}</div>
                                <div class="text-gray-900 dark:text-white">{{ $event->start_date->format('g:i A') }} - {{ $event->end_date->format('g:i A') }}</div>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">Location</div>
                                @if($event->is_virtual)
                                    <div class="text-primary-600 dark:text-primary-400">Virtual Event</div>
                                    @if($event->meeting_url)
                                        <a href="{{ $event->meeting_url }}" target="_blank" class="text-sm text-primary-600 dark:text-primary-400 hover:underline">
                                            Join Meeting →
                                        </a>
                                    @endif
                                @else
                                    <div class="text-gray-900 dark:text-white">{{ $event->location }}</div>
                                @endif
                            </div>
                        </div>

                        @if($event->capacity)
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                <div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">Capacity</div>
                                    <div class="text-gray-900 dark:text-white">{{ $event->attendees_count ?? 0 }} / {{ $event->capacity }}</div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Organizer -->
                <div class="bg-white dark:bg-gray-800 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Organizer</h3>
                    <div class="flex items-center gap-3">
                        <img src="{{ $event->user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($event->user->name) . '&background=4F46E5&color=fff' }}" 
                             alt="{{ $event->user->name }}" 
                             class="w-12 h-12 rounded-full">
                        <div>
                            <a href="{{ route('profile', $event->user->username) }}" class="font-medium text-gray-900 dark:text-white hover:text-primary-600 dark:hover:text-primary-400">
                                {{ $event->user->name }}
                            </a>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Organizer</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
