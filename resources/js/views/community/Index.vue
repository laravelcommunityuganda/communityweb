<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
          <div>
            <h1 class="text-2xl font-bold text-gray-900">Community Forum</h1>
            <p class="text-gray-600 mt-1">Ask questions, share knowledge, and connect with developers</p>
          </div>
          <router-link
            to="/post/create"
            class="inline-flex items-center justify-center px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white font-medium rounded-lg transition-colors"
          >
            <PlusIcon class="h-5 w-5 mr-2" />
            New Post
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
                v-model="filters.category"
                class="rounded-lg border-gray-300 text-sm focus:ring-primary-500 focus:border-primary-500"
              >
                <option value="">All Categories</option>
                <option v-for="cat in categories" :key="cat.id" :value="cat.slug">
                  {{ cat.name }}
                </option>
              </select>
              <select
                v-model="filters.sort"
                class="rounded-lg border-gray-300 text-sm focus:ring-primary-500 focus:border-primary-500"
              >
                <option value="latest">Latest</option>
                <option value="popular">Popular</option>
                <option value="unanswered">Unanswered</option>
              </select>
              <div class="flex-1">
                <input
                  type="search"
                  v-model="filters.search"
                  placeholder="Search posts..."
                  class="w-full rounded-lg border-gray-300 text-sm focus:ring-primary-500 focus:border-primary-500"
                />
              </div>
            </div>
          </div>

          <!-- Posts List -->
          <div class="space-y-4">
            <PostCard
              v-for="post in posts.data"
              :key="post.id"
              :post="post"
            />
          </div>

          <!-- Pagination -->
          <div v-if="posts.meta && posts.meta.last_page > 1" class="mt-6">
            <nav class="flex justify-center">
              <button
                @click="changePage(posts.meta.current_page - 1)"
                :disabled="posts.meta.current_page === 1"
                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-l-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                Previous
              </button>
              <button
                v-for="page in visiblePages"
                :key="page"
                @click="changePage(page)"
                :class="[
                  'px-4 py-2 text-sm font-medium border-t border-b border-gray-300',
                  page === posts.meta.current_page
                    ? 'bg-primary-500 text-white'
                    : 'bg-white text-gray-700 hover:bg-gray-50'
                ]"
              >
                {{ page }}
              </button>
              <button
                @click="changePage(posts.meta.current_page + 1)"
                :disabled="posts.meta.current_page === posts.meta.last_page"
                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-r-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                Next
              </button>
            </nav>
          </div>

          <EmptyState
            v-if="!posts.data || posts.data.length === 0"
            title="No posts found"
            description="Be the first to start a discussion!"
          >
            <template #action>
              <router-link
                to="/post/create"
                class="inline-flex items-center px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white font-medium rounded-lg transition-colors"
              >
                Create Post
              </router-link>
            </template>
          </EmptyState>
        </div>

        <!-- Sidebar -->
        <aside class="lg:w-80">
          <!-- Categories -->
          <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
            <h3 class="font-semibold text-gray-900 mb-4">Categories</h3>
            <div class="space-y-2">
              <router-link
                v-for="cat in categories"
                :key="cat.id"
                :to="`/community/${cat.slug}`"
                class="flex items-center justify-between p-2 rounded-lg hover:bg-gray-50 transition-colors"
                :class="{ 'bg-primary-50 text-primary-600': filters.category === cat.slug }"
              >
                <span class="text-sm">{{ cat.name }}</span>
                <span class="text-xs text-gray-500">{{ cat.posts_count || 0 }}</span>
              </router-link>
            </div>
          </div>

          <!-- Popular Tags -->
          <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
            <h3 class="font-semibold text-gray-900 mb-4">Popular Tags</h3>
            <div class="flex flex-wrap gap-2">
              <router-link
                v-for="tag in popularTags"
                :key="tag.id"
                :to="`/community?tag=${tag.slug}`"
                class="px-3 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm rounded-full transition-colors"
              >
                #{{ tag.name }}
              </router-link>
            </div>
          </div>

          <!-- Top Contributors -->
          <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="font-semibold text-gray-900 mb-4">Top Contributors</h3>
            <div class="space-y-4">
              <div
                v-for="(user, index) in topContributors"
                :key="user.id"
                class="flex items-center gap-3"
              >
                <span class="text-sm font-medium text-gray-500 w-5">{{ index + 1 }}</span>
                <BaseAvatar
                  :src="user.avatar"
                  :name="user.name"
                  size="sm"
                />
                <div class="flex-1 min-w-0">
                  <p class="text-sm font-medium text-gray-900 truncate">{{ user.name }}</p>
                  <p class="text-xs text-gray-500">{{ user.reputation }} reputation</p>
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
import { ref, computed, onMounted, watch } from 'vue'
import { useRoute } from 'vue-router'
import { PlusIcon } from '@heroicons/vue/20/solid'
import PostCard from '@/components/PostCard.vue'
import api from '@/api'

const route = useRoute()

const posts = ref({ data: [], meta: {} })
const categories = ref([])
const popularTags = ref([])
const topContributors = ref([])

const filters = ref({
  category: '',
  sort: 'latest',
  search: '',
  page: 1
})

const visiblePages = computed(() => {
  if (!posts.value.meta) return []
  const { current_page, last_page } = posts.value.meta
  const pages = []
  const start = Math.max(1, current_page - 2)
  const end = Math.min(last_page, current_page + 2)
  for (let i = start; i <= end; i++) {
    pages.push(i)
  }
  return pages
})

const fetchPosts = async () => {
  try {
    const params = new URLSearchParams()
    if (filters.value.category) params.append('category', filters.value.category)
    if (filters.value.sort) params.append('sort', filters.value.sort)
    if (filters.value.search) params.append('search', filters.value.search)
    params.append('page', filters.value.page)

    const response = await api.get(`/posts?${params.toString()}`)
    posts.value = response.data
  } catch (error) {
    console.error('Failed to fetch posts:', error)
  }
}

const fetchCategories = async () => {
  try {
    const response = await api.get('/categories')
    categories.value = response.data.data || response.data
  } catch (error) {
    console.error('Failed to fetch categories:', error)
  }
}

const fetchPopularTags = async () => {
  try {
    const response = await api.get('/tags/popular')
    popularTags.value = response.data.data || response.data
  } catch (error) {
    console.error('Failed to fetch tags:', error)
  }
}

const fetchTopContributors = async () => {
  try {
    const response = await api.get('/users/top-contributors')
    topContributors.value = response.data.data || response.data
  } catch (error) {
    console.error('Failed to fetch contributors:', error)
  }
}

const changePage = (page) => {
  filters.value.page = page
  fetchPosts()
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

watch(() => route.params.category, (newCategory) => {
  filters.value.category = newCategory || ''
  fetchPosts()
})

watch([() => filters.value.category, () => filters.value.sort, () => filters.value.search], () => {
  filters.value.page = 1
  fetchPosts()
})

onMounted(() => {
  filters.value.category = route.params.category || ''
  fetchPosts()
  fetchCategories()
  fetchPopularTags()
  fetchTopContributors()
})
</script>
