<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
          <div>
            <h1 class="text-2xl font-bold text-gray-900">Events & Meetups</h1>
            <p class="text-gray-600 mt-1">Connect with developers at events across Uganda</p>
          </div>
          <router-link
            to="/events/create"
            class="inline-flex items-center justify-center px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white font-medium rounded-lg transition-colors"
          >
            <PlusIcon class="h-5 w-5 mr-2" />
            Create Event
          </router-link>
        </div>
      </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Filters -->
      <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
        <div class="flex flex-wrap items-center gap-4">
          <select
            v-model="filters.type"
            class="rounded-lg border-gray-300 text-sm focus:ring-primary-500 focus:border-primary-500"
          >
            <option value="">All Types</option>
            <option value="meetup">Meetup</option>
            <option value="workshop">Workshop</option>
            <option value="conference">Conference</option>
            <option value="hackathon">Hackathon</option>
          </select>
          <select
            v-model="filters.location"
            class="rounded-lg border-gray-300 text-sm focus:ring-primary-500 focus:border-primary-500"
          >
            <option value="">All Locations</option>
            <option v-for="loc in locations" :key="loc" :value="loc">{{ loc }}</option>
          </select>
          <select
            v-model="filters.time"
            class="rounded-lg border-gray-300 text-sm focus:ring-primary-500 focus:border-primary-500"
          >
            <option value="">All Time</option>
            <option value="upcoming">Upcoming</option>
            <option value="past">Past</option>
            <option value="today">Today</option>
            <option value="week">This Week</option>
          </select>
        </div>
      </div>

      <!-- Events Grid -->
      <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        <EventCard
          v-for="event in events.data"
          :key="event.id"
          :event="event"
        />
      </div>

      <EmptyState
        v-if="!events.data || events.data.length === 0"
        title="No events found"
        description="Be the first to create an event!"
      >
        <template #action>
          <router-link
            to="/events/create"
            class="inline-flex items-center px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white font-medium rounded-lg transition-colors"
          >
            Create Event
          </router-link>
        </template>
      </EmptyState>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import { PlusIcon } from '@heroicons/vue/20/solid'
import EventCard from '@/components/EventCard.vue'
import api from '@/api'

const events = ref({ data: [], meta: {} })
const locations = ref(['Kampala', 'Entebbe', 'Jinja', 'Mbarara', 'Gulu', 'Online'])

const filters = ref({
  type: '',
  location: '',
  time: 'upcoming',
  page: 1
})

const fetchEvents = async () => {
  try {
    const params = new URLSearchParams()
    if (filters.value.type) params.append('type', filters.value.type)
    if (filters.value.location) params.append('location', filters.value.location)
    if (filters.value.time) params.append('time', filters.value.time)
    params.append('page', filters.value.page)

    const response = await api.get(`/events?${params.toString()}`)
    events.value = response.data
  } catch (error) {
    console.error('Failed to fetch events:', error)
  }
}

watch([() => filters.value.type, () => filters.value.location, () => filters.value.time], () => {
  filters.value.page = 1
  fetchEvents()
})

onMounted(() => {
  fetchEvents()
})
</script>
