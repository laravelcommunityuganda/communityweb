<template>
  <div>
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-bold text-gray-900">Posts Management</h1>
      <select v-model="statusFilter" class="rounded-lg border-gray-300">
        <option value="">All Status</option>
        <option value="published">Published</option>
        <option value="pending">Pending</option>
        <option value="hidden">Hidden</option>
      </select>
    </div>

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Post</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Author</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Created</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <tr v-for="post in posts.data" :key="post.id">
            <td class="px-6 py-4">
              <p class="font-medium text-gray-900">{{ post.title }}</p>
              <p class="text-sm text-gray-500">{{ post.comments_count || 0 }} comments</p>
            </td>
            <td class="px-6 py-4">
              <div class="flex items-center gap-2">
                <BaseAvatar :src="post.user?.avatar" :name="post.user?.name" size="sm" />
                <span>{{ post.user?.name }}</span>
              </div>
            </td>
            <td class="px-6 py-4 text-sm text-gray-500">{{ post.category?.name }}</td>
            <td class="px-6 py-4">
              <BaseBadge :variant="getStatusVariant(post.status)">{{ post.status }}</BaseBadge>
            </td>
            <td class="px-6 py-4 text-sm text-gray-500">{{ $filters.date(post.created_at) }}</td>
            <td class="px-6 py-4 text-right">
              <BaseDropdown :items="getPostActions(post)" variant="ghost" />
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import api from '@/api'
import { useToastStore } from '@/stores/toast'

const toast = useToastStore()

const posts = ref({ data: [], meta: {} })
const statusFilter = ref('')

const fetchPosts = async () => {
  try {
    const params = new URLSearchParams()
    if (statusFilter.value) params.append('status', statusFilter.value)
    
    const response = await api.get(`/admin/posts?${params.toString()}`)
    posts.value = response.data
  } catch (error) {
    console.error('Failed to fetch posts:', error)
  }
}

const getStatusVariant = (status) => {
  const variants = { 'published': 'success', 'pending': 'warning', 'hidden': 'default' }
  return variants[status] || 'default'
}

const getPostActions = (post) => {
  return [
    { label: 'View', action: () => window.open(`/post/${post.slug}`, '_blank') },
    { label: post.status === 'hidden' ? 'Unhide' : 'Hide', action: () => toggleVisibility(post) },
    { label: 'Feature', action: () => featurePost(post) },
    { label: 'Delete', action: () => deletePost(post) }
  ]
}

const toggleVisibility = async (post) => {
  try {
    await api.put(`/admin/posts/${post.id}/visibility`)
    post.status = post.status === 'hidden' ? 'published' : 'hidden'
    toast.success('Post visibility updated')
  } catch (error) {
    toast.error('Failed to update post')
  }
}

const featurePost = async (post) => {
  try {
    await api.post(`/admin/posts/${post.id}/feature`)
    toast.success('Post featured')
  } catch (error) {
    toast.error('Failed to feature post')
  }
}

const deletePost = async (post) => {
  if (!confirm('Are you sure you want to delete this post?')) return
  try {
    await api.delete(`/admin/posts/${post.id}`)
    posts.value.data = posts.value.data.filter(p => p.id !== post.id)
    toast.success('Post deleted')
  } catch (error) {
    toast.error('Failed to delete post')
  }
}

watch(statusFilter, fetchPosts)
onMounted(fetchPosts)
</script>
