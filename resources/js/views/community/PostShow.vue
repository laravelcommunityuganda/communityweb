<template>
  <div class="min-h-screen bg-gray-50">
    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <LoadingSpinner v-if="loading" class="py-20" />
      
      <template v-else-if="post">
        <!-- Breadcrumb -->
        <nav class="mb-6">
          <ol class="flex items-center space-x-2 text-sm text-gray-500">
            <li>
              <router-link to="/community" class="hover:text-primary-500">Community</router-link>
            </li>
            <li>/</li>
            <li>
              <router-link :to="`/community/${post.category?.slug}`" class="hover:text-primary-500">
                {{ post.category?.name }}
              </router-link>
            </li>
            <li>/</li>
            <li class="text-gray-900">{{ post.title }}</li>
          </ol>
        </nav>

        <!-- Post -->
        <article class="bg-white rounded-xl shadow-sm overflow-hidden">
          <div class="p-6">
            <!-- Header -->
            <div class="flex items-start justify-between mb-6">
              <div class="flex items-center gap-4">
                <BaseAvatar
                  :src="post.user?.avatar"
                  :name="post.user?.name"
                  size="lg"
                />
                <div>
                  <div class="flex items-center gap-2">
                    <router-link
                      :to="`/profile/${post.user?.username}`"
                      class="font-semibold text-gray-900 hover:text-primary-500"
                    >
                      {{ post.user?.name }}
                    </router-link>
                    <BaseBadge v-if="post.user?.verified" variant="primary" size="sm">Verified</BaseBadge>
                  </div>
                  <p class="text-sm text-gray-500">
                    {{ $filters.relativeTime(post.created_at) }}
                  </p>
                </div>
              </div>
              <BaseDropdown
                :items="postActions"
                variant="ghost"
              >
                <template #trigger>
                  <EllipsisHorizontalIcon class="h-5 w-5" />
                </template>
              </BaseDropdown>
            </div>

            <!-- Title -->
            <h1 class="text-2xl font-bold text-gray-900 mb-4">{{ post.title }}</h1>

            <!-- Tags -->
            <div class="flex flex-wrap gap-2 mb-6">
              <span
                v-for="tag in post.tags"
                :key="tag.id"
                class="px-3 py-1 bg-gray-100 text-gray-700 text-sm rounded-full"
              >
                #{{ tag.name }}
              </span>
            </div>

            <!-- Content -->
            <div class="prose prose-primary max-w-none" v-html="post.content"></div>

            <!-- Stats -->
            <div class="flex items-center gap-6 mt-6 pt-6 border-t border-gray-100">
              <button
                @click="vote('up')"
                :class="[
                  'flex items-center gap-2 px-4 py-2 rounded-lg transition-colors',
                  post.user_vote === 'up' ? 'bg-primary-100 text-primary-600' : 'hover:bg-gray-100'
                ]"
              >
                <ArrowUpIcon class="h-5 w-5" />
                <span>{{ post.upvotes_count || 0 }}</span>
              </button>
              <button
                @click="vote('down')"
                :class="[
                  'flex items-center gap-2 px-4 py-2 rounded-lg transition-colors',
                  post.user_vote === 'down' ? 'bg-red-100 text-red-600' : 'hover:bg-gray-100'
                ]"
              >
                <ArrowDownIcon class="h-5 w-5" />
                <span>{{ post.downvotes_count || 0 }}</span>
              </button>
              <button
                @click="bookmark"
                :class="[
                  'flex items-center gap-2 px-4 py-2 rounded-lg transition-colors',
                  post.is_bookmarked ? 'bg-yellow-100 text-yellow-600' : 'hover:bg-gray-100'
                ]"
              >
                <BookmarkIcon class="h-5 w-5" />
                <span>Save</span>
              </button>
              <button
                @click="share"
                class="flex items-center gap-2 px-4 py-2 rounded-lg hover:bg-gray-100 transition-colors"
              >
                <ShareIcon class="h-5 w-5" />
                <span>Share</span>
              </button>
            </div>
          </div>
        </article>

        <!-- Comments -->
        <section class="mt-8">
          <h2 class="text-lg font-semibold text-gray-900 mb-4">
            Comments ({{ post.comments_count || 0 }})
          </h2>

          <!-- Comment Form -->
          <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
            <textarea
              v-model="newComment"
              rows="4"
              placeholder="Write a comment..."
              class="w-full rounded-lg border-gray-300 focus:ring-primary-500 focus:border-primary-500"
            ></textarea>
            <div class="flex justify-end mt-4">
              <BaseButton @click="submitComment" :loading="submittingComment">
                Post Comment
              </BaseButton>
            </div>
          </div>

          <!-- Comments List -->
          <div class="space-y-4">
            <div
              v-for="comment in comments"
              :key="comment.id"
              class="bg-white rounded-xl shadow-sm p-6"
            >
              <div class="flex items-start gap-4">
                <BaseAvatar
                  :src="comment.user?.avatar"
                  :name="comment.user?.name"
                  size="md"
                />
                <div class="flex-1">
                  <div class="flex items-center gap-2 mb-2">
                    <router-link
                      :to="`/profile/${comment.user?.username}`"
                      class="font-semibold text-gray-900 hover:text-primary-500"
                    >
                      {{ comment.user?.name }}
                    </router-link>
                    <span class="text-sm text-gray-500">
                      {{ $filters.relativeTime(comment.created_at) }}
                    </span>
                  </div>
                  <div class="prose prose-sm max-w-none" v-html="comment.content"></div>
                  <div class="flex items-center gap-4 mt-4">
                    <button
                      @click="voteComment(comment.id, 'up')"
                      class="flex items-center gap-1 text-sm text-gray-500 hover:text-primary-500"
                    >
                      <ArrowUpIcon class="h-4 w-4" />
                      {{ comment.upvotes_count || 0 }}
                    </button>
                    <button
                      @click="voteComment(comment.id, 'down')"
                      class="flex items-center gap-1 text-sm text-gray-500 hover:text-red-500"
                    >
                      <ArrowDownIcon class="h-4 w-4" />
                    </button>
                    <button
                      @click="replyTo(comment)"
                      class="text-sm text-gray-500 hover:text-primary-500"
                    >
                      Reply
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>
      </template>

      <EmptyState
        v-else
        title="Post not found"
        description="The post you're looking for doesn't exist or has been removed."
      >
        <template #action>
          <router-link to="/community" class="text-primary-500 hover:text-primary-600">
            Back to Community
          </router-link>
        </template>
      </EmptyState>
    </main>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import {
  ArrowUpIcon,
  ArrowDownIcon,
  BookmarkIcon,
  ShareIcon,
  EllipsisHorizontalIcon
} from '@heroicons/vue/20/solid'
import api from '@/api'
import { useAuthStore } from '@/stores/auth'
import { useToastStore } from '@/stores/toast'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()
const toast = useToastStore()

