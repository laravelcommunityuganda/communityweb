<template>
  <div class="bg-white rounded-xl shadow-sm p-6">
    <h2 class="text-xl font-bold text-gray-900 mb-6">Notification Settings</h2>

    <form @submit.prevent="submit">
      <div class="space-y-6">
        <!-- Email Notifications -->
        <div>
          <h3 class="font-semibold text-gray-900 mb-4">Email Notifications</h3>
          <div class="space-y-4">
            <label class="flex items-center gap-3">
              <input
                type="checkbox"
                v-model="form.email_replies"
                class="rounded border-gray-300 text-primary-500 focus:ring-primary-500"
              />
              <span class="text-gray-700">Email me when someone replies to my posts</span>
            </label>
            <label class="flex items-center gap-3">
              <input
                type="checkbox"
                v-model="form.email_mentions"
                class="rounded border-gray-300 text-primary-500 focus:ring-primary-500"
              />
              <span class="text-gray-700">Email me when someone mentions me</span>
            </label>
            <label class="flex items-center gap-3">
              <input
                type="checkbox"
                v-model="form.email_followers"
                class="rounded border-gray-300 text-primary-500 focus:ring-primary-500"
              />
              <span class="text-gray-700">Email me when someone follows me</span>
            </label>
            <label class="flex items-center gap-3">
              <input
                type="checkbox"
                v-model="form.email_jobs"
                class="rounded border-gray-300 text-primary-500 focus:ring-primary-500"
              />
              <span class="text-gray-700">Email me about new job opportunities</span>
            </label>
            <label class="flex items-center gap-3">
              <input
                type="checkbox"
                v-model="form.email_events"
                class="rounded border-gray-300 text-primary-500 focus:ring-primary-500"
              />
              <span class="text-gray-700">Email me about upcoming events</span>
            </label>
          </div>
        </div>

        <!-- In-App Notifications -->
        <div class="pt-6 border-t border-gray-100">
          <h3 class="font-semibold text-gray-900 mb-4">In-App Notifications</h3>
          <div class="space-y-4">
            <label class="flex items-center gap-3">
              <input
                type="checkbox"
                v-model="form.app_replies"
                class="rounded border-gray-300 text-primary-500 focus:ring-primary-500"
              />
              <span class="text-gray-700">Notify me about replies to my posts</span>
            </label>
            <label class="flex items-center gap-3">
              <input
                type="checkbox"
                v-model="form.app_mentions"
                class="rounded border-gray-300 text-primary-500 focus:ring-primary-500"
              />
              <span class="text-gray-700">Notify me when someone mentions me</span>
            </label>
            <label class="flex items-center gap-3">
              <input
                type="checkbox"
                v-model="form.app_messages"
                class="rounded border-gray-300 text-primary-500 focus:ring-primary-500"
              />
              <span class="text-gray-700">Notify me about new messages</span>
            </label>
          </div>
        </div>

        <!-- Submit -->
        <div class="flex justify-end pt-4 border-t border-gray-100">
          <BaseButton type="submit" :loading="form.processing">
            Save Preferences
          </BaseButton>
        </div>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/api'
import { useToastStore } from '@/stores/toast'

const toast = useToastStore()

const form = ref({
  email_replies: true,
  email_mentions: true,
  email_followers: true,
  email_jobs: true,
  email_events: true,
  app_replies: true,
  app_mentions: true,
  app_messages: true,
  processing: false
})

const fetchSettings = async () => {
  try {
    const response = await api.get('/user/notification-settings')
    Object.assign(form.value, response.data)
  } catch (error) {
    console.error('Failed to fetch settings:', error)
  }
}

const submit = async () => {
  try {
    form.value.processing = true
    await api.put('/user/notification-settings', form.value)
    toast.success('Notification settings saved!')
  } catch (error) {
    toast.error('Failed to save settings')
  } finally {
    form.value.processing = false
  }
}

onMounted(() => {
  fetchSettings()
})
</script>
