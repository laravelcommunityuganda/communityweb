<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="description" content="Dashboard - Uganda Developers Community">
        
        <!-- PWA Meta Tags -->
        <meta name="theme-color" content="#4F46E5">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
        <meta name="apple-mobile-web-app-title" content="LaravelUG">
        
        <title>Dashboard - {{ config('app.name') }}</title>

        <!-- Favicon -->
        <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
        
        <!-- PWA Manifest -->
        <link rel="manifest" href="/manifest.json">
        
        <!-- Apple Touch Icons -->
        <link rel="apple-touch-icon" sizes="192x192" href="/icons/icon-192x192.png">
        <link rel="apple-touch-icon" sizes="512x512" href="/icons/icon-512x512.png">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/dashboard.js'])
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
        
        <!-- Service Worker Registration -->
        <script>
            if ('serviceWorker' in navigator) {
                window.addEventListener('load', () => {
                    navigator.serviceWorker.register('/sw.js')
                        .then(registration => console.log('SW registered:', registration.scope))
                        .catch(error => console.log('SW registration failed:', error));
                });
            }
        </script>
    </body>
</html>
