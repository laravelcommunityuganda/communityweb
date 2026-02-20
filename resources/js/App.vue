<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Skip to main content link for accessibility -->
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 bg-primary-600 text-white px-4 py-2 rounded-lg z-50">
      Skip to main content
    </a>

    <!-- Navigation (hidden for admin pages) -->
    <TheNavbar v-if="!isAdminRoute" />

    <!-- Main Content -->
    <main id="main-content" :class="isAdminRoute ? '' : 'pt-16'">
      <router-view v-slot="{ Component }">
        <transition name="fade" mode="out-in">
          <component :is="Component" />
        </transition>
      </router-view>
    </main>

    <!-- Footer (hidden for admin pages) -->
    <TheFooter v-if="!isAdminRoute" />

    <!-- Toast Notifications -->
    <BaseToast />

    <!-- Back to top button -->
    <button
      v-show="showBackToTop"
      @click="scrollToTop"
      class="fixed bottom-6 right-6 z-40 p-3 bg-primary-600 text-white rounded-full shadow-lg hover:bg-primary-700 transition-all duration-200"
      aria-label="Back to top"
    >
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
      </svg>
    </button>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, provide } from 'vue';
import { useRoute } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import TheNavbar from '@/components/layout/TheNavbar.vue';
import TheFooter from '@/components/layout/TheFooter.vue';

// Initialize auth store
const authStore = useAuthStore();
const route = useRoute();

// Check if current route is admin route
const isAdminRoute = computed(() => {
  return route.path.startsWith('/admin');
});

// Back to top button
const showBackToTop = ref(false);

const handleScroll = () => {
  showBackToTop.value = window.scrollY > 300;
};

const scrollToTop = () => {
  window.scrollTo({ top: 0, behavior: 'smooth' });
};

// Dark mode
const isDark = ref(localStorage.getItem('darkMode') === 'true' || 
  (!localStorage.getItem('darkMode') && window.matchMedia('(prefers-color-scheme: dark)').matches));

const toggleDarkMode = () => {
  isDark.value = !isDark.value;
  localStorage.setItem('darkMode', isDark.value);
  updateDarkMode();
};

const updateDarkMode = () => {
  if (isDark.value) {
    document.documentElement.classList.add('dark');
  } else {
    document.documentElement.classList.remove('dark');
  }
};

// Provide dark mode to all components
provide('darkMode', {
  isDark,
  toggleDarkMode,
});

// Initialize
onMounted(async () => {
  // Set up scroll listener
  window.addEventListener('scroll', handleScroll);
  
  // Initialize dark mode
  updateDarkMode();
  
  // Fetch user if token exists
  if (authStore.token && !authStore.user) {
    try {
      await authStore.fetchUser();
    } catch (error) {
      console.error('Failed to fetch user:', error);
    }
  }
});

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll);
});
</script>

<style>
/* Page transition animations */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

/* Slide animations */
.slide-up-enter-active,
.slide-up-leave-active {
  transition: all 0.3s ease;
}

.slide-up-enter-from {
  opacity: 0;
  transform: translateY(20px);
}

.slide-up-leave-to {
  opacity: 0;
  transform: translateY(-20px);
}
</style>