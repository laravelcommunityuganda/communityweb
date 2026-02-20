<template>
  <router-link :to="`/events/${event.slug}`" class="block group">
    <div class="flex items-start gap-3">
      <div class="flex-shrink-0 w-12 h-12 bg-primary-100 dark:bg-primary-900 rounded-lg flex flex-col items-center justify-center">
        <span class="text-xs font-medium text-primary-800 dark:text-primary-200">{{ month }}</span>
        <span class="text-lg font-bold text-primary-600 dark:text-primary-400">{{ day }}</span>
      </div>
      <div class="flex-1 min-w-0">
        <h4 class="text-sm font-medium text-gray-900 dark:text-white group-hover:text-primary-600 dark:group-hover:text-primary-400 transition truncate">
          {{ event.title }}
        </h4>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
          {{ event.format === 'online' ? '🌐 Online' : `📍 ${event.venue_city || 'TBD'}` }}
        </p>
        <p class="text-xs text-gray-500 dark:text-gray-400">
          {{ time }}
        </p>
      </div>
    </div>
  </router-link>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  event: {
    type: Object,
    required: true,
  },
})

const month = computed(() => {
  if (!props.event.start_date) return ''
  return new Date(props.event.start_date).toLocaleDateString('en-US', { month: 'short' })
})

const day = computed(() => {
  if (!props.event.start_date) return ''
  return new Date(props.event.start_date).getDate()
})

const time = computed(() => {
  if (!props.event.start_date) return ''
  return new Date(props.event.start_date).toLocaleTimeString('en-US', {
    hour: 'numeric',
    minute: '2-digit',
  })
})
</script>