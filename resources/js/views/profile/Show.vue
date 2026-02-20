<template>
  <div class="min-h-screen bg-gray-50">
    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <LoadingSpinner v-if="loading" class="py-20" />
      
      <template v-else-if="profile">
        <!-- Profile Header -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6">
          <div class="h-32 bg-gradient-to-r from-primary-500 to-primary-600"></div>
          <div class="px-6 pb-6">
            <div class="flex flex-col sm:flex-row sm:items-end gap-4 -mt-12">
              <BaseAvatar
                :src="profile.avatar"
                :name="profile.name"
                size="xl"
                class="ring-4 ring-white"
              />
              <div class="flex-1 pt-4 sm:pt-0">
                <div class="flex items-center gap-2">
                  <h1 class="text-2xl font-bold text-gray-900">{{ profile.name }}</h1>
                  <BaseBadge v-if="profile.verified" variant="primary">Verified</BaseBadge>
                </div>
                <p class="text-gray-600">@{{ profile.username }}</p>
              </div>
              <div class="flex gap-2">
                <BaseButton
                  v-if="!isOwnProfile"
                  @click="follow"
                  :variant="profile.is_following ? 'outline' : 'primary'"
                >
                  {{ profile.is_following ? 'Following' : 'Follow' }}
                </BaseButton>
                <BaseButton
                  v-if="!isOwnProfile"
                  @click="message"
                  variant="secondary"
                >
                  Message
                </BaseButton>
                <BaseButton
                  v-if="isOwnProfile"
                  @click="$router.push('/settings')"
                  variant="secondary"
                >
                  Edit Profile
                </BaseButton>
              </div>
            </div>
          </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-4 gap-4 mb-6">
          <div class="bg-white rounded-xl shadow-sm p-4 text-center">
            <p class="text-2xl font-bold text-gray-900">{{ profile.posts_count || 0 }}</p>
            <p class="text-sm text-gray-500">Posts</p>
          </div>
          <div class="bg-white rounded-xl shadow-sm p-4 text-center">
            <p class="text-2xl font-bold text-gray-900">{{ profile.followers_count || 0 }}</p>
            <p class="text-sm text-gray-500">Followers</p>
          </div>
          <div class="bg-white rounded-xl shadow-sm p-4 text-center">
            <p class="text-2xl font-bold text-gray-900">{{ profile.following_count || 0 }}</p>
            <p class="text-sm text-gray-500">Following</p>
          </div>
          <div class="bg-white rounded-xl shadow-sm p-4 text-center">
            <p class="text-2xl font-bold text-primary-500">{{ profile.reputation || 0 }}</p>
            <p class="text-sm text-gray-500">Reputation</p>
          </div>
        </div>

        <div class="grid lg:grid-cols-3 gap-6">
          <!-- Left Column -->
          <div class="space-y-6">
            <!-- About -->
            <div class="bg-white rounded-xl shadow-sm p-6">
              <h3 class="font-semibold text-gray-900 mb-4">About</h3>
              <p class="text-gray-600">{{ profile.bio || 'No bio yet.' }}</p>
            </div>

            <!-- Info -->
            <div class="bg-white rounded-xl shadow-sm p-6">
              <h3 class="font-semibold text-gray-900 mb-4">Info</h3>
              <div class="space-y-3">
                <div v-if="profile.location" class="flex items-center gap-2 text-gray-600">
                  <MapPinIcon class="h-5 w-5 text-gray-400" />
                  {{ profile.location }}
                </div>
                <div v-if="profile.website" class="flex items-center gap-2 text-gray-600">
                  <LinkIcon class="h-5 w-5 text-gray-400" />
                  <a :href="profile.website" target="_blank" class="text-primary-500 hover:underline">
                    {{ profile.website }}
                  </a>
                </div>
                <div v-if="profile.github" class="flex items-center gap-2 text-gray-600">
                  <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/>
                  </svg>
                  <a :href="`https://github.com/${profile.github}`" target="_blank" class="text-primary-500 hover:underline">
                    {{ profile.github }}
                  </a>
                </div>
                <div class="flex items-center gap-2 text-gray-600">
                  <CalendarIcon class="h-5 w-5 text-gray-400" />
                  Joined {{ $filters.date(profile.created_at) }}
                </div>
              </div>
            </div>

            <!-- Skills -->
            <div v-if="profile.skills?.length" class="bg-white rounded-xl shadow-sm p-6">
              <h3 class="font-semibold text-gray-900 mb-4">Skills</h3>
              <div class="flex flex-wrap gap-2">
                <span
                  v-for="skill in profile.skills"
                  :key="skill"
                  class="px-3 py-1 bg-primary-100 text-primary-700 text-sm rounded-full"
                >
                  {{ skill }}
                </span>
              </div>
            </div>

            <!-- Badges -->
            <div v-if="profile.badges?.length" class="bg-white rounded-xl shadow-sm p-6">
              <h3 class="font-semibold text-gray-900 mb-4">Badges</h3>
              <div class="flex flex-wrap gap-3">
                <div
                  v-for="badge in profile.badges"
                  :key="badge.id"
                  class="flex items-center gap-2 p-2 bg-gray-50 rounded-lg"
                  :title="badge.description"
                >
                  <span class="text-2xl">{{ badge.icon }}</span>
                  <span class="text-sm font-medium">{{ badge.name }}</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Right Column - Posts -->
          <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm p-6">
              <h3 class="font-semibold text-gray-900 mb-4">Recent Posts</h3>
              <div class="space-y-4">
                <div
                  v-for="post in posts.data"
                  :key="post.id"
                  class="p-4 border border-gray-100 rounded-lg hover:bg-gray-50 cursor-pointer"
                  @click="$router.push(`/post/${post.slug}`)"
                >
                  <h4 class="font-medium text-gray-900">{{ post.title }}</h4>
                  <p class="text-sm text-gray-500 mt-1">{{ $filters.relativeTime(post.created_at) }}</p>
                </div>
              </div>
              <p v-if="!posts.data?.length" class="text-gray-500 text-center py-4">No posts yet.</p>
            </div>
          </div>
        </div>
      </template>

      <EmptyState
        v-else
        title="Profile not found"
        description="The user you're looking for doesn't exist."
      />
    </main>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { MapPinIcon, LinkIcon, CalendarIcon } from '@heroicons/vue/20/solid'
