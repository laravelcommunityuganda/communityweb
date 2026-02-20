<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
          <div>
            <h1 class="text-2xl font-bold text-gray-900">Jobs Board</h1>
            <p class="text-gray-600 mt-1">Find your next opportunity in Uganda's tech industry</p>
          </div>
          <router-link
            to="/jobs/create"
            class="inline-flex items-center justify-center px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white font-medium rounded-lg transition-colors"
          >
            <PlusIcon class="h-5 w-5 mr-2" />
            Post a Job
          </router-link>
        </div>
      </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="flex flex-col lg:flex-row gap-8">
        <!-- Main Content -->
        <div class="flex-1">
          <!-- Filters -->
          <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
            <div class="flex flex-wrap items-center gap-4">
              <select
                v-model="filters.type"
                class="rounded-lg border-gray-300 text-sm focus:ring-primary-500 focus:border-primary-500"
              >
                <option value="">All Types</option>
                <option value="full-time">Full-time</option>
                <option value="part-time">Part-time</option>
                <option value="contract">Contract</option>
                <option value="freelance">Freelance</option>
                <option value="internship">Internship</option>
              </select>
              <select
                v-model="filters.location"
                class="rounded-lg border-gray-300 text-sm focus:ring-primary-500 focus:border-primary-500"
              >
                <option value="">All Locations</option>
                <option v-for="loc in locations" :key="loc" :value="loc">{{ loc }}</option>
              </select>
              <select
                v-model="filters.remote"
                class="rounded-lg border-gray-300 text-sm focus:ring-primary-500 focus:border-primary-500"
              >
                <option value="">Remote/Onsite</option>
                <option value="remote">Remote</option>
                <option value="onsite">Onsite</option>
                <option value="hybrid">Hybrid</option>
              </select>
              <div class="flex-1">
                <input
                  type="search"
                  v-model="filters.search"
                  placeholder="Search jobs..."
                  class="w-full rounded-lg border-gray-300 text-sm focus:ring-primary-500 focus:border-primary-500"
                />
              </div>
            </div>
          </div>

          <!-- Jobs List -->
          <div class="space-y-4">
            <div
              v-for="job in jobs.data"
              :key="job.id"
              class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition-shadow cursor-pointer"
              @click="$router.push(`/jobs/${job.slug}`)"
            >
              <div class="flex items-start justify-between">
                <div class="flex items-start gap-4">
                  <div class="h-12 w-12 rounded-lg bg-gray-100 flex items-center justify-center">
                    <BuildingOfficeIcon class="h-6 w-6 text-gray-400" />
                  </div>
                  <div>
                    <h3 class="font-semibold text-gray-900">{{ job.title }}</h3>
                    <p class="text-sm text-gray-600">{{ job.company }}</p>
                    <div class="flex flex-wrap items-center gap-2 mt-2">
                      <BaseBadge :variant="getJobTypeVariant(job.type)">{{ job.type }}</BaseBadge>
                      <span class="text-sm text-gray-500">{{ job.location }}</span>
                      <span v-if="job.remote" class="text-sm text-primary-500">Remote</span>
                    </div>
                  </div>
                </div>
                <div class="text-right">
                  <p class="font-semibold text-gray-900">{{ formatSalary(job.salary_min, job.salary_max) }}</p>
                  <p class="text-sm text-gray-500">{{ $filters.relativeTime(job.created_at) }}</p>
                </div>
              </div>
              <p class="mt-4 text-sm text-gray-600 line-clamp-2">{{ job.description }}</p>
              <div class="flex flex-wrap gap-2 mt-4">
                <span
                  v-for="skill in job.skills?.slice(0, 4)"
                  :key="skill"
                  class="px-2 py-1 bg-gray-100 text-gray-600 text-xs rounded"
                >
                  {{ skill }}
                </span>
              </div>
            </div>
          </div>

          <EmptyState
            v-if="!jobs.data || jobs.data.length === 0"
            title="No jobs found"
            description="Try adjusting your filters or check back later"
          >
            <template #action>
              <router-link
                to="/jobs/create"
                class="inline-flex items-center px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white font-medium rounded-lg transition-colors"
              >
                Post a Job
              </router-link>
            </template>
          </EmptyState>
        </div>

        <!-- Sidebar -->
        <aside class="lg:w-80">
          <!-- Stats -->
          <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
            <h3 class="font-semibold text-gray-900 mb-4">Job Market Stats</h3>
            <div class="space-y-4">
              <div class="flex justify-between">
                <span class="text-gray-600">Total Jobs</span>
                <span class="font-semibold text-gray-900">{{ stats.total || 0 }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">Remote Jobs</span>
                <span class="font-semibold text-gray-900">{{ stats.remote || 0 }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">This Week</span>
                <span class="font-semibold text-gray-900">{{ stats.thisWeek || 0 }}</span>
              </div>
            </div>
          </div>

          <!-- Featured Companies -->
          <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="font-semibold text-gray-900 mb-4">Top Hiring Companies</h3>
            <div class="space-y-4">
              <div
                v-for="company in topCompanies"
                :key="company.name"
                class="flex items-center gap-3"
              >
                <div class="h-10 w-10 rounded-lg bg-gray-100 flex items-center justify-center">
                  <BuildingOfficeIcon class="h-5 w-5 text-gray-400" />
                </div>
                <div>
                  <p class="font-medium text-gray-900">{{ company.name }}</p>
                  <p class="text-sm text-gray-500">{{ company.jobs_count }} jobs</p>
                </div>
              </div>
            </div>
          </div>
        </aside>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import { PlusIcon, BuildingOfficeIcon } from '@heroicons/vue/20/solid'
import api from '@/api'

const jobs = ref({ data: [], meta: {} })
const stats = ref({})
const topCompanies = ref([])
const locations = ref(['Kampala', 'Entebbe', 'Jinja', 'Mbarara', 'Gulu', 'Remote'])

const filters = ref({
  type: '',
  location: '',
  remote: '',
  search: '',
  page: 1
})

const fetchJobs = async () => {
  try {
    const params = new URLSearchParams()
    if (filters.value.type) params.append('type', filters.value.type)
    if (filters.value.location) params.append('location', filters.value.location)
    if (filters.value.remote) params.append('remote', filters.value.remote)
    if (filters.value.search) params.append('search', filters.value.search)
    params.append('page', filters.value.page)

    const response = await api.get(`/jobs?${params.toString()}`)
    jobs.value = response.data
  } catch (error) {
    console.error('Failed to fetch jobs:', error)
  }
}

const fetchStats = async () => {
  try {
    const response = await api.get('/jobs/stats')
    stats.value = response.data
  } catch (error) {
    console.error('Failed to fetch stats:', error)
  }
}

const fetchTopCompanies = async () => {
  try {
    const response = await api.get('/jobs/top-companies')
    topCompanies.value = response.data
  } catch (error) {
    console.error('Failed to fetch companies:', error)
  }
}

const getJobTypeVariant = (type) => {
  const variants = {
    'full-time': 'success',
    'part-time': 'info',
    'contract': 'warning',
    'freelance': 'primary',
    'internship': 'default'
  }
  return variants[type] || 'default'
}

const formatSalary = (min, max) => {
  if (!min && !max) return 'Negotiable'
  const format = (val) => {
    if (val >= 1000000) return `${(val / 1000000).toFixed(1)}M`
    if (val >= 1000) return `${(val / 1000).toFixed(0)}K`
    return val
  }
  if (min && max) return `UGX ${format(min)} - ${format(max)}`
  if (min) return `UGX ${format(min)}+`
  return `Up to UGX ${format(max)}`
}

watch([() => filters.value.type, () => filters.value.location, () => filters.value.remote, () => filters.value.search], () => {
  filters.value.page = 1
  fetchJobs()
})

onMounted(() => {
  fetchJobs()
  fetchStats()
  fetchTopCompanies()
})
</script>
