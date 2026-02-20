<template>
  <div class="relative inline-block">
    <img
      v-if="src"
      :src="src"
      :alt="alt"
      :class="avatarClasses"
      class="object-cover"
    />
    <div
      v-else
      :class="avatarClasses"
      class="bg-primary-500 flex items-center justify-center text-white font-medium"
    >
      {{ initials }}
    </div>
    <span
      v-if="status"
      :class="statusClasses"
      class="absolute bottom-0 right-0 block rounded-full ring-2 ring-white"
    ></span>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  src: {
    type: String,
    default: ''
  },
  alt: {
    type: String,
    default: ''
  },
  name: {
    type: String,
    default: ''
  },
  size: {
    type: String,
    default: 'md',
    validator: (value) => ['xs', 'sm', 'md', 'lg', 'xl'].includes(value)
  },
  status: {
    type: String,
    default: '',
    validator: (value) => ['', 'online', 'offline', 'busy', 'away'].includes(value)
  }
})

const sizes = {
  xs: 'h-6 w-6 text-xs',
  sm: 'h-8 w-8 text-sm',
  md: 'h-10 w-10 text-base',
  lg: 'h-12 w-12 text-lg',
  xl: 'h-16 w-16 text-xl'
}

const statusSizes = {
  xs: 'h-1.5 w-1.5',
  sm: 'h-2 w-2',
  md: 'h-2.5 w-2.5',
  lg: 'h-3 w-3',
  xl: 'h-4 w-4'
}

const statusColors = {
  online: 'bg-green-400',
  offline: 'bg-gray-300',
  busy: 'bg-red-400',
  away: 'bg-yellow-400'
}

const avatarClasses = computed(() => {
  return [
    'rounded-full',
    sizes[props.size]
  ].join(' ')
})

const statusClasses = computed(() => {
  return [
    statusSizes[props.size],
    statusColors[props.status]
  ].join(' ')
})

const initials = computed(() => {
  if (!props.name) return '?'
  const parts = props.name.split(' ')
  if (parts.length >= 2) {
    return (parts[0][0] + parts[1][0]).toUpperCase()
  }
  return props.name.substring(0, 2).toUpperCase()
})
</script>
