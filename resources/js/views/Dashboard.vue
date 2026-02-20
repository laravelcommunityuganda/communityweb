<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <div class="container mx-auto px-4 py-8">
      <!-- Welcome Header -->
      <div class="bg-gradient-to-r from-laravel-500 to-laravel-600 rounded-2xl p-8 mb-8 text-white">
        <div class="flex items-center gap-6">
          <img
            :src="user.avatar"
            :alt="user.name"
            class="w-20 h-20 rounded-full border-4 border-white/20"
          />
          <div>
            <h1 class="text-3xl font-bold">Welcome back, {{ user.name }}!</h1>
            <p class="text-laravel-100 mt-1">@{{ user.username }}</p>
            <div class="flex items-center gap-4 mt-3">
              <span class="flex items-center gap-1">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                </svg>
                {{ user.reputation }} Reputation
              </span>
              <span v-if="user.is_verified" class="flex items-center gap-1 bg-white/20 px-2 py-1 rounded-full text-sm">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                Verified
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm flex flex-col h-full">
          <div class="flex items-center justify-between flex-1">
            <div>
              <p class="text-gray-500 dark:text-gray-400 text-sm">My Posts</p>
              <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ stats.posts }}</p>
            </div>
            <div class="w-12 h-12 bg-laravel-100 dark:bg-laravel-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
              <svg class="w-6 h-6 text-laravel-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
              </svg>
            </div>
          </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm flex flex-col h-full">
          <div class="flex items-center justify-between flex-1">
            <div>
              <p class="text-gray-500 dark:text-gray-400 text-sm">Comments</p>
              <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ stats.comments }}</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
              <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
              </svg>
            </div>
          </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm flex flex-col h-full">
          <div class="flex items-center justify-between flex-1">
            <div>
              <p class="text-gray-500 dark:text-gray-400 text-sm">Followers</p>
              <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ stats.followers }}</p>
            </div>
            <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
              <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
              </svg>
            </div>
          </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm flex flex-col h-full">
          <div class="flex items-center justify-between flex-1">
            <div>
              <p class="text-gray-500 dark:text-gray-400 text-sm">Bookmarks</p>
              <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ stats.bookmarks }}</p>
            </div>
            <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
              <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
              </svg>
            </div>
          </div>
        </div>
      </div>

      <!-- Main Content Grid -->
      <div class="grid lg:grid-cols-3 gap-8 items-start">
        <!-- Left Column -->
        <div class="lg:col-span-2 space-y-8">
          <!-- Quick Actions -->
          <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Quick Actions</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
              <router-link to="/post/create" class="flex flex-col items-center justify-center p-4 rounded-lg bg-laravel-50 dark:bg-laravel-900/20 hover:bg-laravel-100 dark:hover:bg-laravel-900/30 transition min-h-[120px]">
                <svg class="w-8 h-8 text-laravel-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300 text-center">New Post</span>
              </router-link>
              <router-link to="/jobs/create" class="flex flex-col items-center justify-center p-4 rounded-lg bg-blue-50 dark:bg-blue-900/20 hover:bg-blue-100 dark:hover:bg-blue-900/30 transition min-h-[120px]">
                <svg class="w-8 h-8 text-blue-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300 text-center">Post Job</span>
              </router-link>
              <router-link to="/events/create" class="flex flex-col items-center justify-center p-4 rounded-lg bg-green-50 dark:bg-green-900/20 hover:bg-green-100 dark:hover:bg-green-900/30 transition min-h-[120px]">
                <svg class="w-8 h-8 text-green-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300 text-center">New Event</span>
              </router-link>
              <router-link to="/resources/create" class="flex flex-col items-center justify-center p-4 rounded-lg bg-purple-50 dark:bg-purple-900/20 hover:bg-purple-100 dark:hover:bg-purple-900/30 transition min-h-[120px]">
                <svg class="w-8 h-8 text-purple-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                </svg>
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300 text-center">Share Resource</span>
              </router-link>
            </div>
          </div>

          <!-- My Posts -->
          <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
              <h2 class="text-lg font-semibold text-gray-900 dark:text-white">My Recent Posts</h2>
              <router-link to="/my-posts" class="text-laravel-600 hover:text-laravel-700 text-sm font-medium">
                View All
              </router-link>
            </div>
            <div v-if="posts.length === 0" class="text-center py-8">
              <p class="text-gray-500 dark:text-gray-400">You haven't created any posts yet.</p>
              <router-link to="/post/create" class="inline-block mt-4 px-4 py-2 bg-laravel-500 text-white rounded-lg hover:bg-laravel-600 transition">
                Create Your First Post
              </router-link>
            </div>
            <div v-else class="space-y-4">
              <div v-for="post in posts" :key="post.id" class="flex items-start gap-4 p-4 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                <div class="flex-1 min-w-0">
                  <router-link :to="`/post/${post.slug}`" class="font-medium text-gray-900 dark:text-white hover:text-laravel-600 dark:hover:text-laravel-400">
                    {{ post.title }}
                  </router-link>
                  <div class="flex items-center gap-4 mt-2 text-sm text-gray-500 dark:text-gray-400">
                    <span>{{ post.views_count }} views</span>
                    <span>{{ post.comments_count }} comments</span>
                    <span>{{ post.upvotes_count - post.downvotes_count }} votes</span>
                  </div>
                </div>
                <span :class="post.status === 'published' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400'" class="px-2 py-1 text-xs font-medium rounded">
                  {{ post.status }}
                </span>
              </div>
            </div>
          </div>

          <!-- Activity Feed -->
          <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Recent Activity</h2>
            <div class="space-y-4">
              <div v-for="activity in activities" :key="activity.id" class="flex items-start gap-4">
                <div class="w-10 h-10 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                  <component :is="getActivityIcon(activity.type)" class="w-5 h-5 text-gray-500 dark:text-gray-400" />
                </div>
                <div class="flex-1">
                  <p class="text-gray-900 dark:text-white" v-html="activity.message"></p>
                  <p class="text-sm text-gray-500 dark:text-gray-400">{{ activity.time }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Right Column -->
        <div class="space-y-8">
          <!-- Profile Completion -->
          <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Profile Completion</h2>
            <div class="mb-4">
              <div class="flex justify-between text-sm mb-2">
                <span class="text-gray-600 dark:text-gray-400">Progress</span>
                <span class="font-medium text-gray-900 dark:text-white">{{ profileCompletion }}%</span>
              </div>
              <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                <div class="bg-laravel-500 h-2 rounded-full transition-all" :style="{ width: `${profileCompletion}%` }"></div>
              </div>
            </div>
            <ul class="space-y-2 text-sm">
              <li v-for="item in profileItems" :key="item.label" class="flex items-center gap-2">
                <svg v-if="item.completed" class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <svg v-else class="w-5 h-5 text-gray-300 dark:text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <span :class="item.completed ? 'text-gray-700 dark:text-gray-300' : 'text-gray-400 dark:text-gray-500'">
                  {{ item.label }}
                </span>
              </li>
            </ul>
            <router-link to="/settings/profile" class="block mt-4 text-center text-laravel-600 hover:text-laravel-700 text-sm font-medium">
              Complete Profile
            </router-link>
          </div>

          <!-- Badges -->
          <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">My Badges</h2>
            <div v-if="badges.length === 0" class="text-center py-4">
              <p class="text-gray-500 dark:text-gray-400 text-sm">No badges earned yet</p>
            </div>
            <div v-else class="grid grid-cols-3 gap-4">
              <div v-for="badge in badges" :key="badge.id" class="text-center">
                <div class="w-12 h-12 mx-auto bg-yellow-100 dark:bg-yellow-900/30 rounded-full flex items-center justify-center mb-2">
                  <svg class="w-6 h-6 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5 2a2 2 0 00-2 2v14l3.5-2 3.5 2 3.5-2 3.5 2V4a2 2 0 00-2-2H5zm2.5 3a1.5 1.5 0 100 3 1.5 1.5 0 000-3zm6.207.293a1 1 0 00-1.414 0l-6 6a1 1 0 101.414 1.414l6-6a1 1 0 000-1.414zM12.5 10a1.5 1.5 0 100 3 1.5 1.5 0 000-3z" clip-rule="evenodd"/>
                  </svg>
                </div>
                <p class="text-xs text-gray-600 dark:text-gray-400">{{ badge.name }}</p>
              </div>
            </div>
          </div>

          <!-- Upcoming Events -->
          <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Upcoming Events</h2>
            <div v-if="events.length === 0" class="text-center py-4">
              <p class="text-gray-500 dark:text-gray-400 text-sm">No upcoming events</p>
            </div>
            <div v-else class="space-y-4">
              <EventCard v-for="event in events" :key="event.id" :event="event" />
            </div>
          </div>

          <!-- Support Our Community -->
          <div class="bg-gradient-to-br from-pink-500 to-rose-600 rounded-xl p-6 shadow-sm text-white">
            <div class="flex items-center gap-3 mb-3">
              <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/>
              </svg>
              <h2 class="text-lg font-semibold">Support Our Community</h2>
            </div>
            <p class="text-pink-100 text-sm mb-4">
              Help us grow and maintain the Laravel Uganda community. Your donations support events, resources, and community initiatives.
            </p>
            <router-link 
              to="/donations" 
              class="inline-flex items-center gap-2 px-4 py-2 bg-white text-pink-600 rounded-lg font-medium hover:bg-pink-50 transition"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
              </svg>
              Make a Donation
            </router-link>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useAuthStore } from '@/stores/auth'
