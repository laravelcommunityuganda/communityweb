<template>
  <header class="fixed top-0 left-0 right-0 z-50 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex items-center justify-between h-16">
        <!-- Logo -->
        <div class="flex items-center space-x-4">
          <router-link to="/" class="flex items-center space-x-2">
            <div class="w-8 h-8 bg-primary-600 rounded-lg flex items-center justify-center">
              <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
              </svg>
            </div>
            <span class="text-lg font-bold text-gray-900 dark:text-white hidden sm:block">Laravel Community Uganda</span>
          </router-link>

          <!-- Search -->
          <div class="hidden md:block">
            <div class="relative">
              <input
                type="text"
                v-model="searchQuery"
                @keyup.enter="handleSearch"
                placeholder="Search..."
                class="w-64 lg:w-96 pl-10 pr-4 py-2 text-sm bg-gray-100 dark:bg-gray-700 border-0 rounded-lg focus:ring-2 focus:ring-primary-500 dark:text-white dark:placeholder-gray-400"
              />
              <svg class="absolute left-3 top-2.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </div>
          </div>
        </div>

        <!-- Navigation Links -->
        <nav class="hidden lg:flex items-center space-x-1">
          <router-link
            v-for="item in navItems"
            :key="item.name"
            :to="item.href"
            class="px-3 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
            :class="{ 'text-primary-600 dark:text-primary-400 bg-gray-100 dark:bg-gray-700': isActive(item.href) }"
          >
            {{ item.name }}
          </router-link>
        </nav>

        <!-- Right side -->
        <div class="flex items-center space-x-2">
          <!-- Dark mode toggle -->
          <button
            @click="toggleDarkMode"
            class="p-2 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg"
            aria-label="Toggle dark mode"
          >
            <svg v-if="isDark" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
            </svg>
          </button>

          <!-- Notifications -->
          <button
            v-if="authStore.isAuthenticated"
            @click="showNotifications = !showNotifications"
            class="relative p-2 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg"
            aria-label="Notifications"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            <span
              v-if="unreadNotifications > 0"
              class="absolute top-1 right-1 w-4 h-4 bg-danger-500 text-white text-xs rounded-full flex items-center justify-center"
            >
              {{ unreadNotifications > 9 ? '9+' : unreadNotifications }}
            </span>
          </button>

          <!-- User menu -->
          <div v-if="authStore.isAuthenticated" class="relative">
            <button
              @click="showUserMenu = !showUserMenu"
              class="flex items-center space-x-2 p-1 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700"
            >
              <img
                :src="authStore.user?.avatar_url || `https://ui-avatars.com/api/?name=${encodeURIComponent(authStore.user?.name || 'User')}`"
                :alt="authStore.user?.name"
                class="w-8 h-8 rounded-full"
              />
              <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </button>

            <!-- User dropdown -->
            <transition
              enter-active-class="transition ease-out duration-100"
              enter-from-class="transform opacity-0 scale-95"
              enter-to-class="transform opacity-100 scale-100"
              leave-active-class="transition ease-in duration-75"
              leave-from-class="transform opacity-100 scale-100"
              leave-to-class="transform opacity-0 scale-95"
            >
              <div
                v-if="showUserMenu"
                class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 dark:ring-gray-700 py-1"
              >
                <div class="px-4 py-2 border-b border-gray-200 dark:border-gray-700">
                  <p class="text-sm font-medium text-gray-900 dark:text-white">{{ authStore.user?.name }}</p>
                  <p class="text-xs text-gray-500 dark:text-gray-400">@{{ authStore.user?.username }}</p>
                </div>

                <router-link
                  v-for="item in userMenuItems"
                  :key="item.name"
                  :to="item.href"
                  @click="showUserMenu = false"
                  class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700"
                >
                  <component :is="item.icon" class="w-4 h-4 mr-3" />
                  {{ item.name }}
                </router-link>

                <button
                  @click="handleLogout"
                  class="w-full flex items-center px-4 py-2 text-sm text-danger-600 dark:text-danger-400 hover:bg-gray-100 dark:hover:bg-gray-700"
                >
                  <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                  </svg>
                  Sign out
                </button>
              </div>
            </transition>
          </div>

          <!-- Login/Register buttons -->
          <div v-else class="flex items-center space-x-2">
            <router-link
              to="/login"
              class="px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400"
            >
              Sign in
            </router-link>
            <router-link
              to="/register"
              class="px-4 py-2 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-lg"
            >
              Sign up
            </router-link>
          </div>

          <!-- Mobile menu button -->
          <button
            @click="showMobileMenu = !showMobileMenu"
            class="lg:hidden p-2 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg"
          >
            <svg v-if="!showMobileMenu" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
            <svg v-else class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
      </div>
    </div>

    <!-- Mobile menu -->
    <transition
      enter-active-class="transition ease-out duration-100"
      enter-from-class="transform opacity-0 scale-95"
      enter-to-class="transform opacity-100 scale-100"
      leave-active-class="transition ease-in duration-75"
      leave-from-class="transform opacity-100 scale-100"
      leave-to-class="transform opacity-0 scale-95"
    >
      <div v-if="showMobileMenu" class="lg:hidden bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
        <div class="px-4 py-3">
          <input
            type="text"
            v-model="searchQuery"
            @keyup.enter="handleSearch"
            placeholder="Search..."
            class="w-full pl-10 pr-4 py-2 text-sm bg-gray-100 dark:bg-gray-700 border-0 rounded-lg focus:ring-2 focus:ring-primary-500 dark:text-white dark:placeholder-gray-400"
          />
        </div>
        <nav class="px-2 py-2">
          <router-link
            v-for="item in navItems"
            :key="item.name"
            :to="item.href"
            @click="showMobileMenu = false"
            class="block px-3 py-2 text-base font-medium text-gray-600 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700"
          >
            {{ item.name }}
          </router-link>
        </nav>
      </div>
    </transition>
  </header>
