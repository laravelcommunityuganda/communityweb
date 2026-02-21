<?php

use App\Http\Controllers\FrontendController;
use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Home page
Route::get('/', [FrontendController::class, 'home'])->name('home');

// Authentication Routes
Route::get('/login', [FrontendController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [FrontendController::class, 'register'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/forgot-password', [FrontendController::class, 'forgotPassword'])->name('password.request');

// Social Authentication
Route::get('/auth/{provider}', [AuthController::class, 'redirectToProvider'])->name('auth.redirect');
Route::get('/auth/{provider}/callback', [AuthController::class, 'handleProviderCallback'])->name('auth.callback');

// Community Routes
Route::get('/community', [FrontendController::class, 'community'])->name('community');
Route::get('/community/{category}', [FrontendController::class, 'community'])->name('community.category');
Route::get('/post/{slug}', [FrontendController::class, 'postShow'])->name('post.show');

// Jobs Routes
Route::get('/jobs', [FrontendController::class, 'jobs'])->name('jobs');
Route::get('/jobs/{slug}', [FrontendController::class, 'jobShow'])->name('job.show');

// Events Routes
Route::get('/events', [FrontendController::class, 'events'])->name('events');
Route::get('/events/{slug}', [FrontendController::class, 'eventShow'])->name('event.show');

// Resources Routes
Route::get('/resources', [FrontendController::class, 'resources'])->name('resources');
Route::get('/resources/{slug}', [FrontendController::class, 'resourceShow'])->name('resource.show');

// Donations Route
Route::get('/donations', [FrontendController::class, 'donations'])->name('donations');

// Mentors Route
Route::get('/mentors', [FrontendController::class, 'mentors'])->name('mentors');

// Profile Route
Route::get('/profile/{username}', [FrontendController::class, 'profile'])->name('profile');

// Authenticated Routes
Route::middleware(['auth:sanctum'])->group(function () {
    // Post Create/Edit
    Route::get('/post/create', [FrontendController::class, 'postCreate'])->name('post.create');
    Route::get('/post/{slug}/edit', [FrontendController::class, 'postEdit'])->name('post.edit');
    
    // Job Create
    Route::get('/jobs/create', [FrontendController::class, 'jobCreate'])->name('job.create');
    
    // Event Create
    Route::get('/events/create', [FrontendController::class, 'eventCreate'])->name('event.create');
    
    // Dashboard SPA - Catch-all for dashboard routes
    Route::get('/dashboard', [FrontendController::class, 'dashboard'])->name('dashboard');
    Route::get('/dashboard/{any}', [FrontendController::class, 'dashboard'])->where('any', '.*');
    
    // Admin SPA - Catch-all for admin routes
    Route::get('/admin', [FrontendController::class, 'admin'])->name('admin');
    Route::get('/admin/{any}', [FrontendController::class, 'admin'])->where('any', '.*');
    
    // Settings SPA
    Route::get('/settings', [FrontendController::class, 'dashboard'])->name('settings');
    Route::get('/settings/{any}', [FrontendController::class, 'dashboard'])->where('any', '.*');
    
    // Chat SPA
    Route::get('/chat', [FrontendController::class, 'dashboard'])->name('chat');
    Route::get('/chat/{any}', [FrontendController::class, 'dashboard'])->where('any', '.*');
    
    // Notifications SPA
    Route::get('/notifications', [FrontendController::class, 'dashboard'])->name('notifications');
});
