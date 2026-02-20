<template>
  <div class="min-h-screen bg-gray-50">
    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="flex flex-col lg:flex-row gap-8">
        <!-- Sidebar -->
        <aside class="lg:w-64">
          <nav class="bg-white rounded-xl shadow-sm p-4">
            <router-link
              v-for="item in menuItems"
              :key="item.to"
              :to="item.to"
              class="flex items-center gap-3 px-4 py-2 rounded-lg transition-colors"
              :class="$route.name === item.name ? 'bg-primary-50 text-primary-600' : 'text-gray-700 hover:bg-gray-50'"
            >
              <component :is="item.icon" class="h-5 w-5" />
              {{ item.label }}
            </router-link>
          </nav>
        </aside>

        <!-- Content -->
        <div class="flex-1">
          <router-view />
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { UserIcon, Cog6ToothIcon, BellIcon, LockClosedIcon } from '@heroicons/vue/20/solid'

const menuItems = [
  { name: 'settings-profile', to: '/settings', label: 'Profile', icon: UserIcon },
  { name: 'settings-account', to: '/settings/account', label: 'Account', icon: Cog6ToothIcon },
  { name: 'settings-notifications', to: '/settings/notifications', label: 'Notifications', icon: BellIcon },
  { name: 'settings-privacy', to: '/settings/privacy', label: 'Privacy', icon: LockClosedIcon }
]
</script>
