@extends('layouts.app')

@section('title', 'Reset Password - ' . config('app.name'))

@section('content')
<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div class="text-center">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Forgot your password?</h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">Enter your email and we'll send you a reset link</p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-8">
            @if(session('status'))
                <div class="mb-4 p-4 bg-green-100 dark:bg-green-900/20 text-green-600 dark:text-green-400 rounded-lg">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <div class="space-y-6">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Email Address
                        </label>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               value="{{ old('email') }}"
                               required 
                               autofocus
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('email') border-red-500 @enderror">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="w-full py-3 px-4 bg-primary-600 text-white rounded-lg font-semibold hover:bg-primary-700 transition focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                        Send Reset Link
                    </button>
                </div>
            </form>
        </div>

        <p class="text-center text-gray-600 dark:text-gray-400">
            Remember your password? 
            <a href="{{ route('login') }}" class="text-primary-600 dark:text-primary-400 hover:underline font-medium">
                Sign in
            </a>
        </p>
    </div>
</div>
@endsection
