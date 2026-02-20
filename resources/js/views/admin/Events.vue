<template>
  <div>
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-bold text-gray-900">Events Management</h1>
    </div>

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Event</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Attendees</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <tr v-for="event in events.data" :key="event.id">
            <td class="px-6 py-4">
              <p class="font-medium text-gray-900">{{ event.title }}</p>
              <p class="text-sm text-gray-500">{{ event.location }}</p>
            </td>
            <td class="px-6 py-4">
              <BaseBadge>{{ event.type }}</BaseBadge>
            </td>
            <td class="px-6 py-4 text-sm text-gray-500">{{ $filters.date(event.start_date) }}</td>
            <td class="px-6 py-4 text-sm text-gray-500">{{ event.attendees_count || 0 }}</td>
            <td class="px-6 py-4 text-right">
              <BaseDropdown :items="getEventActions(event)" variant="ghost" />
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/api'
import { useToastStore } from '@/stores/toast'

const toast = useToastStore()
const events = ref({ data: [], meta: {} })

const fetchEvents = async () => {
  try {
    const response = await api.get('/admin/events')
    events.value = response.data
  } catch (error) {
    console.error('Failed to fetch events:', error)
  }
}

const getEventActions = (event) => [
  { label: 'View', action: () => window.open(`/events/${event.slug}`, '_blank') },
  { label: 'Feature', action: () => featureEvent(event) },
  { label: 'Delete', action: () => deleteEvent(event) }
]

const featureEvent = async (event) => {
  try {
    await api.post(`/admin/events/${event.id}/feature`)
    toast.success('Event featured')
  } catch (error) {
    toast.error('Failed to feature event')
  }
}

const deleteEvent = async (event) => {
  if (!confirm('Are you sure?')) return
  try {
    await api.delete(`/admin/events/${event.id}`)
    events.value.data = events.value.data.filter(e => e.id !== event.id)
    toast.success('Event deleted')
  } catch (error) {
    toast.error('Failed to delete event')
  }
}

onMounted(fetchEvents)
</script>
