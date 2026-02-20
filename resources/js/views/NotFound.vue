<template>
  <div class="min-h-screen bg-gray-50 flex flex-col items-center justify-center px-4">
    <div class="text-center">
      <h1 class="text-9xl font-bold text-primary-500">404</h1>
      <h2 class="text-2xl font-semibold text-gray-900 mt-4">Page Not Found</h2>
      <p class="text-gray-600 mt-2">Sorry, the page you're looking for doesn't exist or has been moved.</p>
      <div class="mt-8 flex justify-center gap-4">
        <router-link
          to="/"
          class="inline-flex items-center px-6 py-3 bg-primary-500 hover:bg-primary-600 text-white font-medium rounded-lg transition-colors"
        >
          <HomeIcon class="h-5 w-5 mr-2" />
          Go Home
        </router-link>
        <button
          @click="$router.back()"
          class="inline-flex items-center px-6 py-3 bg-white hover:bg-gray-50 text-gray-700 font-medium rounded-lg border border-gray-300 transition-colors"
        >
          <ArrowLeftIcon class="h-5 w-5 mr-2" />
          Go Back
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { HomeIcon, ArrowLeftIcon } from '@heroicons/vue/20/solid'
</script>
