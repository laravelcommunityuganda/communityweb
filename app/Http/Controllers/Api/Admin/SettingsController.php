<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

class SettingsController extends Controller
{
    /**
     * Get all platform settings.
     */
    public function index(): JsonResponse
    {
        $settings = [
            'general' => [
                'platform_name' => config('app.name', 'Uganda Developers Community'),
                'tagline' => Cache::get('settings.tagline', 'Connect, Learn, and Grow'),
                'contact_email' => Cache::get('settings.contact_email', 'admin@laravelcommunity.ug'),
                'support_url' => Cache::get('settings.support_url', ''),
                'description' => Cache::get('settings.description', ''),
            ],
            'features' => [
                'forum_enabled' => Cache::get('settings.features.forum_enabled', true),
                'jobs_enabled' => Cache::get('settings.features.jobs_enabled', true),
                'events_enabled' => Cache::get('settings.features.events_enabled', true),
                'mentorship_enabled' => Cache::get('settings.features.mentorship_enabled', true),
                'resources_enabled' => Cache::get('settings.features.resources_enabled', true),
                'messaging_enabled' => Cache::get('settings.features.messaging_enabled', true),
            ],
            'content' => [
                'auto_moderate' => Cache::get('settings.content.auto_moderate', false),
                'require_job_approval' => Cache::get('settings.content.require_job_approval', true),
                'require_resource_approval' => Cache::get('settings.content.require_resource_approval', true),
                'banned_words' => Cache::get('settings.content.banned_words', ''),
                'max_post_length' => Cache::get('settings.content.max_post_length', 50000),
                'max_comment_length' => Cache::get('settings.content.max_comment_length', 5000),
                'max_tags_per_post' => Cache::get('settings.content.max_tags_per_post', 5),
                'max_file_size' => Cache::get('settings.content.max_file_size', 10),
            ],
            'email' => [
                'welcome_email' => Cache::get('settings.email.welcome_email', true),
                'reply_notifications' => Cache::get('settings.email.reply_notifications', true),
                'mention_notifications' => Cache::get('settings.email.mention_notifications', true),
                'event_reminders' => Cache::get('settings.email.event_reminders', true),
                'weekly_digest' => Cache::get('settings.email.weekly_digest', false),
            ],
            'security' => [
                'email_verification_required' => Cache::get('settings.security.email_verification_required', true),
                'two_factor_enabled' => Cache::get('settings.security.two_factor_enabled', false),
                'social_login_enabled' => Cache::get('settings.security.social_login_enabled', true),
                'min_password_length' => Cache::get('settings.security.min_password_length', 8),
                'password_reset_expiry' => Cache::get('settings.security.password_reset_expiry', 60),
                'require_mixed_case' => Cache::get('settings.security.require_mixed_case', true),
                'require_numbers' => Cache::get('settings.security.require_numbers', true),
                'require_symbols' => Cache::get('settings.security.require_symbols', false),
                'max_login_attempts' => Cache::get('settings.security.max_login_attempts', 5),
                'lockout_duration' => Cache::get('settings.security.lockout_duration', 15),
            ],
            'maintenance' => [
                'enabled' => app()->isDownForMaintenance(),
                'message' => Cache::get('settings.maintenance.message', ''),
            ],
        ];

        return response()->json([
            'success' => true,
            'data' => $settings,
        ]);
    }

    /**
     * Update platform settings.
     */
    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'general.platform_name' => ['sometimes', 'string', 'max:255'],
            'general.tagline' => ['sometimes', 'string', 'max:255'],
            'general.contact_email' => ['sometimes', 'email', 'max:255'],
            'general.support_url' => ['sometimes', 'url', 'max:255', 'nullable'],
            'general.description' => ['sometimes', 'string', 'max:1000'],
            
            'features.forum_enabled' => ['sometimes', 'boolean'],
            'features.jobs_enabled' => ['sometimes', 'boolean'],
            'features.events_enabled' => ['sometimes', 'boolean'],
            'features.mentorship_enabled' => ['sometimes', 'boolean'],
            'features.resources_enabled' => ['sometimes', 'boolean'],
            'features.messaging_enabled' => ['sometimes', 'boolean'],
            
