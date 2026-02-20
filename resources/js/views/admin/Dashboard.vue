<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Admin Dashboard</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Welcome back, {{ authStore.user?.name }}</p>
      </div>
      <div class="flex items-center gap-3">
        <span class="text-sm text-gray-500 dark:text-gray-400">
          Last updated: {{ lastUpdated }}
        </span>
        <button 
          @click="refreshData" 
          :disabled="loading"
          class="px-4 py-2 bg-laravel-500 text-white rounded-lg hover:bg-laravel-600 transition disabled:opacity-50"
        >
          <ArrowPathIcon v-if="loading" class="h-4 w-4 animate-spin inline mr-2" />
          Refresh
        </button>
      </div>
    </div>

    <!-- Quick Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border-l-4 border-blue-500">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Users</p>
            <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ stats.users?.total || 0 }}</p>
            <p class="text-sm text-green-600 mt-1">
              <span v-if="stats.users?.new_this_week">+{{ stats.users.new_this_week }} this week</span>
            </p>
          </div>
          <div class="h-12 w-12 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
            <UsersIcon class="h-6 w-6 text-blue-600" />
          </div>
        </div>
      </div>

      <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border-l-4 border-green-500">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Posts</p>
            <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ stats.posts?.total || 0 }}</p>
            <p class="text-sm text-green-600 mt-1">
              <span v-if="stats.posts?.this_week">+{{ stats.posts.this_week }} this week</span>
            </p>
          </div>
          <div class="h-12 w-12 rounded-lg bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
            <DocumentTextIcon class="h-6 w-6 text-green-600" />
          </div>
        </div>
      </div>

      <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border-l-4 border-yellow-500">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Active Jobs</p>
            <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ stats.jobs?.published || 0 }}</p>
            <p class="text-sm text-yellow-600 mt-1">
              {{ stats.jobs?.pending || 0 }} pending approval
            </p>
          </div>
          <div class="h-12 w-12 rounded-lg bg-yellow-100 dark:bg-yellow-900/30 flex items-center justify-center">
            <BriefcaseIcon class="h-6 w-6 text-yellow-600" />
          </div>
        </div>
      </div>

      <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border-l-4 border-laravel-500">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Upcoming Events</p>
            <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ stats.events?.upcoming || 0 }}</p>
            <p class="text-sm text-laravel-600 mt-1">
              {{ stats.events?.total || 0 }} total events
            </p>
          </div>
          <div class="h-12 w-12 rounded-lg bg-laravel-100 dark:bg-laravel-900/30 flex items-center justify-center">
            <CalendarIcon class="h-6 w-6 text-laravel-600" />
          </div>
        </div>
      </div>
    </div>

    <!-- Secondary Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-4">User Verification</h3>
        <div class="flex items-center gap-4">
          <div class="flex-1">
            <div class="flex justify-between text-sm mb-1">
              <span class="text-gray-600 dark:text-gray-400">Verified</span>
              <span class="font-medium text-gray-900 dark:text-white">{{ stats.users?.verified || 0 }}</span>
            </div>
            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
              <div 
                class="bg-green-500 h-2 rounded-full" 
                :style="{ width: `${verificationRate}%` }"
              ></div>
            </div>
          </div>
          <span class="text-2xl font-bold text-green-600">{{ verificationRate }}%</span>
        </div>
      </div>

      <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-4">Content Status</h3>
        <div class="space-y-3">
          <div class="flex justify-between items-center">
            <span class="text-sm text-gray-600 dark:text-gray-400">Published Posts</span>
            <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">{{ stats.posts?.published || 0 }}</span>
          </div>
          <div class="flex justify-between items-center">
            <span class="text-sm text-gray-600 dark:text-gray-400">Draft Posts</span>
            <span class="px-2 py-1 bg-gray-100 text-gray-800 text-xs rounded-full">{{ stats.posts?.draft || 0 }}</span>
          </div>
          <div class="flex justify-between items-center">
            <span class="text-sm text-gray-600 dark:text-gray-400">Pending Resources</span>
            <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs rounded-full">{{ stats.resources?.pending || 0 }}</span>
          </div>
        </div>
      </div>

      <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-4">Reports Overview</h3>
        <div class="space-y-3">
          <div class="flex justify-between items-center">
            <span class="text-sm text-gray-600 dark:text-gray-400">Pending</span>
            <span class="px-2 py-1 bg-red-100 text-red-800 text-xs rounded-full">{{ stats.reports?.pending || 0 }}</span>
          </div>
          <div class="flex justify-between items-center">
            <span class="text-sm text-gray-600 dark:text-gray-400">Resolved</span>
            <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">{{ stats.reports?.resolved || 0 }}</span>
          </div>
          <div class="flex justify-between items-center">
            <span class="text-sm text-gray-600 dark:text-gray-400">Total</span>
            <span class="px-2 py-1 bg-gray-100 text-gray-800 text-xs rounded-full">{{ stats.reports?.total || 0 }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Growth Chart Section -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
      <div class="flex items-center justify-between mb-6">
        <h2 class="font-semibold text-gray-900 dark:text-white">Growth Overview</h2>
        <div class="flex items-center gap-2">
          <button 
            v-for="period in ['7', '30', '90']" 
            :key="period"
            @click="chartPeriod = period"
            :class="[
              'px-3 py-1 text-sm rounded-lg transition',
              chartPeriod === period 
                ? 'bg-laravel-500 text-white' 
                : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 hover:bg-gray-200'
            ]"
          >
            {{ period }} days
          </button>
        </div>
      </div>
      
      <!-- Simple Chart Visualization -->
      <div class="h-64 flex items-end gap-1" v-if="growthData.users?.length">
        <div 
          v-for="(item, index) in growthData.users" 
          :key="index"
          class="flex-1 bg-blue-500 rounded-t transition-all hover:bg-blue-600"
          :style="{ height: `${(item.count / maxGrowthValue) * 100}%` }"
          :title="`${item.date}: ${item.count} users`"
        ></div>
      </div>
      <div v-else class="h-64 flex items-center justify-center text-gray-500">
        No growth data available
      </div>
      
      <!-- Legend -->
      <div class="flex items-center justify-center gap-6 mt-4">
        <div class="flex items-center gap-2">
          <div class="w-3 h-3 bg-blue-500 rounded"></div>
          <span class="text-sm text-gray-600 dark:text-gray-400">New Users</span>
        </div>
      </div>
    </div>

    <!-- Two Column Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Recent Activity -->
      <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
          <h2 class="font-semibold text-gray-900 dark:text-white">Recent Activity</h2>
          <router-link to="/admin/activity" class="text-sm text-laravel-500 hover:text-laravel-600">
            View all
          </router-link>
        </div>
        <div v-if="recentActivity.length" class="space-y-4">
          <div
            v-for="activity in recentActivity"
            :key="activity.id"
            class="flex items-start gap-3 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition"
          >
            <div class="w-10 h-10 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center flex-shrink-0">
              <span class="text-sm font-medium text-gray-600 dark:text-gray-300">
                {{ activity.user?.name?.charAt(0) || '?' }}
              </span>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-sm text-gray-900 dark:text-white">{{ activity.description }}</p>
              <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ formatTime(activity.created_at) }}</p>
            </div>
          </div>
        </div>
        <div v-else class="text-center py-8">
          <ClockIcon class="h-12 w-12 text-gray-300 dark:text-gray-600 mx-auto mb-3" />
          <p class="text-gray-500 dark:text-gray-400">No recent activity</p>
        </div>
      </div>

      <!-- Pending Reports -->
      <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
          <h2 class="font-semibold text-gray-900 dark:text-white">Pending Reports</h2>
          <router-link to="/admin/reports" class="text-sm text-laravel-500 hover:text-laravel-600">
            View all
          </router-link>
        </div>
        <div v-if="pendingReports.length" class="space-y-4">
          <div
            v-for="report in pendingReports"
            :key="report.id"
            class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg"
          >
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                <FlagIcon class="h-5 w-5 text-red-600" />
              </div>
              <div>
                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ report.reason }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ formatReportableType(report.reportable_type) }}</p>
              </div>
            </div>
            <router-link 
              to="/admin/reports" 
              class="px-3 py-1 text-sm bg-laravel-500 text-white rounded-lg hover:bg-laravel-600 transition"
            >
              Review
            </router-link>
          </div>
        </div>
        <div v-else class="text-center py-8">
          <CheckCircleIcon class="h-12 w-12 text-green-300 dark:text-green-600 mx-auto mb-3" />
          <p class="text-gray-500 dark:text-gray-400">No pending reports</p>
        </div>
      </div>
    </div>

    <!-- Top Users Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Top Posters -->
      <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
        <h2 class="font-semibold text-gray-900 dark:text-white mb-4">Top Contributors</h2>
        <div v-if="topUsers.top_posters?.length" class="space-y-3">
          <div 
            v-for="(user, index) in topUsers.top_posters" 
            :key="user.id"
            class="flex items-center gap-3"
          >
            <span class="text-sm font-medium text-gray-500 w-5">{{ index + 1 }}</span>
            <div class="w-8 h-8 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
              <span class="text-xs font-medium text-gray-600 dark:text-gray-300">{{ user.name?.charAt(0) }}</span>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ user.name }}</p>
              <p class="text-xs text-gray-500">{{ user.posts_count }} posts</p>
            </div>
          </div>
        </div>
        <p v-else class="text-gray-500 dark:text-gray-400 text-center py-4">No data available</p>
      </div>

      <!-- Top Reputation -->
      <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
        <h2 class="font-semibold text-gray-900 dark:text-white mb-4">Top Reputation</h2>
        <div v-if="topUsers.top_reputation?.length" class="space-y-3">
          <div 
            v-for="(user, index) in topUsers.top_reputation" 
            :key="user.id"
            class="flex items-center gap-3"
          >
            <span class="text-sm font-medium text-gray-500 w-5">{{ index + 1 }}</span>
            <div class="w-8 h-8 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
              <span class="text-xs font-medium text-gray-600 dark:text-gray-300">{{ user.name?.charAt(0) }}</span>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ user.name }}</p>
              <p class="text-xs text-gray-500">{{ user.reputation }} points</p>
            </div>
          </div>
        </div>
        <p v-else class="text-gray-500 dark:text-gray-400 text-center py-4">No data available</p>
      </div>

      <!-- Top Employers -->
      <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
        <h2 class="font-semibold text-gray-900 dark:text-white mb-4">Top Employers</h2>
        <div v-if="topUsers.top_employers?.length" class="space-y-3">
          <div 
            v-for="(user, index) in topUsers.top_employers" 
            :key="user.id"
            class="flex items-center gap-3"
          >
            <span class="text-sm font-medium text-gray-500 w-5">{{ index + 1 }}</span>
            <div class="w-8 h-8 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
              <span class="text-xs font-medium text-gray-600 dark:text-gray-300">{{ user.name?.charAt(0) }}</span>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ user.name }}</p>
              <p class="text-xs text-gray-500">{{ user.jobs_count }} jobs posted</p>
            </div>
          </div>
        </div>
        <p v-else class="text-gray-500 dark:text-gray-400 text-center py-4">No data available</p>
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
      <h2 class="font-semibold text-gray-900 dark:text-white mb-4">Quick Actions</h2>
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <router-link 
          to="/admin/users" 
          class="flex items-center gap-3 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition"
        >
          <UsersIcon class="h-6 w-6 text-blue-500" />
          <span class="text-sm font-medium text-gray-900 dark:text-white">Manage Users</span>
        </router-link>
        <router-link 
          to="/admin/posts" 
          class="flex items-center gap-3 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition"
        >
          <DocumentTextIcon class="h-6 w-6 text-green-500" />
          <span class="text-sm font-medium text-gray-900 dark:text-white">Moderate Posts</span>
        </router-link>
        <router-link 
          to="/admin/jobs" 
          class="flex items-center gap-3 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition"
        >
          <BriefcaseIcon class="h-6 w-6 text-yellow-500" />
          <span class="text-sm font-medium text-gray-900 dark:text-white">Review Jobs</span>
        </router-link>
        <router-link 
          to="/admin/reports" 
          class="flex items-center gap-3 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition"
        >
          <FlagIcon class="h-6 w-6 text-red-500" />
          <span class="text-sm font-medium text-gray-900 dark:text-white">Handle Reports</span>
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { 
  UsersIcon, 
  DocumentTextIcon, 
  BriefcaseIcon, 
  CalendarIcon,
  FlagIcon,
  ClockIcon,
  CheckCircleIcon,
  ArrowPathIcon
} from '@heroicons/vue/20/solid'
import api from '@/api'
import { useAuthStore } from '@/stores/auth'

