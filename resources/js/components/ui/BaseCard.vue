<template>
  <div :class="cardClasses">
    <div v-if="$slots.header" class="px-6 py-4 border-b border-gray-100">
      <slot name="header" />
    </div>
    <div :class="bodyClasses">
      <slot />
    </div>
    <div v-if="$slots.footer" class="px-6 py-4 border-t border-gray-100 bg-gray-50 rounded-b-xl">
      <slot name="footer" />
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  padding: {
    type: Boolean,
    default: true
  },
  hover: {
    type: Boolean,
    default: false
  }
})

const cardClasses = computed(() => {
  const classes = ['bg-white rounded-xl shadow-sm border border-gray-100']
  if (props.hover) {
    classes.push('hover:shadow-md transition-shadow duration-200')
  }
  return classes.join(' ')
})

const bodyClasses = computed(() => {
  return props.padding ? 'p-6' : ''
})
</script>
