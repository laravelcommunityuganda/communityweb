<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="description" content="Uganda Developers Community - A platform for developers in Uganda to connect, learn, and grow together.">
        <meta name="keywords" content="Uganda, developers, programming, software engineering, Laravel, PHP, Vue.js, JavaScript">
        <meta name="author" content="Uganda Developers Community">
        
        <!-- Open Graph / Facebook -->
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:title" content="{{ config('app.name') }}">
        <meta property="og:description" content="A platform for developers in Uganda to connect, learn, and grow together.">
        <meta property="og:image" content="{{ asset('images/og-image.png') }}">

        <!-- Twitter -->
        <meta property="twitter:card" content="summary_large_image">
        <meta property="twitter:url" content="{{ url()->current() }}">
        <meta property="twitter:title" content="{{ config('app.name') }}">
        <meta property="twitter:description" content="A platform for developers in Uganda to connect, learn, and grow together.">
        <meta property="twitter:image" content="{{ asset('images/og-image.png') }}">

        <title>{{ config('app.name') }}</title>

        <!-- Favicon -->
        <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased">
        <div id="app"></div>

        <!-- NoScript fallback -->
        <noscript>
            <div class="flex items-center justify-center min-h-screen bg-gray-100">
                <div class="text-center p-8">
                    <h1 class="text-2xl font-bold text-gray-900 mb-4">JavaScript Required</h1>
                    <p class="text-gray-600">Please enable JavaScript to use this application.</p>
                </div>
            </div>
        </noscript>
    </body>
</html>