<template>
  <div class="bg-white rounded-xl shadow-sm p-6">
    <h2 class="text-xl font-bold text-gray-900 mb-6">Account Settings</h2>

    <!-- Change Email -->
    <div class="mb-8">
      <h3 class="font-semibold text-gray-900 mb-4">Email Address</h3>
      <form @submit.prevent="updateEmail" class="space-y-4">
        <BaseInput
          id="email"
          type="email"
          v-model="emailForm.email"
          :error="emailForm.errors.email"
          label="Email"
        />
        <BaseButton type="submit" :loading="emailForm.processing">
          Update Email
        </BaseButton>
      </form>
    </div>

    <!-- Change Password -->
    <div class="mb-8 pt-8 border-t border-gray-100">
      <h3 class="font-semibold text-gray-900 mb-4">Change Password</h3>
      <form @submit.prevent="updatePassword" class="space-y-4">
        <BaseInput
          id="current_password"
          type="password"
          v-model="passwordForm.current_password"
          :error="passwordForm.errors.current_password"
          label="Current Password"
        />
        <BaseInput
          id="new_password"
          type="password"
          v-model="passwordForm.password"
          :error="passwordForm.errors.password"
          label="New Password"
        />
        <BaseInput
          id="password_confirmation"
          type="password"
          v-model="passwordForm.password_confirmation"
          label="Confirm New Password"
        />
        <BaseButton type="submit" :loading="passwordForm.processing">
          Update Password
        </BaseButton>
      </form>
    </div>

    <!-- Delete Account -->
    <div class="pt-8 border-t border-gray-100">
      <h3 class="font-semibold text-red-600 mb-4">Delete Account</h3>
      <p class="text-gray-600 mb-4">
        Once you delete your account, there is no going back. Please be certain.
      </p>
      <BaseButton variant="danger" @click="deleteAccount">
        Delete Account
      </BaseButton>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/api'
import { useAuthStore } from '@/stores/auth'
import { useToastStore } from '@/stores/toast'

const router = useRouter()
const authStore = useAuthStore()
const toast = useToastStore()

const emailForm = ref({
  email: '',
  errors: {},
  processing: false
})

const passwordForm = ref({
  current_password: '',
  password: '',
  password_confirmation: '',
  errors: {},
  processing: false
})

const fetchUser = async () => {
  try {
    const response = await api.get('/user')
    emailForm.value.email = response.data.email
  } catch (error) {
    console.error('Failed to fetch user:', error)
  }
}

const updateEmail = async () => {
  try {
    emailForm.value.processing = true
    emailForm.value.errors = {}
    
    await api.put('/user/email', { email: emailForm.value.email })
    toast.success('Email updated!')
  } catch (error) {
    if (error.response?.data?.errors) {
      emailForm.value.errors = error.response.data.errors
    } else {
      toast.error('Failed to update email')
    }
  } finally {
    emailForm.value.processing = false
  }
}

const updatePassword = async () => {
  try {
    passwordForm.value.processing = true
    passwordForm.value.errors = {}
    
    await api.put('/user/password', passwordForm.value)
    toast.success('Password updated!')
    passwordForm.value.current_password = ''
    passwordForm.value.password = ''
    passwordForm.value.password_confirmation = ''
  } catch (error) {
    if (error.response?.data?.errors) {
      passwordForm.value.errors = error.response.data.errors
    } else {
      toast.error('Failed to update password')
    }
  } finally {
    passwordForm.value.processing = false
  }
}

const deleteAccount = async () => {
  if (!confirm('Are you sure you want to delete your account? This action cannot be undone.')) return
  
  try {
    await api.delete('/user')
    authStore.logout()
    toast.success('Account deleted')
    router.push('/')
  } catch (error) {
    toast.error('Failed to delete account')
  }
}

onMounted(() => {
  fetchUser()
})
</script>