const loading = ref(true)
const post = ref(null)
const comments = ref([])
const newComment = ref('')
const submittingComment = ref(false)

const postActions = computed(() => {
  const actions = [
    { label: 'Share', action: () => share() }
  ]
  
  if (authStore.user?.id === post.value?.user?.id) {
    actions.push({ label: 'Edit', action: () => router.push(`/post/${post.value.slug}/edit`) })
    actions.push({ label: 'Delete', action: () => deletePost() })
  }
  
  actions.push({ label: 'Report', action: () => reportPost() })
  
  return actions
})

const fetchPost = async () => {
  try {
    loading.value = true
    const response = await api.get(`/posts/${route.params.slug}`)
    post.value = response.data.data || response.data
    comments.value = post.value.comments || []
  } catch (error) {
    console.error('Failed to fetch post:', error)
  } finally {
    loading.value = false
  }
}

const vote = async (type) => {
  if (!authStore.isAuthenticated) {
    router.push({ name: 'login', query: { redirect: route.fullPath } })
    return
  }
  
  try {
    await api.post(`/posts/${post.value.id}/vote`, { type })
    if (post.value.user_vote === type) {
      post.value.user_vote = null
      if (type === 'up') post.value.upvotes_count--
      else post.value.downvotes_count--
    } else {
      if (post.value.user_vote) {
        if (post.value.user_vote === 'up') post.value.upvotes_count--
        else post.value.downvotes_count--
      }
      post.value.user_vote = type
      if (type === 'up') post.value.upvotes_count++
      else post.value.downvotes_count++
    }
  } catch (error) {
    toast.error('Failed to vote')
  }
}

const bookmark = async () => {
  if (!authStore.isAuthenticated) {
    router.push({ name: 'login', query: { redirect: route.fullPath } })
    return
  }
  
  try {
    await api.post(`/posts/${post.value.id}/bookmark`)
    post.value.is_bookmarked = !post.value.is_bookmarked
    toast.success(post.value.is_bookmarked ? 'Post saved!' : 'Post removed from saved')
  } catch (error) {
    toast.error('Failed to bookmark')
  }
}

const share = () => {
  navigator.clipboard.writeText(window.location.href)
  toast.success('Link copied to clipboard!')
}

const submitComment = async () => {
  if (!authStore.isAuthenticated) {
    router.push({ name: 'login', query: { redirect: route.fullPath } })
    return
  }
  
  if (!newComment.value.trim()) return
  
  try {
    submittingComment.value = true
    const response = await api.post(`/posts/${post.value.id}/comments`, {
      content: newComment.value
    })
    comments.value.unshift(response.data.data || response.data)
    newComment.value = ''
    toast.success('Comment posted!')
  } catch (error) {
    toast.error('Failed to post comment')
  } finally {
    submittingComment.value = false
  }
}

const voteComment = async (commentId, type) => {
  if (!authStore.isAuthenticated) {
    router.push({ name: 'login', query: { redirect: route.fullPath } })
    return
  }
  
  try {
    await api.post(`/comments/${commentId}/vote`, { type })
  } catch (error) {
    toast.error('Failed to vote')
  }
}

const replyTo = (comment) => {
  newComment.value = `@${comment.user?.username} `
}

const deletePost = async () => {
  if (!confirm('Are you sure you want to delete this post?')) return
  
  try {
    await api.delete(`/posts/${post.value.id}`)
    toast.success('Post deleted')
    router.push('/community')
  } catch (error) {
    toast.error('Failed to delete post')
  }
}

const reportPost = () => {
  toast.info('Report feature coming soon')
}

onMounted(() => {
  fetchPost()
})
</script>