const authStore = useAuthStore()

const loading = ref(false)
const lastUpdated = ref(new Date().toLocaleString())

const stats = ref({
  users: { total: 0, new_this_week: 0, verified: 0 },
  posts: { total: 0, published: 0, draft: 0, this_week: 0 },
  jobs: { total: 0, published: 0, pending: 0 },
  events: { total: 0, upcoming: 0 },
  resources: { total: 0, pending: 0 },
  reports: { total: 0, pending: 0, resolved: 0 }
})

const recentActivity = ref([])
const pendingReports = ref([])
const growthData = ref({ users: [], posts: [], jobs: [] })
const topUsers = ref({ top_posters: [], top_reputation: [], top_employers: [] })
const chartPeriod = ref('30')

const verificationRate = computed(() => {
  if (!stats.value.users?.total) return 0
  return Math.round((stats.value.users.verified / stats.value.users.total) * 100)
})

const maxGrowthValue = computed(() => {
  if (!growthData.value.users?.length) return 1
  return Math.max(...growthData.value.users.map(u => u.count), 1)
})

const fetchStats = async () => {
  loading.value = true
  try {
    const response = await api.get('/admin/statistics')
    stats.value = response.data.data || response.data
  } catch (error) {
    console.error('Failed to fetch stats:', error)
  }
}

