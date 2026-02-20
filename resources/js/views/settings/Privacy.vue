<template>
  <div class="bg-white rounded-xl shadow-sm p-6">
    <h2 class="text-xl font-bold text-gray-900 mb-6">Privacy Settings</h2>

    <form @submit.prevent="submit">
      <div class="space-y-6">
        <!-- Profile Visibility -->
        <div>
          <h3 class="font-semibold text-gray-900 mb-4">Profile Visibility</h3>
          <div class="space-y-4">
            <label class="flex items-center gap-3">
              <input
                type="radio"
                v-model="form.profile_visibility"
                value="public"
                class="border-gray-300 text-primary-500 focus:ring-primary-500"
              />
              <div>
                <span class="text-gray-700 font-medium">Public</span>
                <p class="text-sm text-gray-500">Anyone can view your profile</p>
              </div>
            </label>
            <label class="flex items-center gap-3">
              <input
                type="radio"
                v-model="form.profile_visibility"
                value="members"
                class="border-gray-300 text-primary-500 focus:ring-primary-500"
              />
              <div>
                <span class="text-gray-700 font-medium">Members Only</span>
                <p class="text-sm text-gray-500">Only logged-in members can view your profile</p>
              </div>
            </label>
            <label class="flex items-center gap-3">
              <input
                type="radio"
                v-model="form.profile_visibility"
                value="private"
                class="border-gray-300 text-primary-500 focus:ring-primary-500"
              />
              <div>
                <span class="text-gray-700 font-medium">Private</span>
                <p class="text-sm text-gray-500">Only you can view your profile</p>
              </div>
            </label>
          </div>
        </div>

        <!-- Activity -->
        <div class="pt-6 border-t border-gray-100">
          <h3 class="font-semibold text-gray-900 mb-4">Activity</h3>
          <div class="space-y-4">
            <label class="flex items-center gap-3">
              <input
                type="checkbox"
                v-model="form.show_online_status"
                class="rounded border-gray-300 text-primary-500 focus:ring-primary-500"
              />
              <span class="text-gray-700">Show my online status</span>
            </label>
            <label class="flex items-center gap-3">
              <input
                type="checkbox"
                v-model="form.show_activity"
                class="rounded border-gray-300 text-primary-500 focus:ring-primary-500"
              />
              <span class="text-gray-700">Show my activity on my profile</span>
            </label>
          </div>
        </div>

        <!-- Messaging -->
        <div class="pt-6 border-t border-gray-100">
          <h3 class="font-semibold text-gray-900 mb-4">Messaging</h3>
          <div class="space-y-4">
            <label class="flex items-center gap-3">
              <input
                type="checkbox"
                v-model="form.allow_messages"
                class="rounded border-gray-300 text-primary-500 focus:ring-primary-500"
              />
              <span class="text-gray-700">Allow others to send me messages</span>
            </label>
            <label class="flex items-center gap-3">
              <input
                type="checkbox"
                v-model="form.allow_mentions"
                class="rounded border-gray-300 text-primary-500 focus:ring-primary-500"
              />
              <span class="text-gray-700">Allow others to mention me in posts</span>
            </label>
          </div>
        </div>

        <!-- Blocked Users -->
        <div class="pt-6 border-t border-gray-100">
          <h3 class="font-semibold text-gray-900 mb-4">Blocked Users</h3>
          <p class="text-gray-600 mb-4">Manage users you've blocked</p>
          <div v-if="blockedUsers.length" class="space-y-2">
            <div
              v-for="user in blockedUsers"
              :key="user.id"
              class="flex items-center justify-between p-3 bg-gray-50 rounded-lg"
            >
              <div class="flex items-center gap-3">
                <BaseAvatar :src="user.avatar" :name="user.name" size="sm" />
                <span class="font-medium">{{ user.name }}</span>
              </div>
              <BaseButton size="sm" variant="secondary" @click="unblock(user.id)">
                Unblock
              </BaseButton>
            </div>
          </div>
          <p v-else class="text-gray-500">You haven't blocked any users.</p>
        </div>

        <!-- Submit -->
        <div class="flex justify-end pt-4 border-t border-gray-100">
          <BaseButton type="submit" :loading="form.processing">
            Save Settings
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
  profile_visibility: 'public',
  show_online_status: true,
  show_activity: true,
  allow_messages: true,
  allow_mentions: true,
  processing: false
})

const blockedUsers = ref([])

const fetchSettings = async () => {
  try {
    const response = await api.get('/user/privacy-settings')
    Object.assign(form.value, response.data.settings || response.data)
    blockedUsers.value = response.data.blocked_users || []
  } catch (error) {
    console.error('Failed to fetch settings:', error)
  }
}

const unblock = async (userId) => {
  try {
    await api.delete(`/users/${userId}/unblock`)
    blockedUsers.value = blockedUsers.value.filter(u => u.id !== userId)
    toast.success('User unblocked')
  } catch (error) {
    toast.error('Failed to unblock user')
  }
}

const submit = async () => {
  try {
    form.value.processing = true
    await api.put('/user/privacy-settings', form.value)
    toast.success('Privacy settings saved!')
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