</template>

<script setup>
import { ref, inject, computed } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useAuthStore } from '@/stores/auth';

const router = useRouter();
const route = useRoute();
const authStore = useAuthStore();

// Dark mode
const { isDark, toggleDarkMode } = inject('darkMode');

// State
const showMobileMenu = ref(false);
const showUserMenu = ref(false);
const showNotifications = ref(false);
const searchQuery = ref('');
const unreadNotifications = ref(0);

// Navigation items
const navItems = [
  { name: 'Community', href: '/community' },
  { name: 'Resources', href: '/resources' },
  { name: 'Jobs', href: '/jobs' },
  { name: 'Events', href: '/events' },
  { name: 'Mentors', href: '/mentors' },
  { name: 'Donate', href: '/donations' },
];

// User menu items
const userMenuItems = computed(() => {
  const items = [
    { name: 'Profile', href: `/profile/${authStore.user?.username}`, icon: 'UserIcon' },
    { name: 'Settings', href: '/settings', icon: 'CogIcon' },
    { name: 'My Applications', href: '/my-applications', icon: 'BriefcaseIcon' },
    { name: 'My Mentorships', href: '/my-mentorships', icon: 'AcademicCapIcon' },
    { name: 'Messages', href: '/chat', icon: 'ChatIcon' },
  ];
  
  // Add Admin link for admin users
  if (authStore.isAdmin) {
    items.unshift({ name: 'Admin Dashboard', href: '/admin', icon: 'ShieldCheckIcon' });
  }
  
  return items;
});

// Check if nav item is active
const isActive = (href) => {
  return route.path.startsWith(href);
};

// Handle search
const handleSearch = () => {
  if (searchQuery.value.trim()) {
    router.push({ name: 'search', query: { q: searchQuery.value } });
    showMobileMenu.value = false;
  }
};

// Handle logout
const handleLogout = async () => {
  await authStore.logout();
  showUserMenu.value = false;
  router.push('/');
};
</script>