import axios from 'axios'
import EventCard from '@/components/EventCard.vue'

const authStore = useAuthStore()
const user = computed(() => authStore.user)

const stats = ref({
  posts: 0,
  comments: 0,
  followers: 0,
  bookmarks: 0,
})

const posts = ref([])
const activities = ref([])
const badges = ref([])
const events = ref([])

const profileCompletion = ref(0)
const profileItems = ref([
  { label: 'Profile picture', completed: false },
  { label: 'Bio', completed: false },
  { label: 'Skills', completed: false },
  { label: 'Location', completed: false },
  { label: 'GitHub profile', completed: false },
])

const fetchDashboardData = async () => {
  try {
    const response = await axios.get('/api/v1/auth/user/dashboard')
    
    const data = response.data.data || response.data
    stats.value = data.stats
    posts.value = data.posts
    activities.value = data.activities
    badges.value = data.badges
    events.value = data.events
    profileCompletion.value = data.profile_completion
    profileItems.value = data.profile_items
  } catch (error) {
    console.error('Failed to fetch dashboard data:', error)
  }
}

const getActivityIcon = (type) => {
  const icons = {
    post: 'PostIcon',
    comment: 'CommentIcon',
    vote: 'VoteIcon',
    badge: 'BadgeIcon',
  }
  return icons[type] || 'ActivityIcon'
}

onMounted(() => {
  fetchDashboardData()
})
</script>