<template>
  <div class="min-h-screen bg-gray-100">
    <div class="flex">
      <!-- Sidebar -->
      <aside class="w-64 bg-gray-900 min-h-screen fixed left-0 top-0">
        <div class="p-4 border-b border-gray-800">
          <router-link to="/" class="flex items-center gap-2 text-white">
            <svg class="h-8 w-auto" viewBox="0 0 50 52" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M49.626 11.564a.809.809 0 01.028.209v10.972a.8.8 0 01-.402.694l-9.209 5.302V39.25c0 .286-.152.55-.4.694L20.42 51.01c-.044.025-.092.041-.14.058-.018.006-.035.017-.054.022a.805.805 0 01-.41 0c-.022-.006-.042-.018-.063-.026-.044-.016-.09-.03-.132-.054L.402 39.944A.801.801 0 010 39.25V6.334c0-.072.01-.142.028-.21.005-.021.018-.04.026-.06.015-.037.028-.075.049-.11.014-.023.036-.04.053-.06.023-.027.044-.055.071-.078.022-.018.049-.03.073-.046.029-.018.055-.04.087-.053l9.61-4.914a.802.802 0 01.736 0l9.61 4.914c.032.013.058.035.087.053.024.016.05.028.073.046.027.023.048.051.071.078.017.02.04.037.053.06.021.035.034.073.05.11.007.02.02.039.025.06.02.068.028.138.028.21v20.559l8.008-4.611V11.773c0-.07.01-.141.028-.209.006-.02.018-.04.026-.06.015-.037.028-.075.049-.11.014-.022.036-.04.053-.06.023-.027.044-.054.071-.077.022-.018.049-.03.073-.046.029-.018.055-.04.087-.053l9.611-4.914a.802.802 0 01.736 0l9.61 4.914c.032.013.058.035.087.053.024.016.05.028.073.046.027.023.048.05.071.077.017.02.04.038.053.06.021.035.034.073.05.11.007.02.02.04.025.06z" fill="#FF2D20"/>
            </svg>
            <div>
              <span class="font-bold text-lg block">Laravel Community</span>
              <span class="text-xs text-gray-400">Uganda Admin</span>
            </div>
          </router-link>
        </div>
        
        <nav class="mt-4">
          <router-link
            v-for="item in menuItems"
            :key="item.to"
            :to="item.to"
            class="flex items-center gap-3 px-4 py-3 text-gray-300 hover:bg-gray-800 hover:text-white transition-colors"
            :class="{ 'bg-gray-800 text-white border-l-4 border-primary-500': isActive(item.to) }"
          >
            <component :is="item.icon" class="h-5 w-5" />
            {{ item.label }}
          </router-link>
        </nav>

        <!-- User Section at Bottom -->
        <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-gray-800">
          <div class="flex items-center gap-3 mb-3">
            <img :src="user?.avatar || '/images/default-avatar.png'" :alt="user?.name" class="w-10 h-10 rounded-full" />
            <div class="flex-1 min-w-0">
              <p class="text-white text-sm font-medium truncate">{{ user?.name }}</p>
              <p class="text-gray-400 text-xs truncate">{{ user?.email }}</p>
            </div>
          </div>
          <div class="flex gap-2">
            <router-link to="/settings" class="flex-1 text-center py-2 text-sm text-gray-300 hover:text-white bg-gray-800 rounded-lg transition">
              Settings
            </router-link>
            <button @click="logout" class="flex-1 py-2 text-sm text-red-400 hover:text-red-300 bg-gray-800 rounded-lg transition">
              Logout
            </button>
          </div>
        </div>
      </aside>

      <!-- Main Content -->
      <main class="flex-1 ml-64 p-8">
        <router-view />
      </main>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import {
  HomeIcon,
  UsersIcon,
  DocumentTextIcon,
  BriefcaseIcon,
  CalendarIcon,
  FlagIcon,
  TagIcon,
  TrophyIcon,
  CogIcon,
  HeartIcon
} from '@heroicons/vue/20/solid'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()

const user = computed(() => authStore.user)

const menuItems = [
  { to: '/admin', label: 'Dashboard', icon: HomeIcon },
  { to: '/admin/users', label: 'Users', icon: UsersIcon },
  { to: '/admin/posts', label: 'Posts', icon: DocumentTextIcon },
  { to: '/admin/jobs', label: 'Jobs', icon: BriefcaseIcon },
  { to: '/admin/events', label: 'Events', icon: CalendarIcon },
  { to: '/admin/reports', label: 'Reports', icon: FlagIcon },
  { to: '/admin/categories', label: 'Categories', icon: TagIcon },
  { to: '/admin/badges', label: 'Badges', icon: TrophyIcon },
  { to: '/admin/donations', label: 'Donations', icon: HeartIcon },
  { to: '/admin/settings', label: 'Settings', icon: CogIcon }
]

const isActive = (path) => {
  if (path === '/admin') {
    return route.path === '/admin'
  }
  return route.path.startsWith(path)
}

const logout = async () => {
  await authStore.logout()
  router.push('/login')
}
</script>
