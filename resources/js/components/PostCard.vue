<template>
  <article class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm hover:shadow-md transition">
    <div class="flex items-start gap-4">
      <!-- Vote buttons -->
      <div class="flex flex-col items-center gap-1">
        <button
          @click.prevent="vote('up')"
          class="text-gray-400 hover:text-primary-600 transition"
          :class="{ 'text-primary-600': userVote === 1 }"
        >
          <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M3.293 9.707a1 1 0 010-1.414l6-6a1 1 0 011.414 0l6 6a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L4.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd" />
          </svg>
        </button>
        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ post.upvotes_count - post.downvotes_count }}</span>
        <button
          @click.prevent="vote('down')"
          class="text-gray-400 hover:text-red-600 transition"
          :class="{ 'text-red-600': userVote === -1 }"
        >
          <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M16.707 10.293a1 1 0 010 1.414l-6 6a1 1 0 01-1.414 0l-6-6a1 1 0 111.414-1.414L9 14.586V3a1 1 0 012 0v11.586l4.293-4.293a1 1 0 011.414 0z" clip-rule="evenodd" />
          </svg>
        </button>
      </div>

      <!-- Content -->
      <div class="flex-1 min-w-0">
        <div class="flex items-center gap-2 mb-2">
          <span
            v-if="post.type === 'question'"
            class="px-2 py-0.5 text-xs font-medium rounded bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200"
          >
            Question
          </span>
          <span
            v-else-if="post.type === 'discussion'"
            class="px-2 py-0.5 text-xs font-medium rounded bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200"
          >
            Discussion
          </span>
          <span
            v-else-if="post.type === 'tutorial'"
            class="px-2 py-0.5 text-xs font-medium rounded bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200"
          >
            Tutorial
          </span>
          <span
            v-if="post.is_solved"
            class="px-2 py-0.5 text-xs font-medium rounded bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200"
          >
            ✓ Solved
          </span>
        </div>

        <router-link :to="`/post/${post.slug}`" class="block group">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white group-hover:text-primary-600 dark:group-hover:text-primary-400 transition">
            {{ post.title }}
          </h3>
        </router-link>

        <p class="mt-2 text-gray-600 dark:text-gray-400 text-sm line-clamp-2">
          {{ post.excerpt }}
        </p>

        <!-- Tags -->
        <div class="flex flex-wrap gap-2 mt-3">
          <router-link
            v-for="tag in post.tags"
            :key="tag.id"
            :to="`/tag/${tag.slug}`"
            class="px-2 py-1 text-xs bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded hover:bg-gray-200 dark:hover:bg-gray-600 transition"
          >
            {{ tag.name }}
          </router-link>
        </div>

        <!-- Meta -->
        <div class="flex items-center justify-between mt-4 text-sm text-gray-500 dark:text-gray-400">
          <div class="flex items-center gap-4">
            <router-link :to="`/profile/${post.user.username}`" class="flex items-center gap-2 hover:text-primary-600 dark:hover:text-primary-400">
              <img :src="post.user.avatar" :alt="post.user.name" class="w-5 h-5 rounded-full" />
              <span>{{ post.user.name }}</span>
            </router-link>
            <span>•</span>
            <span>{{ formatDate(post.created_at) }}</span>
          </div>
          <div class="flex items-center gap-4">
            <span class="flex items-center gap-1">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
              </svg>
              {{ post.comments_count }}
            </span>
            <span class="flex items-center gap-1">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
              </svg>
              {{ post.views_count }}
            </span>
          </div>
        </div>
      </div>
    </div>
  </article>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import axios from 'axios'

const props = defineProps({
  post: {
    type: Object,
    required: true,
  },
})

const authStore = useAuthStore()
const userVote = ref(null)

onMounted(async () => {
  if (authStore.isAuthenticated) {
    try {
      const response = await axios.get(`/api/v1/posts/${props.post.id}/vote`)
      userVote.value = response.data.data.vote
    } catch (error) {
      // Ignore error
    }
  }
})

const vote = async (type) => {
  if (!authStore.isAuthenticated) {
    // Redirect to login
    window.location.href = '/login'
    return
  }

  try {
    const response = await axios.post(`/api/v1/posts/${props.post.id}/vote`, { vote: type })
    userVote.value = response.data.data.voted ? (type === 'up' ? 1 : -1) : null
    props.post.upvotes_count = response.data.data.upvotes_count
    props.post.downvotes_count = response.data.data.downvotes_count
  } catch (error) {
    console.error('Failed to vote:', error)
  }
}

const formatDate = (date) => {
  const d = new Date(date)
  const now = new Date()
  const diff = now - d
  const seconds = Math.floor(diff / 1000)
  const minutes = Math.floor(seconds / 60)
  const hours = Math.floor(minutes / 60)
  const days = Math.floor(hours / 24)

  if (days > 7) {
    return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })
  } else if (days > 0) {
    return `${days}d ago`
  } else if (hours > 0) {
    return `${hours}h ago`
  } else if (minutes > 0) {
    return `${minutes}m ago`
  } else {
    return 'Just now'
  }
}
</script>