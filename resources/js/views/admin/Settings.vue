<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Platform Settings</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Configure your community platform settings</p>
      </div>
      <button 
        @click="saveSettings" 
        :disabled="saving"
        class="px-4 py-2 bg-laravel-500 text-white rounded-lg hover:bg-laravel-600 transition disabled:opacity-50"
      >
        <span v-if="saving">Saving...</span>
        <span v-else>Save Changes</span>
      </button>
    </div>

    <!-- Settings Tabs -->
    <div class="border-b border-gray-200 dark:border-gray-700">
      <nav class="flex space-x-8">
        <button 
          v-for="tab in tabs" 
          :key="tab.id"
          @click="activeTab = tab.id"
          :class="[
            'py-4 px-1 border-b-2 font-medium text-sm transition',
            activeTab === tab.id
              ? 'border-laravel-500 text-laravel-600 dark:text-laravel-400'
              : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'
          ]"
        >
          {{ tab.name }}
        </button>
      </nav>
    </div>

    <!-- General Settings -->
    <div v-show="activeTab === 'general'" class="space-y-6">
      <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">General Settings</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Platform Name
            </label>
            <input 
              v-model="settings.general.platform_name"
              type="text"
              class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-laravel-500 focus:border-laravel-500"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Platform Tagline
            </label>
            <input 
              v-model="settings.general.tagline"
              type="text"
              class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-laravel-500 focus:border-laravel-500"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Contact Email
            </label>
            <input 
              v-model="settings.general.contact_email"
              type="email"
              class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-laravel-500 focus:border-laravel-500"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Support URL
            </label>
            <input 
              v-model="settings.general.support_url"
              type="url"
              class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-laravel-500 focus:border-laravel-500"
            />
          </div>
          <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Platform Description
            </label>
            <textarea 
              v-model="settings.general.description"
              rows="3"
              class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-laravel-500 focus:border-laravel-500"
            ></textarea>
          </div>
        </div>
      </div>

      <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Feature Flags</h2>
        <div class="space-y-4">
          <div class="flex items-center justify-between">
            <div>
              <p class="font-medium text-gray-900 dark:text-white">Enable Forum</p>
              <p class="text-sm text-gray-500 dark:text-gray-400">Allow users to create posts and discussions</p>
            </div>
            <button 
              @click="settings.features.forum_enabled = !settings.features.forum_enabled"
              :class="[
                'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out',
                settings.features.forum_enabled ? 'bg-laravel-500' : 'bg-gray-200 dark:bg-gray-600'
              ]"
            >
              <span 
                :class="[
                  'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out',
                  settings.features.forum_enabled ? 'translate-x-5' : 'translate-x-0'
                ]"
              ></span>
            </button>
          </div>
          <div class="flex items-center justify-between">
            <div>
              <p class="font-medium text-gray-900 dark:text-white">Enable Jobs Board</p>
              <p class="text-sm text-gray-500 dark:text-gray-400">Allow job postings and applications</p>
            </div>
            <button 
              @click="settings.features.jobs_enabled = !settings.features.jobs_enabled"
              :class="[
                'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out',
                settings.features.jobs_enabled ? 'bg-laravel-500' : 'bg-gray-200 dark:bg-gray-600'
              ]"
            >
              <span 
                :class="[
                  'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out',
                  settings.features.jobs_enabled ? 'translate-x-5' : 'translate-x-0'
                ]"
              ></span>
            </button>
          </div>
          <div class="flex items-center justify-between">
            <div>
              <p class="font-medium text-gray-900 dark:text-white">Enable Events</p>
              <p class="text-sm text-gray-500 dark:text-gray-400">Allow event creation and RSVPs</p>
            </div>
            <button 
              @click="settings.features.events_enabled = !settings.features.events_enabled"
              :class="[
                'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out',
                settings.features.events_enabled ? 'bg-laravel-500' : 'bg-gray-200 dark:bg-gray-600'
              ]"
            >
              <span 
                :class="[
                  'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out',
                  settings.features.events_enabled ? 'translate-x-5' : 'translate-x-0'
                ]"
              ></span>
            </button>
          </div>
          <div class="flex items-center justify-between">
            <div>
              <p class="font-medium text-gray-900 dark:text-white">Enable Mentorship</p>
              <p class="text-sm text-gray-500 dark:text-gray-400">Allow mentorship program</p>
            </div>
            <button 
              @click="settings.features.mentorship_enabled = !settings.features.mentorship_enabled"
              :class="[
                'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out',
                settings.features.mentorship_enabled ? 'bg-laravel-500' : 'bg-gray-200 dark:bg-gray-600'
              ]"
            >
              <span 
                :class="[
                  'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out',
                  settings.features.mentorship_enabled ? 'translate-x-5' : 'translate-x-0'
                ]"
              ></span>
            </button>
          </div>
          <div class="flex items-center justify-between">
            <div>
              <p class="font-medium text-gray-900 dark:text-white">Enable Resources</p>
              <p class="text-sm text-gray-500 dark:text-gray-400">Allow resource sharing</p>
            </div>
            <button 
              @click="settings.features.resources_enabled = !settings.features.resources_enabled"
              :class="[
                'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out',
                settings.features.resources_enabled ? 'bg-laravel-500' : 'bg-gray-200 dark:bg-gray-600'
              ]"
            >
              <span 
                :class="[
                  'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out',
                  settings.features.resources_enabled ? 'translate-x-5' : 'translate-x-0'
                ]"
              ></span>
            </button>
          </div>
          <div class="flex items-center justify-between">
            <div>
              <p class="font-medium text-gray-900 dark:text-white">Enable Private Messaging</p>
              <p class="text-sm text-gray-500 dark:text-gray-400">Allow users to send private messages</p>
            </div>
            <button 
              @click="settings.features.messaging_enabled = !settings.features.messaging_enabled"
              :class="[
                'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out',
                settings.features.messaging_enabled ? 'bg-laravel-500' : 'bg-gray-200 dark:bg-gray-600'
              ]"
            >
              <span 
                :class="[
                  'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out',
                  settings.features.messaging_enabled ? 'translate-x-5' : 'translate-x-0'
                ]"
              ></span>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Content Settings -->
    <div v-show="activeTab === 'content'" class="space-y-6">
      <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Content Moderation</h2>
        <div class="space-y-4">
          <div class="flex items-center justify-between">
            <div>
              <p class="font-medium text-gray-900 dark:text-white">Auto-moderate Posts</p>
              <p class="text-sm text-gray-500 dark:text-gray-400">Automatically moderate new posts for spam</p>
            </div>
            <button 
              @click="settings.content.auto_moderate = !settings.content.auto_moderate"
              :class="[
                'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out',
                settings.content.auto_moderate ? 'bg-laravel-500' : 'bg-gray-200 dark:bg-gray-600'
              ]"
            >
              <span 
                :class="[
                  'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out',
                  settings.content.auto_moderate ? 'translate-x-5' : 'translate-x-0'
                ]"
              ></span>
            </button>
          </div>
          <div class="flex items-center justify-between">
            <div>
              <p class="font-medium text-gray-900 dark:text-white">Require Job Approval</p>
              <p class="text-sm text-gray-500 dark:text-gray-400">Jobs require admin approval before publishing</p>
            </div>
            <button 
              @click="settings.content.require_job_approval = !settings.content.require_job_approval"
              :class="[
                'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out',
                settings.content.require_job_approval ? 'bg-laravel-500' : 'bg-gray-200 dark:bg-gray-600'
              ]"
            >
              <span 
                :class="[
                  'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out',
                  settings.content.require_job_approval ? 'translate-x-5' : 'translate-x-0'
                ]"
              ></span>
            </button>
          </div>
          <div class="flex items-center justify-between">
            <div>
              <p class="font-medium text-gray-900 dark:text-white">Require Resource Approval</p>
              <p class="text-sm text-gray-500 dark:text-gray-400">Resources require admin approval before publishing</p>
            </div>
            <button 
              @click="settings.content.require_resource_approval = !settings.content.require_resource_approval"
              :class="[
                'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out',
                settings.content.require_resource_approval ? 'bg-laravel-500' : 'bg-gray-200 dark:bg-gray-600'
              ]"
            >
              <span 
                :class="[
                  'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out',
                  settings.content.require_resource_approval ? 'translate-x-5' : 'translate-x-0'
                ]"
              ></span>
            </button>
          </div>
        </div>
      </div>

      <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Banned Words</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
          Words that will be automatically filtered from posts and comments
        </p>
        <textarea 
          v-model="settings.content.banned_words"
          rows="4"
          placeholder="Enter banned words separated by commas..."
          class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-laravel-500 focus:border-laravel-500"
        ></textarea>
      </div>

      <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Content Limits</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Max Post Length
            </label>
            <input 
              v-model="settings.content.max_post_length"
              type="number"
              class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-laravel-500 focus:border-laravel-500"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Max Comment Length
            </label>
            <input 
              v-model="settings.content.max_comment_length"
              type="number"
              class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-laravel-500 focus:border-laravel-500"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Max Tags Per Post
            </label>
            <input 
              v-model="settings.content.max_tags_per_post"
              type="number"
              class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-laravel-500 focus:border-laravel-500"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Max File Upload Size (MB)
            </label>
            <input 
              v-model="settings.content.max_file_size"
              type="number"
              class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-laravel-500 focus:border-laravel-500"
            />
          </div>
        </div>
      </div>
    </div>

    <!-- Email Settings -->
    <div v-show="activeTab === 'email'" class="space-y-6">
      <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Email Notifications</h2>
        <div class="space-y-4">
          <div class="flex items-center justify-between">
            <div>
              <p class="font-medium text-gray-900 dark:text-white">Welcome Email</p>
              <p class="text-sm text-gray-500 dark:text-gray-400">Send welcome email to new users</p>
            </div>
            <button 
              @click="settings.email.welcome_email = !settings.email.welcome_email"
              :class="[
                'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out',
                settings.email.welcome_email ? 'bg-laravel-500' : 'bg-gray-200 dark:bg-gray-600'
              ]"
            >
              <span 
                :class="[
                  'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out',
                  settings.email.welcome_email ? 'translate-x-5' : 'translate-x-0'
                ]"
              ></span>
            </button>
          </div>
          <div class="flex items-center justify-between">
            <div>
              <p class="font-medium text-gray-900 dark:text-white">Post Reply Notifications</p>
              <p class="text-sm text-gray-500 dark:text-gray-400">Send email when someone replies to a post</p>
            </div>
            <button 
              @click="settings.email.reply_notifications = !settings.email.reply_notifications"
              :class="[
                'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out',
                settings.email.reply_notifications ? 'bg-laravel-500' : 'bg-gray-200 dark:bg-gray-600'
              ]"
            >
              <span 
                :class="[
                  'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out',
                  settings.email.reply_notifications ? 'translate-x-5' : 'translate-x-0'
                ]"
              ></span>
            </button>
          </div>
          <div class="flex items-center justify-between">
            <div>
              <p class="font-medium text-gray-900 dark:text-white">Mention Notifications</p>
              <p class="text-sm text-gray-500 dark:text-gray-400">Send email when user is mentioned</p>
            </div>
            <button 
              @click="settings.email.mention_notifications = !settings.email.mention_notifications"
              :class="[
                'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out',
                settings.email.mention_notifications ? 'bg-laravel-500' : 'bg-gray-200 dark:bg-gray-600'
              ]"
            >
              <span 
                :class="[
                  'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out',
                  settings.email.mention_notifications ? 'translate-x-5' : 'translate-x-0'
                ]"
              ></span>
            </button>
          </div>
          <div class="flex items-center justify-between">
            <div>
              <p class="font-medium text-gray-900 dark:text-white">Event Reminders</p>
              <p class="text-sm text-gray-500 dark:text-gray-400">Send reminders before events</p>
            </div>
            <button 
              @click="settings.email.event_reminders = !settings.email.event_reminders"
              :class="[
                'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out',
                settings.email.event_reminders ? 'bg-laravel-500' : 'bg-gray-200 dark:bg-gray-600'
              ]"
            >
              <span 
                :class="[
                  'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out',
                  settings.email.event_reminders ? 'translate-x-5' : 'translate-x-0'
                ]"
              ></span>
            </button>
          </div>
          <div class="flex items-center justify-between">
            <div>
              <p class="font-medium text-gray-900 dark:text-white">Weekly Digest</p>
              <p class="text-sm text-gray-500 dark:text-gray-400">Send weekly activity summary</p>
            </div>
            <button 
              @click="settings.email.weekly_digest = !settings.email.weekly_digest"
              :class="[
                'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out',
                settings.email.weekly_digest ? 'bg-laravel-500' : 'bg-gray-200 dark:bg-gray-600'
              ]"
            >
              <span 
                :class="[
                  'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out',
                  settings.email.weekly_digest ? 'translate-x-5' : 'translate-x-0'
                ]"
              ></span>
            </button>
          </div>
        </div>
      </div>

      <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Email Templates</h2>
        <div class="space-y-4">
          <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
            <div>
              <p class="font-medium text-gray-900 dark:text-white">Welcome Email Template</p>
              <p class="text-sm text-gray-500 dark:text-gray-400">Sent to new users after registration</p>
            </div>
            <button class="px-3 py-1 text-sm text-laravel-500 hover:text-laravel-600">
              Edit Template
            </button>
          </div>
          <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
            <div>
              <p class="font-medium text-gray-900 dark:text-white">Password Reset Template</p>
              <p class="text-sm text-gray-500 dark:text-gray-400">Sent when user requests password reset</p>
            </div>
            <button class="px-3 py-1 text-sm text-laravel-500 hover:text-laravel-600">
              Edit Template
            </button>
          </div>
          <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
            <div>
              <p class="font-medium text-gray-900 dark:text-white">Notification Template</p>
              <p class="text-sm text-gray-500 dark:text-gray-400">Base template for all notifications</p>
            </div>
            <button class="px-3 py-1 text-sm text-laravel-500 hover:text-laravel-600">
              Edit Template
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Security Settings -->
    <div v-show="activeTab === 'security'" class="space-y-6">
      <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Authentication Settings</h2>
        <div class="space-y-4">
          <div class="flex items-center justify-between">
            <div>
              <p class="font-medium text-gray-900 dark:text-white">Email Verification Required</p>
              <p class="text-sm text-gray-500 dark:text-gray-400">Users must verify email before accessing platform</p>
            </div>
            <button 
              @click="settings.security.email_verification_required = !settings.security.email_verification_required"
              :class="[
                'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out',
                settings.security.email_verification_required ? 'bg-laravel-500' : 'bg-gray-200 dark:bg-gray-600'
              ]"
            >
              <span 
                :class="[
                  'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out',
                  settings.security.email_verification_required ? 'translate-x-5' : 'translate-x-0'
                ]"
              ></span>
            </button>
          </div>
          <div class="flex items-center justify-between">
            <div>
              <p class="font-medium text-gray-900 dark:text-white">Two-Factor Authentication</p>
              <p class="text-sm text-gray-500 dark:text-gray-400">Allow users to enable 2FA</p>
            </div>
            <button 
              @click="settings.security.two_factor_enabled = !settings.security.two_factor_enabled"
              :class="[
                'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out',
                settings.security.two_factor_enabled ? 'bg-laravel-500' : 'bg-gray-200 dark:bg-gray-600'
              ]"
            >
              <span 
                :class="[
                  'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out',
                  settings.security.two_factor_enabled ? 'translate-x-5' : 'translate-x-0'
                ]"
              ></span>
            </button>
          </div>
          <div class="flex items-center justify-between">
            <div>
              <p class="font-medium text-gray-900 dark:text-white">Social Login</p>
              <p class="text-sm text-gray-500 dark:text-gray-400">Allow login via Google, GitHub, etc.</p>
            </div>
            <button 
              @click="settings.security.social_login_enabled = !settings.security.social_login_enabled"
              :class="[
                'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out',
                settings.security.social_login_enabled ? 'bg-laravel-500' : 'bg-gray-200 dark:bg-gray-600'
              ]"
            >
              <span 
                :class="[
                  'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out',
                  settings.security.social_login_enabled ? 'translate-x-5' : 'translate-x-0'
                ]"
              ></span>
            </button>
          </div>
        </div>
      </div>

      <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Password Requirements</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Minimum Password Length
            </label>
            <input 
              v-model="settings.security.min_password_length"
              type="number"
              min="6"
              max="32"
              class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-laravel-500 focus:border-laravel-500"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Password Reset Expiry (minutes)
            </label>
            <input 
              v-model="settings.security.password_reset_expiry"
              type="number"
              class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-laravel-500 focus:border-laravel-500"
            />
          </div>
        </div>
        <div class="mt-4 space-y-2">
          <label class="flex items-center gap-2">
            <input 
              v-model="settings.security.require_mixed_case"
              type="checkbox"
              class="rounded border-gray-300 text-laravel-500 focus:ring-laravel-500"
            />
            <span class="text-sm text-gray-700 dark:text-gray-300">Require mixed case letters</span>
          </label>
          <label class="flex items-center gap-2">
            <input 
              v-model="settings.security.require_numbers"
              type="checkbox"
              class="rounded border-gray-300 text-laravel-500 focus:ring-laravel-500"
            />
            <span class="text-sm text-gray-700 dark:text-gray-300">Require numbers</span>
          </label>
          <label class="flex items-center gap-2">
            <input 
              v-model="settings.security.require_symbols"
              type="checkbox"
              class="rounded border-gray-300 text-laravel-500 focus:ring-laravel-500"
            />
            <span class="text-sm text-gray-700 dark:text-gray-300">Require special characters</span>
          </label>
        </div>
      </div>

      <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Rate Limiting</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Max Login Attempts
            </label>
            <input 
              v-model="settings.security.max_login_attempts"
              type="number"
              class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-laravel-500 focus:border-laravel-500"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Lockout Duration (minutes)
            </label>
            <input 
              v-model="settings.security.lockout_duration"
              type="number"
              class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-laravel-500 focus:border-laravel-500"
            />
          </div>
        </div>
      </div>
    </div>

    <!-- Maintenance Settings -->
    <div v-show="activeTab === 'maintenance'" class="space-y-6">
      <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Maintenance Mode</h2>
        <div class="flex items-center justify-between mb-4">
          <div>
            <p class="font-medium text-gray-900 dark:text-white">Enable Maintenance Mode</p>
            <p class="text-sm text-gray-500 dark:text-gray-400">Only admins can access the platform</p>
          </div>
          <button 
            @click="settings.maintenance.enabled = !settings.maintenance.enabled"
            :class="[
              'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out',
              settings.maintenance.enabled ? 'bg-red-500' : 'bg-gray-200 dark:bg-gray-600'
            ]"
          >
            <span 
              :class="[
                'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out',
                settings.maintenance.enabled ? 'translate-x-5' : 'translate-x-0'
              ]"
            ></span>
          </button>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            Maintenance Message
          </label>
          <textarea 
            v-model="settings.maintenance.message"
            rows="3"
            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-laravel-500 focus:border-laravel-500"
            placeholder="We're currently performing maintenance. Please check back soon!"
          ></textarea>
        </div>
      </div>

      <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Cache Management</h2>
        <div class="space-y-4">
          <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
            <div>
              <p class="font-medium text-gray-900 dark:text-white">Clear Application Cache</p>
              <p class="text-sm text-gray-500 dark:text-gray-400">Clear all cached data</p>
            </div>
            <button 
              @click="clearCache"
              class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition"
            >
              Clear Cache
            </button>
          </div>
          <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
            <div>
              <p class="font-medium text-gray-900 dark:text-white">Clear View Cache</p>
              <p class="text-sm text-gray-500 dark:text-gray-400">Clear compiled view files</p>
            </div>
            <button 
              @click="clearViewCache"
              class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition"
            >
              Clear Views
            </button>
          </div>
          <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
            <div>
              <p class="font-medium text-gray-900 dark:text-white">Clear Config Cache</p>
              <p class="text-sm text-gray-500 dark:text-gray-400">Clear cached configuration</p>
            </div>
            <button 
              @click="clearConfigCache"
              class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition"
            >
              Clear Config
            </button>
          </div>
        </div>
      </div>

      <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">System Information</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
            <p class="text-sm text-gray-500 dark:text-gray-400">Laravel Version</p>
            <p class="font-medium text-gray-900 dark:text-white">{{ systemInfo.laravel_version }}</p>
          </div>
          <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
            <p class="text-sm text-gray-500 dark:text-gray-400">PHP Version</p>
            <p class="font-medium text-gray-900 dark:text-white">{{ systemInfo.php_version }}</p>
          </div>
          <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
            <p class="text-sm text-gray-500 dark:text-gray-400">Database</p>
            <p class="font-medium text-gray-900 dark:text-white">{{ systemInfo.database }}</p>
          </div>
          <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
            <p class="text-sm text-gray-500 dark:text-gray-400">Environment</p>
            <p class="font-medium text-gray-900 dark:text-white">{{ systemInfo.environment }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/api'
import { useToastStore } from '@/stores/toast'

const toast = useToastStore()
const saving = ref(false)
const activeTab = ref('general')

const tabs = [
  { id: 'general', name: 'General' },
  { id: 'content', name: 'Content' },
  { id: 'email', name: 'Email' },
  { id: 'security', name: 'Security' },
  { id: 'maintenance', name: 'Maintenance' }
]

const settings = ref({
  general: {
    platform_name: 'Uganda Developers Community',
    tagline: 'Connect, Learn, and Grow',
    contact_email: 'admin@laravelcommunity.ug',
    support_url: '',
    description: ''
  },
  features: {
    forum_enabled: true,
    jobs_enabled: true,
    events_enabled: true,
    mentorship_enabled: true,
    resources_enabled: true,
    messaging_enabled: true
  },
  content: {
    auto_moderate: false,
    require_job_approval: true,
    require_resource_approval: true,
    banned_words: '',
    max_post_length: 50000,
    max_comment_length: 5000,
    max_tags_per_post: 5,
    max_file_size: 10
  },
  email: {
    welcome_email: true,
    reply_notifications: true,
    mention_notifications: true,
    event_reminders: true,
    weekly_digest: false
  },
  security: {
    email_verification_required: true,
    two_factor_enabled: false,
    social_login_enabled: true,
    min_password_length: 8,
    password_reset_expiry: 60,
    require_mixed_case: true,
    require_numbers: true,
    require_symbols: false,
    max_login_attempts: 5,
    lockout_duration: 15
  },
  maintenance: {
    enabled: false,
    message: ''
  }
})

const systemInfo = ref({
  laravel_version: '11.x',
  php_version: '8.2',
  database: 'MySQL 8.0',
  environment: 'local'
})

const fetchSettings = async () => {
  try {
    const response = await api.get('/admin/settings')
    if (response.data.data) {
      settings.value = { ...settings.value, ...response.data.data }
    }
  } catch (error) {
    console.error('Failed to fetch settings:', error)
  }
}

const fetchSystemInfo = async () => {
  try {
    const response = await api.get('/admin/system-info')
    if (response.data.data) {
      systemInfo.value = response.data.data
    }
  } catch (error) {
    console.error('Failed to fetch system info:', error)
  }
}

const saveSettings = async () => {
  saving.value = true
  try {
    await api.put('/admin/settings', settings.value)
    toast.success('Settings saved successfully')
  } catch (error) {
    toast.error('Failed to save settings')
    console.error('Failed to save settings:', error)
  } finally {
    saving.value = false
  }
}

const clearCache = async () => {
  try {
    await api.post('/admin/cache/clear')
    toast.success('Application cache cleared')
  } catch (error) {
    toast.error('Failed to clear cache')
  }
}

const clearViewCache = async () => {
  try {
    await api.post('/admin/cache/clear-views')
    toast.success('View cache cleared')
  } catch (error) {
    toast.error('Failed to clear view cache')
  }
}

const clearConfigCache = async () => {
  try {
    await api.post('/admin/cache/clear-config')
    toast.success('Config cache cleared')
  } catch (error) {
    toast.error('Failed to clear config cache')
  }
}

onMounted(() => {
  fetchSettings()
  fetchSystemInfo()
})
</script>