            'content.auto_moderate' => ['sometimes', 'boolean'],
            'content.require_job_approval' => ['sometimes', 'boolean'],
            'content.require_resource_approval' => ['sometimes', 'boolean'],
            'content.banned_words' => ['sometimes', 'string', 'nullable'],
            'content.max_post_length' => ['sometimes', 'integer', 'min:100', 'max:100000'],
            'content.max_comment_length' => ['sometimes', 'integer', 'min:100', 'max:20000'],
            'content.max_tags_per_post' => ['sometimes', 'integer', 'min:1', 'max:20'],
            'content.max_file_size' => ['sometimes', 'integer', 'min:1', 'max:100'],
            
            'email.welcome_email' => ['sometimes', 'boolean'],
            'email.reply_notifications' => ['sometimes', 'boolean'],
            'email.mention_notifications' => ['sometimes', 'boolean'],
            'email.event_reminders' => ['sometimes', 'boolean'],
            'email.weekly_digest' => ['sometimes', 'boolean'],
            
            'security.email_verification_required' => ['sometimes', 'boolean'],
            'security.two_factor_enabled' => ['sometimes', 'boolean'],
            'security.social_login_enabled' => ['sometimes', 'boolean'],
            'security.min_password_length' => ['sometimes', 'integer', 'min:6', 'max:32'],
            'security.password_reset_expiry' => ['sometimes', 'integer', 'min:5', 'max:1440'],
            'security.require_mixed_case' => ['sometimes', 'boolean'],
            'security.require_numbers' => ['sometimes', 'boolean'],
            'security.require_symbols' => ['sometimes', 'boolean'],
            'security.max_login_attempts' => ['sometimes', 'integer', 'min:1', 'max:20'],
            'security.lockout_duration' => ['sometimes', 'integer', 'min:1', 'max:1440'],
            
            'maintenance.enabled' => ['sometimes', 'boolean'],
            'maintenance.message' => ['sometimes', 'string', 'max:500'],
        ]);

        // Store settings in cache
        foreach ($validated as $group => $settings) {
            foreach ($settings as $key => $value) {
                Cache::forever("settings.{$group}.{$key}", $value);
            }
        }

        // Handle maintenance mode
        if (isset($validated['maintenance']['enabled'])) {
            if ($validated['maintenance']['enabled'] && !app()->isDownForMaintenance()) {
                Artisan::call('down', [
                    '--message' => $validated['maintenance']['message'] ?? 'Maintenance mode',
                    '--retry' => 60,
                ]);
            } elseif (!$validated['maintenance']['enabled'] && app()->isDownForMaintenance()) {
                Artisan::call('up');
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Settings updated successfully',
        ]);
    }

    /**
     * Get system information.
     */
    public function systemInfo(): JsonResponse
    {
        $info = [
            'laravel_version' => app()->version(),
            'php_version' => PHP_VERSION,
            'database' => $this->getDatabaseInfo(),
            'environment' => config('app.env'),
            'debug_mode' => config('app.debug'),
            'url' => config('app.url'),
            'timezone' => config('app.timezone'),
            'cache_driver' => config('cache.default'),
            'queue_driver' => config('queue.default'),
            'session_driver' => config('session.driver'),
        ];

        return response()->json([
            'success' => true,
            'data' => $info,
        ]);
    }

    /**
     * Clear application cache.
     */
    public function clearCache(): JsonResponse
    {
        try {
            Artisan::call('cache:clear');
            
            return response()->json([
                'success' => true,
                'message' => 'Application cache cleared successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear cache: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Clear view cache.
     */
    public function clearViewCache(): JsonResponse
    {
        try {
            Artisan::call('view:clear');
            
            return response()->json([
                'success' => true,
                'message' => 'View cache cleared successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear view cache: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Clear config cache.
     */
    public function clearConfigCache(): JsonResponse
    {
        try {
            Artisan::call('config:clear');
            
            return response()->json([
                'success' => true,
                'message' => 'Config cache cleared successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear config cache: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get database information.
     */
    private function getDatabaseInfo(): string
    {
        $connection = config('database.default');
        $driver = config("database.connections.{$connection}.driver");
        
        try {
            $version = \DB::select('SELECT VERSION() as version')[0]->version ?? 'Unknown';
            return ucfirst($driver) . ' ' . $version;
        } catch (\Exception $e) {
            return ucfirst($driver);
        }
    }
}
