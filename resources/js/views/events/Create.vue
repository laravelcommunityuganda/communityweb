<template>
  <div class="min-h-screen bg-gray-50">
    <main class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="bg-white rounded-xl shadow-sm p-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Create New Event</h1>

        <form @submit.prevent="submit">
          <div class="space-y-6">
            <!-- Title -->
            <BaseInput
              id="title"
              label="Event Title"
              v-model="form.title"
              :error="form.errors.title"
              placeholder="e.g. Laravel Uganda Meetup 2024"
              required
            />

            <!-- Type & Location -->
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                  Event Type <span class="text-red-500">*</span>
                </label>
                <select
                  v-model="form.type"
                  class="w-full rounded-lg border-gray-300 focus:ring-primary-500 focus:border-primary-500"
                >
                  <option value="">Select type</option>
                  <option value="meetup">Meetup</option>
                  <option value="workshop">Workshop</option>
                  <option value="conference">Conference</option>
                  <option value="hackathon">Hackathon</option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                <select
                  v-model="form.location"
                  class="w-full rounded-lg border-gray-300 focus:ring-primary-500 focus:border-primary-500"
                >
                  <option value="">Select location</option>
                  <option v-for="loc in locations" :key="loc" :value="loc">{{ loc }}</option>
                </select>
              </div>
            </div>

            <!-- Date & Time -->
            <div class="grid grid-cols-2 gap-4">
              <BaseInput
                id="start_date"
                label="Start Date & Time"
                v-model="form.start_date"
                type="datetime-local"
                required
              />
              <BaseInput
                id="end_date"
                label="End Date & Time"
                v-model="form.end_date"
                type="datetime-local"
              />
            </div>

            <!-- Capacity & Price -->
            <div class="grid grid-cols-2 gap-4">
              <BaseInput
                id="capacity"
                label="Capacity"
                v-model="form.capacity"
                type="number"
                placeholder="Leave empty for unlimited"
              />
              <BaseInput
                id="price"
                label="Price (UGX)"
                v-model="form.price"
                type="number"
                placeholder="0 for free events"
              />
            </div>

            <!-- Description -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                Description <span class="text-red-500">*</span>
              </label>
              <textarea
                v-model="form.description"
                rows="6"
                class="w-full rounded-lg border-gray-300 focus:ring-primary-500 focus:border-primary-500"
                placeholder="Describe your event, what attendees will learn, agenda, etc."
              ></textarea>
              <p v-if="form.errors.description" class="mt-1 text-sm text-red-500">
                {{ form.errors.description }}
              </p>
            </div>

            <!-- Submit -->
            <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-100">
              <router-link to="/events" class="px-4 py-2 text-gray-700 hover:text-gray-900">
                Cancel
              </router-link>
              <BaseButton type="submit" :loading="form.processing">
                Create Event
              </BaseButton>
            </div>
          </div>
        </form>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/api'
import { useToastStore } from '@/stores/toast'

const router = useRouter()
const toast = useToastStore()

const locations = ref(['Kampala', 'Entebbe', 'Jinja', 'Mbarara', 'Gulu', 'Online'])

const form = ref({
  title: '',
  type: '',
  location: '',
  start_date: '',
  end_date: '',
  capacity: '',
  price: '',
  description: '',
  errors: {},
  processing: false
})

const submit = async () => {
  try {
    form.value.processing = true
    form.value.errors = {}
    
    const response = await api.post('/events', form.value)
    
    toast.success('Event created successfully!')
    router.push(`/events/${response.data.data.slug}`)
  } catch (error) {
    if (error.response?.data?.errors) {
      form.value.errors = error.response.data.errors
    } else {
      toast.error('Failed to create event')
    }
  } finally {
    form.value.processing = false
  }
}
</script>