const fetchActivity = async () => {
  try {
    const response = await api.get('/admin/activity')
    recentActivity.value = response.data.data || []
  } catch (error) {
    console.error('Failed to fetch activity:', error)
  }
}

const fetchGrowth = async () => {
  try {
    const response = await api.get(`/admin/growth?period=${chartPeriod.value}`)
    growthData.value = response.data.data || { users: [], posts: [], jobs: [] }
  } catch (error) {
    console.error('Failed to fetch growth data:', error)
  }
}

const fetchTopUsers = async () => {
  try {
    const response = await api.get('/admin/top-users')
    topUsers.value = response.data.data || { top_posters: [], top_reputation: [], top_employers: [] }
  } catch (error) {
    console.error('Failed to fetch top users:', error)
  }
}

const fetchPendingReports = async () => {
  try {
    const response = await api.get('/admin/reports?status=pending&limit=5')
    pendingReports.value = response.data.data?.slice(0, 5) || []
  } catch (error) {
    console.error('Failed to fetch reports:', error)
  }
}

const refreshData = async () => {
  await Promise.all([
    fetchStats(),
    fetchActivity(),
    fetchGrowth(),
    fetchTopUsers(),
    fetchPendingReports()
  ])
  lastUpdated.value = new Date().toLocaleString()
  loading.value = false
}

const formatTime = (date) => {
  if (!date) return ''
  const d = new Date(date)
  const now = new Date()
  const diff = now - d
  const minutes = Math.floor(diff / 60000)
  const hours = Math.floor(diff / 3600000)
  const days = Math.floor(diff / 86400000)
  
  if (minutes < 1) return 'Just now'
  if (minutes < 60) return `${minutes}m ago`
  if (hours < 24) return `${hours}h ago`
  return `${days}d ago`
}

const formatReportableType = (type) => {
  if (!type) return 'Unknown'
  return type.split('\\').pop()
}

watch(chartPeriod, fetchGrowth)

onMounted(() => {
  refreshData()
})
</script>
