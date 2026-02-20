<template>
  <div :class="containerClasses">
    <svg
      :class="spinnerClasses"
      class="animate-spin"
      xmlns="http://www.w3.org/2000/svg"
      fill="none"
      viewBox="0 0 24 24"
    >
      <circle
        class="opacity-25"
        cx="12"
        cy="12"
        r="10"
        stroke="currentColor"
        stroke-width="4"
      ></circle>
      <path
        class="opacity-75"
        fill="currentColor"
        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
      ></path>
    </svg>
    <p v-if="text" :class="textClasses">{{ text }}</p>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  size: {
    type: String,
    default: 'md',
    validator: (value) => ['sm', 'md', 'lg', 'xl'].includes(value)
  },
  text: {
    type: String,
    default: ''
  },
  color: {
    type: String,
    default: 'primary',
    validator: (value) => ['primary', 'white', 'gray'].includes(value)
  },
  fullScreen: {
    type: Boolean,
    default: false
  }
})

const sizes = {
  sm: 'h-4 w-4',
  md: 'h-8 w-8',
  lg: 'h-12 w-12',
  xl: 'h-16 w-16'
}

const textSizes = {
  sm: 'text-xs',
  md: 'text-sm',
  lg: 'text-base',
  xl: 'text-lg'
}

const colors = {
  primary: 'text-primary-500',
  white: 'text-white',
  gray: 'text-gray-400'
}

const spinnerClasses = computed(() => {
  return [sizes[props.size], colors[props.color]].join(' ')
})

const textClasses = computed(() => {
  return [textSizes[props.size], colors[props.color], 'mt-2'].join(' ')
})

const containerClasses = computed(() => {
  const classes = ['flex flex-col items-center justify-center']
  if (props.fullScreen) {
    classes.push('fixed inset-0 bg-white/80 z-50')
  }
  return classes.join(' ')
})
</script>
