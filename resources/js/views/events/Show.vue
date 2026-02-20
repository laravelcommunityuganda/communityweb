<template>
  <div class="min-h-screen bg-gray-50">
    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <LoadingSpinner v-if="loading" class="py-20" />
      
      <template v-else-if="event">
        <!-- Back Button -->
        <router-link to="/events" class="inline-flex items-center text-gray-600 hover:text-gray-900 mb-6">
          <ArrowLeftIcon class="h-5 w-5 mr-2" />
          Back to Events
        </router-link>

        <!-- Event Hero -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6">
          <div v-if="event.banner" class="h-48 bg-cover bg-center" :style="`background-image: url(${event.banner})`"></div>
          <div v-else class="h-48 bg-gradient-to-r from-primary-500 to-primary-600"></div>
          
          <div class="p-6">
            <div class="flex items-start justify-between mb-4">
              <div>
                <BaseBadge :variant="getEventVariant(event.type)">{{ event.type }}</BaseBadge>
                <h1 class="text-2xl font-bold text-gray-900 mt-2">{{ event.title }}</h1>
              </div>
              <div class="text-right">
                <p class="text-2xl font-bold text-primary-500">{{ formatPrice(event.price) }}</p>
              </div>
            </div>

            <!-- Quick Info -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6 p-4 bg-gray-50 rounded-lg">
              <div class="flex items-center gap-2">
                <CalendarIcon class="h-5 w-5 text-gray-400" />
                <div>
                  <p class="text-sm text-gray-500">Date</p>
                  <p class="font-medium text-gray-900">{{ $filters.date(event.start_date) }}</p>
                </div>
              </div>
              <div class="flex items-center gap-2">
                <ClockIcon class="h-5 w-5 text-gray-400" />
                <div>
                  <p class="text-sm text-gray-500">Time</p>
                  <p class="font-medium text-gray-900">{{ $filters.time(event.start_date) }}</p>
                </div>
              </div>
              <div class="flex items-center gap-2">
                <MapPinIcon class="h-5 w-5 text-gray-400" />
                <div>
                  <p class="text-sm text-gray-500">Location</p>
                  <p class="font-medium text-gray-900">{{ event.location }}</p>
                </div>
              </div>
              <div class="flex items-center gap-2">
                <UsersIcon class="h-5 w-5 text-gray-400" />
                <div>
                  <p class="text-sm text-gray-500">Attendees</p>
                  <p class="font-medium text-gray-900">{{ event.attendees_count || 0 }}/{{ event.capacity || '∞' }}</p>
                </div>
              </div>
            </div>

            <!-- Description -->
            <div class="prose prose-primary max-w-none mb-6">
              <div v-html="event.description"></div>
            </div>

            <!-- RSVP -->
            <div class="flex items-center gap-4 pt-6 border-t border-gray-100">
              <BaseButton
                @click="rsvp"
                :loading="rsvping"
                :disabled="isPastEvent"
                size="lg"
              >
                {{ isAttending ? 'Cancel RSVP' : 'RSVP Now' }}
              </BaseButton>
              <span v-if="isPastEvent" class="text-gray-500">This event has passed</span>
            </div>
          </div>
        </div>

        <!-- Organizer -->
        <div class="bg-white rounded-xl shadow-sm p-6">
          <h3 class="font-semibold text-gray-900 mb-4">Organizer</h3>
          <div class="flex items-center gap-4">
            <BaseAvatar
              :src="event.organizer?.avatar"
              :name="event.organizer?.name"
              size="lg"
            />
            <div>
              <p class="font-medium text-gray-900">{{ event.organizer?.name }}</p>
              <p class="text-sm text-gray-500">{{ event.organizer?.email }}</p>
            </div>
          </div>
        </div>
      </template>

      <EmptyState
        v-else
        title="Event not found"
        description="The event you're looking for doesn't exist or has been removed."
      >
        <template #action>
          <router-link to="/events" class="text-primary-500 hover:text-primary-600">
            Browse Events
          </router-link>
        </template>
      </EmptyState>
    </main>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import {
  ArrowLeftIcon,
  CalendarIcon,
  ClockIcon,
  MapPinIcon,
  UsersIcon
} from '@heroicons/vue/20/solid'
import api from '@/api'
import { useAuthStore } from '@/stores/auth'
import { useToastStore } from '@/stores/toast'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()
const toast = useToastStore()

const loading = ref(true)
const event = ref(null)
const rsvping = ref(false)

const isPastEvent = computed(() => {
  if (!event.value) return false
  return new Date(event.value.start_date) < new Date()
})

const isAttending = computed(() => event.value?.is_attending)

const fetchEvent = async () => {
  try {
    loading.value = true
    const response = await api.get(`/events/${route.params.slug}`)
    event.value = response.data.data || response.data
  } catch (error) {
    console.error('Failed to fetch event:', error)
  } finally {
    loading.value = false
  }
}

const rsvp = async () => {
  if (!authStore.isAuthenticated) {
    router.push({ name: 'login', query: { redirect: route.fullPath } })
    return
  }
  
  try {
    rsvping.value = true
    await api.post(`/events/${event.value.id}/rsvp`)
    event.value.is_attending = !event.value.is_attending
    if (event.value.is_attending) {
      event.value.attendees_count = (event.value.attendees_count || 0) + 1
      toast.success('RSVP confirmed!')
    } else {
      event.value.attendees_count = Math.max(0, (event.value.attendees_count || 1) - 1)
      toast.success('RSVP cancelled')
    }
  } catch (error) {
    toast.error(error.response?.data?.message || 'Failed to RSVP')
  } finally {
    rsvping.value = false
  }
}

const getEventVariant = (type) => {
  const variants = {
    'meetup': 'primary',
    'workshop': 'success',
    'conference': 'info',
    'hackathon': 'warning'
  }
  return variants[type] || 'default'
}

const formatPrice = (price) => {
  if (!price || price === 0) return 'Free'
  return `UGX ${price.toLocaleString()}`
}

onMounted(() => {
  fetchEvent()
})
</script>
