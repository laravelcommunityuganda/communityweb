<template>
  <div class="min-h-screen bg-gray-50">
    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <LoadingSpinner v-if="loading" class="py-20" />
      
      <div v-else class="bg-white rounded-xl shadow-sm p-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Edit Post</h1>

        <form @submit.prevent="submit">
          <div class="space-y-6">
            <!-- Title -->
            <BaseInput
              id="title"
              label="Title"
              v-model="form.title"
              :error="form.errors.title"
              placeholder="Enter a descriptive title"
              required
            />

            <!-- Category -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                Category <span class="text-red-500">*</span>
              </label>
              <select
                v-model="form.category_id"
                class="w-full rounded-lg border-gray-300 focus:ring-primary-500 focus:border-primary-500"
              >
                <option value="">Select a category</option>
                <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                  {{ cat.name }}
                </option>
              </select>
              <p v-if="form.errors.category_id" class="mt-1 text-sm text-red-500">
                {{ form.errors.category_id }}
              </p>
            </div>

            <!-- Content -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                Content <span class="text-red-500">*</span>
              </label>
              <textarea
                v-model="form.content"
                rows="12"
                class="w-full rounded-lg border-gray-300 focus:ring-primary-500 focus:border-primary-500"
                placeholder="Write your post content here... Markdown is supported!"
              ></textarea>
              <p v-if="form.errors.content" class="mt-1 text-sm text-red-500">
                {{ form.errors.content }}
              </p>
            </div>

            <!-- Tags -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Tags</label>
              <div class="flex flex-wrap gap-2 mb-2">
                <span
                  v-for="tag in selectedTags"
                  :key="tag"
                  class="inline-flex items-center gap-1 px-3 py-1 bg-primary-100 text-primary-700 text-sm rounded-full"
                >
                  {{ tag }}
                  <button
                    type="button"
                    @click="removeTag(tag)"
                    class="hover:text-primary-900"
                  >
                    ×
                  </button>
                </span>
              </div>
              <input
                v-model="tagInput"
                @keydown.enter.prevent="addTag"
                type="text"
                class="w-full rounded-lg border-gray-300 focus:ring-primary-500 focus:border-primary-500"
                placeholder="Type a tag and press Enter"
              />
            </div>

            <!-- Submit -->
            <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-100">
              <router-link
                :to="`/post/${post?.slug}`"
                class="px-4 py-2 text-gray-700 hover:text-gray-900"
              >
                Cancel
              </router-link>
              <BaseButton type="submit" :loading="form.processing">
                Update Post
              </BaseButton>
            </div>
          </div>
        </form>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '@/api'
import { useToastStore } from '@/stores/toast'

const route = useRoute()
const router = useRouter()
const toast = useToastStore()

const loading = ref(true)
const post = ref(null)
const categories = ref([])
const selectedTags = ref([])
const tagInput = ref('')

const form = ref({
  title: '',
  content: '',
  category_id: '',
  tags: [],
  errors: {},
  processing: false
})

const fetchPost = async () => {
  try {
    const response = await api.get(`/posts/${route.params.slug}`)
    post.value = response.data.data || response.data
    form.value.title = post.value.title
    form.value.content = post.value.content
    form.value.category_id = post.value.category_id
    selectedTags.value = post.value.tags?.map(t => t.name) || []
  } catch (error) {
    toast.error('Failed to load post')
    router.push('/community')
  } finally {
    loading.value = false
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

const addTag = () => {
  const tag = tagInput.value.trim().toLowerCase()
  if (tag && !selectedTags.value.includes(tag) && selectedTags.value.length < 5) {
    selectedTags.value.push(tag)
  }
  tagInput.value = ''
}

const removeTag = (tag) => {
  selectedTags.value = selectedTags.value.filter(t => t !== tag)
}

const submit = async () => {
  try {
    form.value.processing = true
    form.value.errors = {}
    
    const response = await api.put(`/posts/${post.value.id}`, {
      title: form.value.title,
      content: form.value.content,
      category_id: form.value.category_id,
      tags: selectedTags.value
    })
    
    toast.success('Post updated successfully!')
    router.push(`/post/${response.data.data.slug}`)
  } catch (error) {
    if (error.response?.data?.errors) {
      form.value.errors = error.response.data.errors
    } else {
      toast.error('Failed to update post')
    }
  } finally {
    form.value.processing = false
  }
}

onMounted(() => {
  fetchPost()
  fetchCategories()
})
</script>
