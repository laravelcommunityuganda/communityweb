<template>
  <Teleport to="body">
    <div
      class="fixed z-50 flex flex-col gap-2 pointer-events-none"
      :class="positionClasses"
    >
      <TransitionGroup
        name="toast"
        tag="div"
        class="flex flex-col gap-2"
      >
        <div
          v-for="toast in toasts"
          :key="toast.id"
          :class="getToastClasses(toast.type)"
          class="pointer-events-auto flex items-center gap-3 px-4 py-3 rounded-lg shadow-lg min-w-[300px] max-w-md"
        >
          <component
            :is="getIcon(toast.type)"
            class="h-5 w-5 flex-shrink-0"
          />
          <div class="flex-1">
            <p v-if="toast.title" class="font-medium text-sm">{{ toast.title }}</p>
            <p class="text-sm opacity-90">{{ toast.message }}</p>
          </div>
          <button
            @click="removeToast(toast.id)"
            class="flex-shrink-0 p-1 rounded hover:bg-black/10 transition-colors"
          >
            <XMarkIcon class="h-4 w-4" />
          </button>
        </div>
      </TransitionGroup>
    </div>
  </Teleport>
</template>

<script setup>
import { computed } from 'vue'
import { useToastStore } from '@/stores/toast'
import {
  CheckCircleIcon,
  ExclamationCircleIcon,
  ExclamationTriangleIcon,
  InformationCircleIcon,
  XMarkIcon
} from '@heroicons/vue/20/solid'

const toastStore = useToastStore()

const props = defineProps({
  position: {
    type: String,
    default: 'top-right',
    validator: (value) => ['top-right', 'top-left', 'bottom-right', 'bottom-left'].includes(value)
  }
})

const toasts = computed(() => toastStore.toasts)

const positionClasses = computed(() => {
  const positions = {
    'top-right': 'top-4 right-4',
    'top-left': 'top-4 left-4',
    'bottom-right': 'bottom-4 right-4',
    'bottom-left': 'bottom-4 left-4'
  }
  return positions[props.position]
})

const getToastClasses = (type) => {
  const types = {
    success: 'bg-green-500 text-white',
    error: 'bg-red-500 text-white',
    warning: 'bg-yellow-500 text-white',
    info: 'bg-blue-500 text-white'
  }
  return types[type] || types.info
}

const getIcon = (type) => {
  const icons = {
    success: CheckCircleIcon,
    error: ExclamationCircleIcon,
    warning: ExclamationTriangleIcon,
    info: InformationCircleIcon
  }
  return icons[type] || icons.info
}

const removeToast = (id) => {
  toastStore.remove(id)
}
</script>

<style scoped>
.toast-enter-active,
.toast-leave-active {
  transition: all 0.3s ease;
}

.toast-enter-from {
  opacity: 0;
  transform: translateX(100%);
}

.toast-leave-to {
  opacity: 0;
  transform: translateX(100%);
}
</style>