import api from '@/api'
import { useAuthStore } from '@/stores/auth'
import { useToastStore } from '@/stores/toast'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()
const toast = useToastStore()

const loading = ref(true)
const profile = ref(null)
const posts = ref({ data: [] })

const isOwnProfile = computed(() => {
  return authStore.user?.username === profile.value?.username
})

const fetchProfile = async () => {
  try {
    loading.value = true
    const response = await api.get(`/users/${route.params.username}`)
    profile.value = response.data.data || response.data
    posts.value = profile.value.posts || { data: [] }
  } catch (error) {
    console.error('Failed to fetch profile:', error)
  } finally {
    loading.value = false
  }
}

const follow = async () => {
  if (!authStore.isAuthenticated) {
    router.push({ name: 'login' })
    return
  }
  
  try {
    await api.post(`/users/${profile.value.id}/follow`)
    profile.value.is_following = !profile.value.is_following
    if (profile.value.is_following) {
      profile.value.followers_count++
      toast.success('Following!')
    } else {
      profile.value.followers_count--
      toast.success('Unfollowed')
    }
  } catch (error) {
    toast.error('Failed to follow')
  }
}

const message = () => {
  if (!authStore.isAuthenticated) {
    router.push({ name: 'login' })
    return
  }
  router.push({ name: 'chat', query: { user: profile.value.id } })
}

watch(() => route.params.username, () => {
  fetchProfile()
})

onMounted(() => {
  fetchProfile()
})
</script>
