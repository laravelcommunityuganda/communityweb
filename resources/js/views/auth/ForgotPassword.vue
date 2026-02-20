<template>
  <div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
      <router-link to="/" class="flex justify-center">
        <svg class="h-12 w-auto" viewBox="0 0 50 52" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M49.626 11.564a.809.809 0 01.028.209v10.972a.8.8 0 01-.402.694l-9.209 5.302V39.25c0 .286-.152.55-.4.694L20.42 51.01c-.044.025-.092.041-.14.058-.018.006-.035.017-.054.022a.805.805 0 01-.41 0c-.022-.006-.042-.018-.063-.026-.044-.016-.09-.03-.132-.054L.402 39.944A.801.801 0 010 39.25V6.334c0-.072.01-.142.028-.21.005-.021.018-.04.026-.06.015-.037.028-.075.049-.11.014-.023.036-.04.053-.06.023-.027.044-.055.071-.078.022-.018.049-.03.073-.046.029-.018.055-.04.087-.053l9.61-4.914a.802.802 0 01.736 0l9.61 4.914c.032.013.058.035.087.053.024.016.05.028.073.046.027.023.048.051.071.078.017.02.04.037.053.06.021.035.034.073.05.11.007.02.02.039.025.06.02.068.028.138.028.21v20.559l8.008-4.611V11.773c0-.07.01-.141.028-.209.006-.02.018-.04.026-.06.015-.037.028-.075.049-.11.014-.022.036-.04.053-.06.023-.027.044-.054.071-.077.022-.018.049-.03.073-.046.029-.018.055-.04.087-.053l9.611-4.914a.802.802 0 01.736 0l9.61 4.914c.032.013.058.035.087.053.024.016.05.028.073.046.027.023.048.05.071.077.017.02.04.038.053.06.021.035.034.073.05.11.007.02.02.04.025.06z" fill="#FF2D20"/>
        </svg>
      </router-link>
      <h2 class="mt-6 text-center text-3xl font-bold tracking-tight text-gray-900">
        Reset your password
      </h2>
      <p class="mt-2 text-center text-sm text-gray-600">
        Or
        <router-link to="/login" class="font-medium text-primary-500 hover:text-primary-600">
          back to login
        </router-link>
      </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
      <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
        <div v-if="status" class="mb-4 p-4 bg-green-50 rounded-lg">
          <p class="text-sm text-green-600">{{ status }}</p>
        </div>

        <form @submit.prevent="submitEmail" v-if="!emailSent">
          <div class="space-y-6">
            <BaseInput
              id="email"
              type="email"
              label="Email address"
              v-model="email"
              :error="errors.email"
              required
              autocomplete="email"
              placeholder="Enter your email address"
            />

            <div>
              <BaseButton
                type="submit"
                :loading="loading"
                class="w-full"
              >
                Send Password Reset Link
              </BaseButton>
            </div>
          </div>
        </form>

        <div v-else class="text-center">
          <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
            <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
          </div>
          <h3 class="text-lg font-medium text-gray-900 mb-2">Check your email</h3>
          <p class="text-sm text-gray-600 mb-6">
            We've sent a password reset link to <strong>{{ email }}</strong>
          </p>
          <BaseButton
            @click="emailSent = false"
            variant="secondary"
            class="w-full"
          >
            Try another email
          </BaseButton>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import api from '@/api'
import { useToastStore } from '@/stores/toast'

const toast = useToastStore()

const email = ref('')
const emailSent = ref(false)
const status = ref('')
const loading = ref(false)
const errors = ref({})

const submitEmail = async () => {
  try {
    loading.value = true
    errors.value = {}
    
    await api.post('/auth/forgot-password', { email: email.value })
    emailSent.value = true
  } catch (error) {
    if (error.response?.data?.errors) {
      errors.value = error.response.data.errors
    } else {
      toast.error('Failed to send reset link')
    }
  } finally {
    loading.value = false
  }
}
</script>
