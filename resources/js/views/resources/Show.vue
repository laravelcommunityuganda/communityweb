<template>
  <div class="min-h-screen bg-gray-50">
    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <LoadingSpinner v-if="loading" class="py-20" />
      
      <template v-else-if="resource">
        <!-- Back Button -->
        <router-link to="/resources" class="inline-flex items-center text-gray-600 hover:text-gray-900 mb-6">
          <ArrowLeftIcon class="h-5 w-5 mr-2" />
          Back to Resources
        </router-link>

        <!-- Resource Details -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
          <div class="p-6">
            <!-- Header -->
            <div class="flex items-start gap-4 mb-6">
              <div class="h-16 w-16 rounded-xl flex items-center justify-center" :class="getTypeColor(resource.type)">
                <component :is="getTypeIcon(resource.type)" class="h-8 w-8" />
              </div>
              <div class="flex-1">
                <div class="flex items-center gap-2">
                  <BaseBadge :variant="getTypeVariant(resource.type)">{{ resource.type }}</BaseBadge>
                </div>
                <h1 class="text-2xl font-bold text-gray-900 mt-2">{{ resource.title }}</h1>
                <p class="text-gray-600 mt-1">{{ resource.description }}</p>
              </div>
            </div>

            <!-- Stats -->
            <div class="flex items-center gap-6 mb-6 p-4 bg-gray-50 rounded-lg">
              <div class="flex items-center gap-2">
                <ArrowDownTrayIcon class="h-5 w-5 text-gray-400" />
                <span class="font-medium">{{ resource.downloads_count || 0 }}</span>
                <span class="text-gray-500">downloads</span>
              </div>
              <div class="flex items-center gap-2">
                <StarIcon class="h-5 w-5 text-yellow-400" />
                <span class="font-medium">{{ resource.rating || 0 }}</span>
                <span class="text-gray-500">rating</span>
              </div>
              <div class="flex items-center gap-2">
                <EyeIcon class="h-5 w-5 text-gray-400" />
                <span class="font-medium">{{ resource.views_count || 0 }}</span>
                <span class="text-gray-500">views</span>
              </div>
            </div>

            <!-- Content -->
            <div class="prose prose-primary max-w-none mb-6">
              <div v-html="resource.content"></div>
            </div>

            <!-- Actions -->
            <div class="flex items-center gap-4 pt-6 border-t border-gray-100">
              <BaseButton
                v-if="resource.file_url"
                @click="download"
                :loading="downloading"
              >
                <ArrowDownTrayIcon class="h-5 w-5 mr-2" />
                Download
              </BaseButton>
              <a
                v-if="resource.external_url"
                :href="resource.external_url"
                target="_blank"
                class="inline-flex items-center px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white font-medium rounded-lg transition-colors"
              >
                <LinkIcon class="h-5 w-5 mr-2" />
                Open Link
              </a>
              <BaseButton
                @click="bookmark"
                :variant="resource.is_bookmarked ? 'primary' : 'outline'"
              >
                <BookmarkIcon class="h-5 w-5 mr-2" />
                {{ resource.is_bookmarked ? 'Saved' : 'Save' }}
              </BaseButton>
            </div>
          </div>
        </div>

        <!-- Author -->
        <div class="bg-white rounded-xl shadow-sm p-6 mt-6">
          <h3 class="font-semibold text-gray-900 mb-4">Shared by</h3>
          <div class="flex items-center gap-4">
            <BaseAvatar
              :src="resource.user?.avatar"
              :name="resource.user?.name"
              size="lg"
            />
            <div>
              <router-link
                :to="`/profile/${resource.user?.username}`"
                class="font-medium text-gray-900 hover:text-primary-500"
              >
                {{ resource.user?.name }}
              </router-link>
              <p class="text-sm text-gray-500">{{ $filters.relativeTime(resource.created_at) }}</p>
            </div>
          </div>
        </div>
      </template>

      <EmptyState
        v-else
        title="Resource not found"
        description="The resource you're looking for doesn't exist or has been removed."
      >
        <template #action>
          <router-link to="/resources" class="text-primary-500 hover:text-primary-600">
            Browse Resources
          </router-link>
        </template>
      </EmptyState>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import {
  ArrowLeftIcon,
  ArrowDownTrayIcon,
  StarIcon,
  EyeIcon,
  LinkIcon,
  BookmarkIcon,
  DocumentTextIcon,
  CodeBracketIcon,
  PlayCircleIcon,
  FolderIcon
} from '@heroicons/vue/20/solid'
import api from '@/api'
import { useAuthStore } from '@/stores/auth'
import { useToastStore } from '@/stores/toast'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()
const toast = useToastStore()

const loading = ref(true)
const resource = ref(null)
const downloading = ref(false)

const fetchResource = async () => {
  try {
    loading.value = true
    const response = await api.get(`/resources/${route.params.slug}`)
    resource.value = response.data.data || response.data
  } catch (error) {
    console.error('Failed to fetch resource:', error)
  } finally {
    loading.value = false
  }
}

const download = async () => {
  try {
    downloading.value = true
    window.open(resource.value.file_url, '_blank')
    await api.post(`/resources/${resource.value.id}/download`)
    resource.value.downloads_count = (resource.value.downloads_count || 0) + 1
  } catch (error) {
    toast.error('Failed to download')
  } finally {
    downloading.value = false
  }
}

const bookmark = async () => {
  if (!authStore.isAuthenticated) {
    router.push({ name: 'login', query: { redirect: route.fullPath } })
    return
  }
  
  try {
    await api.post(`/resources/${resource.value.id}/bookmark`)
    resource.value.is_bookmarked = !resource.value.is_bookmarked
    toast.success(resource.value.is_bookmarked ? 'Resource saved!' : 'Resource removed from saved')
  } catch (error) {
    toast.error('Failed to save resource')
  }
}

const getTypeIcon = (type) => {
  const icons = {
    'tutorial': DocumentTextIcon,
    'snippet': CodeBracketIcon,
    'pdf': DocumentTextIcon,
    'video': PlayCircleIcon,
    'repository': FolderIcon
  }
  return icons[type] || DocumentTextIcon
}

const getTypeColor = (type) => {
  const colors = {
    'tutorial': 'bg-blue-100 text-blue-600',
    'snippet': 'bg-green-100 text-green-600',
    'pdf': 'bg-red-100 text-red-600',
    'video': 'bg-purple-100 text-purple-600',
    'repository': 'bg-yellow-100 text-yellow-600'
  }
  return colors[type] || 'bg-gray-100 text-gray-600'
}

const getTypeVariant = (type) => {
  const variants = {
    'tutorial': 'info',
    'snippet': 'success',
    'pdf': 'danger',
    'video': 'primary',
    'repository': 'warning'
  }
  return variants[type] || 'default'
}

onMounted(() => {
  fetchResource()
})
</script>
