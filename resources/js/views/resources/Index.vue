<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
          <div>
            <h1 class="text-2xl font-bold text-gray-900">Resources</h1>
            <p class="text-gray-600 mt-1">Tutorials, code snippets, and learning materials</p>
          </div>
          <BaseButton @click="showUploadModal = true">
            <PlusIcon class="h-5 w-5 mr-2" />
            Share Resource
          </BaseButton>
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
                <option value="tutorial">Tutorials</option>
                <option value="snippet">Code Snippets</option>
                <option value="pdf">PDFs</option>
                <option value="video">Videos</option>
                <option value="repository">Repositories</option>
              </select>
              <div class="flex-1">
                <input
                  type="search"
                  v-model="filters.search"
                  placeholder="Search resources..."
                  class="w-full rounded-lg border-gray-300 text-sm focus:ring-primary-500 focus:border-primary-500"
                />
              </div>
            </div>
          </div>

          <!-- Resources List -->
          <div class="space-y-4">
            <div
              v-for="resource in resources.data"
              :key="resource.id"
              class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition-shadow cursor-pointer"
              @click="$router.push(`/resources/${resource.slug}`)"
            >
              <div class="flex items-start gap-4">
                <div class="h-12 w-12 rounded-lg flex items-center justify-center" :class="getTypeColor(resource.type)">
                  <component :is="getTypeIcon(resource.type)" class="h-6 w-6" />
                </div>
                <div class="flex-1">
                  <div class="flex items-start justify-between">
                    <div>
                      <h3 class="font-semibold text-gray-900">{{ resource.title }}</h3>
                      <p class="text-sm text-gray-600 mt-1">{{ resource.description }}</p>
                    </div>
                    <BaseBadge :variant="getTypeVariant(resource.type)">{{ resource.type }}</BaseBadge>
                  </div>
                  <div class="flex items-center gap-4 mt-4 text-sm text-gray-500">
                    <span class="flex items-center gap-1">
                      <ArrowDownTrayIcon class="h-4 w-4" />
                      {{ resource.downloads_count || 0 }}
                    </span>
                    <span class="flex items-center gap-1">
                      <StarIcon class="h-4 w-4" />
                      {{ resource.rating || 0 }}
                    </span>
                    <span>{{ $filters.relativeTime(resource.created_at) }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <EmptyState
            v-if="!resources.data || resources.data.length === 0"
            title="No resources found"
            description="Be the first to share a resource!"
          />
        </div>

        <!-- Sidebar -->
        <aside class="lg:w-80">
          <!-- Categories -->
          <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="font-semibold text-gray-900 mb-4">Categories</h3>
            <div class="space-y-2">
              <button
                v-for="cat in categories"
                :key="cat.slug"
                @click="filters.type = cat.slug"
                class="flex items-center justify-between w-full p-2 rounded-lg hover:bg-gray-50 transition-colors text-left"
                :class="{ 'bg-primary-50 text-primary-600': filters.type === cat.slug }"
              >
                <span class="text-sm">{{ cat.name }}</span>
                <span class="text-xs text-gray-500">{{ cat.count || 0 }}</span>
              </button>
            </div>
          </div>
        </aside>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import {
  PlusIcon,
  DocumentTextIcon,
  CodeBracketIcon,
  PlayCircleIcon,
  FolderIcon,
  StarIcon,
  ArrowDownTrayIcon
} from '@heroicons/vue/20/solid'
import api from '@/api'

const resources = ref({ data: [], meta: {} })
const categories = ref([
  { slug: 'tutorial', name: 'Tutorials', count: 0 },
  { slug: 'snippet', name: 'Code Snippets', count: 0 },
  { slug: 'pdf', name: 'PDFs', count: 0 },
  { slug: 'video', name: 'Videos', count: 0 },
  { slug: 'repository', name: 'Repositories', count: 0 }
])

const filters = ref({
  type: '',
  search: '',
  page: 1
})

const showUploadModal = ref(false)

const fetchResources = async () => {
  try {
    const params = new URLSearchParams()
    if (filters.value.type) params.append('type', filters.value.type)
    if (filters.value.search) params.append('search', filters.value.search)
    params.append('page', filters.value.page)

    const response = await api.get(`/resources?${params.toString()}`)
    resources.value = response.data
  } catch (error) {
    console.error('Failed to fetch resources:', error)
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

watch([() => filters.value.type, () => filters.value.search], () => {
  filters.value.page = 1
  fetchResources()
})

onMounted(() => {
  fetchResources()
})
</script>
