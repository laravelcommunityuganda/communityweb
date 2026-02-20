<template>
  <div class="min-h-screen bg-gray-50">
    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <LoadingSpinner v-if="loading" class="py-20" />
      
      <template v-else-if="job">
        <!-- Back Button -->
        <router-link to="/jobs" class="inline-flex items-center text-gray-600 hover:text-gray-900 mb-6">
          <ArrowLeftIcon class="h-5 w-5 mr-2" />
          Back to Jobs
        </router-link>

        <!-- Job Details -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
          <div class="p-6">
            <!-- Header -->
            <div class="flex items-start gap-4 mb-6">
              <div class="h-16 w-16 rounded-xl bg-gray-100 flex items-center justify-center">
                <BuildingOfficeIcon class="h-8 w-8 text-gray-400" />
              </div>
              <div class="flex-1">
                <h1 class="text-2xl font-bold text-gray-900">{{ job.title }}</h1>
                <p class="text-lg text-gray-600">{{ job.company }}</p>
                <div class="flex flex-wrap items-center gap-3 mt-2">
                  <BaseBadge :variant="getJobTypeVariant(job.type)">{{ job.type }}</BaseBadge>
                  <span class="text-gray-500">{{ job.location }}</span>
                  <span v-if="job.remote" class="text-primary-500">Remote</span>
                </div>
              </div>
            </div>

            <!-- Quick Info -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6 p-4 bg-gray-50 rounded-lg">
              <div>
                <p class="text-sm text-gray-500">Salary</p>
                <p class="font-semibold text-gray-900">{{ formatSalary(job.salary_min, job.salary_max) }}</p>
              </div>
              <div>
                <p class="text-sm text-gray-500">Experience</p>
                <p class="font-semibold text-gray-900">{{ job.experience_level || 'Not specified' }}</p>
              </div>
              <div>
                <p class="text-sm text-gray-500">Deadline</p>
                <p class="font-semibold text-gray-900">{{ job.deadline ? $filters.date(job.deadline) : 'Open' }}</p>
              </div>
              <div>
                <p class="text-sm text-gray-500">Posted</p>
                <p class="font-semibold text-gray-900">{{ $filters.relativeTime(job.created_at) }}</p>
              </div>
            </div>

            <!-- Description -->
            <div class="prose prose-primary max-w-none mb-6">
              <h3>Description</h3>
              <div v-html="job.description"></div>
            </div>

            <!-- Requirements -->
            <div v-if="job.requirements" class="mb-6">
              <h3 class="text-lg font-semibold text-gray-900 mb-3">Requirements</h3>
              <div class="prose prose-primary max-w-none" v-html="job.requirements"></div>
            </div>

            <!-- Skills -->
            <div v-if="job.skills?.length" class="mb-6">
              <h3 class="text-lg font-semibold text-gray-900 mb-3">Required Skills</h3>
              <div class="flex flex-wrap gap-2">
                <span
                  v-for="skill in job.skills"
                  :key="skill"
                  class="px-3 py-1 bg-primary-100 text-primary-700 text-sm rounded-full"
                >
                  {{ skill }}
                </span>
              </div>
            </div>

            <!-- Apply Button -->
            <div class="flex items-center gap-4 pt-6 border-t border-gray-100">
              <BaseButton
                @click="apply"
                :loading="applying"
                size="lg"
              >
                Apply Now
              </BaseButton>
              <BaseButton
                @click="saveJob"
                :variant="job.is_saved ? 'primary' : 'outline'"
              >
                <BookmarkIcon class="h-5 w-5 mr-2" />
                {{ job.is_saved ? 'Saved' : 'Save Job' }}
              </BaseButton>
            </div>
          </div>
        </div>

        <!-- Company Info -->
        <div class="bg-white rounded-xl shadow-sm p-6 mt-6">
          <h3 class="font-semibold text-gray-900 mb-4">About {{ job.company }}</h3>
          <p class="text-gray-600">{{ job.company_description || 'No company description available.' }}</p>
        </div>
      </template>

      <EmptyState
        v-else
        title="Job not found"
        description="The job you're looking for doesn't exist or has been removed."
      >
        <template #action>
          <router-link to="/jobs" class="text-primary-500 hover:text-primary-600">
            Browse Jobs
          </router-link>
        </template>
      </EmptyState>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { ArrowLeftIcon, BuildingOfficeIcon, BookmarkIcon } from '@heroicons/vue/20/solid'
import api from '@/api'
import { useAuthStore } from '@/stores/auth'
import { useToastStore } from '@/stores/toast'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()
const toast = useToastStore()

const loading = ref(true)
const job = ref(null)
const applying = ref(false)

const fetchJob = async () => {
  try {
    loading.value = true
    const response = await api.get(`/jobs/${route.params.slug}`)
    job.value = response.data.data || response.data
  } catch (error) {
    console.error('Failed to fetch job:', error)
  } finally {
    loading.value = false
  }
}

const apply = async () => {
  if (!authStore.isAuthenticated) {
    router.push({ name: 'login', query: { redirect: route.fullPath } })
    return
  }
  
  try {
    applying.value = true
    await api.post(`/jobs/${job.value.id}/apply`)
    toast.success('Application submitted successfully!')
  } catch (error) {
    toast.error(error.response?.data?.message || 'Failed to apply')
  } finally {
    applying.value = false
  }
}

const saveJob = async () => {
  if (!authStore.isAuthenticated) {
    router.push({ name: 'login', query: { redirect: route.fullPath } })
    return
  }
  
  try {
    await api.post(`/jobs/${job.value.id}/save`)
    job.value.is_saved = !job.value.is_saved
    toast.success(job.value.is_saved ? 'Job saved!' : 'Job removed from saved')
  } catch (error) {
    toast.error('Failed to save job')
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

onMounted(() => {
  fetchJob()
})
</script>
