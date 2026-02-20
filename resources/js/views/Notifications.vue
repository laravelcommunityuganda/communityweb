<template>
  <div class="min-h-screen bg-gray-50">
    <main class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Notifications</h1>
        <BaseButton
          v-if="notifications.length"
          variant="secondary"
          size="sm"
          @click="markAllAsRead"
        >
          Mark all as read
        </BaseButton>
      </div>

      <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div v-if="notifications.length" class="divide-y divide-gray-100">
          <div
            v-for="notification in notifications"
            :key="notification.id"
            :class="[
              'p-4 hover:bg-gray-50 cursor-pointer transition-colors',
              !notification.read_at && 'bg-primary-50'
            ]"
            @click="handleClick(notification)"
          >
            <div class="flex items-start gap-4">
              <div class="flex-shrink-0">
                <div
                  :class="[
                    'h-10 w-10 rounded-full flex items-center justify-center',
                    getNotificationColor(notification.type)
                  ]"
                >
                  <component :is="getNotificationIcon(notification.type)" class="h-5 w-5 text-white" />
                </div>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-sm text-gray-900" v-html="notification.message"></p>
                <p class="text-xs text-gray-500 mt-1">
                  {{ $filters.relativeTime(notification.created_at) }}
                </p>
              </div>
              <button
                @click.stop="deleteNotification(notification.id)"
                class="text-gray-400 hover:text-gray-600"
              >
                <XMarkIcon class="h-5 w-5" />
              </button>
            </div>
          </div>
        </div>

        <EmptyState
          v-else
          title="No notifications"
          description="You're all caught up!"
        />
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import {
  XMarkIcon,
  ChatBubbleLeftIcon,
  UserPlusIcon,
  BriefcaseIcon,
  CalendarIcon,
  BellIcon
} from '@heroicons/vue/20/solid'
import api from '@/api'
import { useToastStore } from '@/stores/toast'

const router = useRouter()
const toast = useToastStore()

const notifications = ref([])

const fetchNotifications = async () => {
  try {
    const response = await api.get('/notifications')
    notifications.value = response.data.data || response.data
  } catch (error) {
    console.error('Failed to fetch notifications:', error)
  }
}

const handleClick = async (notification) => {
  if (!notification.read_at) {
    await markAsRead(notification.id)
  }
  
  if (notification.url) {
    router.push(notification.url)
  }
}

const markAsRead = async (id) => {
  try {
    await api.put(`/notifications/${id}/read`)
    const notification = notifications.value.find(n => n.id === id)
    if (notification) {
      notification.read_at = new Date().toISOString()
    }
  } catch (error) {
    console.error('Failed to mark as read:', error)
  }
}

const markAllAsRead = async () => {
  try {
    await api.put('/notifications/read-all')
    notifications.value.forEach(n => {
      n.read_at = n.read_at || new Date().toISOString()
    })
    toast.success('All notifications marked as read')
  } catch (error) {
    toast.error('Failed to mark notifications as read')
  }
}

const deleteNotification = async (id) => {
  try {
    await api.delete(`/notifications/${id}`)
    notifications.value = notifications.value.filter(n => n.id !== id)
  } catch (error) {
    toast.error('Failed to delete notification')
  }
}

const getNotificationIcon = (type) => {
  const icons = {
    'reply': ChatBubbleLeftIcon,
    'mention': ChatBubbleLeftIcon,
    'follow': UserPlusIcon,
    'job': BriefcaseIcon,
    'event': CalendarIcon
  }
  return icons[type] || BellIcon
}

const getNotificationColor = (type) => {
  const colors = {
    'reply': 'bg-blue-500',
    'mention': 'bg-purple-500',
    'follow': 'bg-green-500',
    'job': 'bg-yellow-500',
    'event': 'bg-primary-500'
  }
  return colors[type] || 'bg-gray-500'
}

onMounted(() => {
  fetchNotifications()
})
</script>
