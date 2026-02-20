<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <h1 class="text-2xl font-bold text-gray-900">Find a Mentor</h1>
        <p class="text-gray-600 mt-1">Connect with experienced developers who can guide your journey</p>
      </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Filters -->
      <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
        <div class="flex flex-wrap items-center gap-4">
          <select
            v-model="filters.expertise"
            class="rounded-lg border-gray-300 text-sm focus:ring-primary-500 focus:border-primary-500"
          >
            <option value="">All Expertise</option>
            <option v-for="exp in expertiseAreas" :key="exp" :value="exp">{{ exp }}</option>
          </select>
          <div class="flex-1">
            <input
              type="search"
              v-model="filters.search"
              placeholder="Search mentors..."
              class="w-full rounded-lg border-gray-300 text-sm focus:ring-primary-500 focus:border-primary-500"
            />
          </div>
        </div>
      </div>

      <!-- Mentors Grid -->
      <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="mentor in mentors.data"
          :key="mentor.id"
          class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition-shadow"
        >
          <div class="flex items-center gap-4 mb-4">
            <BaseAvatar
              :src="mentor.avatar"
              :name="mentor.name"
              size="lg"
              :status="mentor.is_online ? 'online' : 'offline'"
            />
            <div>
              <h3 class="font-semibold text-gray-900">{{ mentor.name }}</h3>
              <p class="text-sm text-gray-500">{{ mentor.title }}</p>
            </div>
          </div>

          <p class="text-sm text-gray-600 mb-4 line-clamp-2">{{ mentor.bio }}</p>

          <div class="flex flex-wrap gap-2 mb-4">
            <span
              v-for="skill in mentor.skills?.slice(0, 3)"
              :key="skill"
              class="px-2 py-1 bg-gray-100 text-gray-600 text-xs rounded"
            >
              {{ skill }}
            </span>
          </div>

          <div class="flex items-center justify-between pt-4 border-t border-gray-100">
            <div class="flex items-center gap-1">
              <StarIcon class="h-4 w-4 text-yellow-400" />
              <span class="text-sm font-medium">{{ mentor.rating || 0 }}</span>
              <span class="text-sm text-gray-500">({{ mentor.reviews_count || 0 }})</span>
            </div>
            <BaseButton
              size="sm"
              @click="requestMentorship(mentor)"
            >
              Request
            </BaseButton>
          </div>
        </div>
      </div>

      <EmptyState
        v-if="!mentors.data || mentors.data.length === 0"
        title="No mentors found"
        description="Try adjusting your filters"
      />
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import { useRouter } from 'vue-router'
import { StarIcon } from '@heroicons/vue/20/solid'
import api from '@/api'
import { useAuthStore } from '@/stores/auth'
import { useToastStore } from '@/stores/toast'

const router = useRouter()
const authStore = useAuthStore()
const toast = useToastStore()

const mentors = ref({ data: [], meta: {} })
const expertiseAreas = ref([
  'Laravel',
  'Vue.js',
  'React',
  'Node.js',
  'Python',
  'DevOps',
  'Mobile Development',
  'Data Science',
  'UI/UX Design'
])

const filters = ref({
  expertise: '',
  search: '',
  page: 1
})

const fetchMentors = async () => {
  try {
    const params = new URLSearchParams()
    if (filters.value.expertise) params.append('expertise', filters.value.expertise)
    if (filters.value.search) params.append('search', filters.value.search)
    params.append('page', filters.value.page)

    const response = await api.get(`/mentors?${params.toString()}`)
    mentors.value = response.data
  } catch (error) {
    console.error('Failed to fetch mentors:', error)
  }
}

const requestMentorship = async (mentor) => {
  if (!authStore.isAuthenticated) {
    router.push({ name: 'login' })
    return
  }
  
  try {
    await api.post(`/mentors/${mentor.id}/request`)
    toast.success('Mentorship request sent!')
  } catch (error) {
    toast.error(error.response?.data?.message || 'Failed to send request')
  }
}

watch([() => filters.value.expertise, () => filters.value.search], () => {
  filters.value.page = 1
  fetchMentors()
})

onMounted(() => {
  fetchMentors()
})
</script>
