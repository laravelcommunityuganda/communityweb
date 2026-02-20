<template>
  <div class="min-h-screen bg-gray-50">
    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="bg-white rounded-xl shadow-sm p-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Create New Post</h1>

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
              <div class="border border-gray-300 rounded-lg overflow-hidden">
                <div class="bg-gray-50 border-b border-gray-300 p-2 flex gap-2">
                  <button
                    type="button"
                    @click="insertMarkdown('**', '**')"
                    class="p-2 hover:bg-gray-200 rounded"
                    title="Bold"
                  >
                    <strong>B</strong>
                  </button>
                  <button
                    type="button"
                    @click="insertMarkdown('*', '*')"
                    class="p-2 hover:bg-gray-200 rounded italic"
                    title="Italic"
                  >
                    I
                  </button>
                  <button
                    type="button"
                    @click="insertMarkdown('`', '`')"
                    class="p-2 hover:bg-gray-200 rounded font-mono"
                    title="Code"
                  >
                    &lt;/&gt;
                  </button>
                  <button
                    type="button"
                    @click="insertMarkdown('\n```\n', '\n```\n')"
                    class="p-2 hover:bg-gray-200 rounded font-mono text-sm"
                    title="Code Block"
                  >
                    Code Block
                  </button>
                  <button
                    type="button"
                    @click="insertMarkdown('[', '](url)')"
                    class="p-2 hover:bg-gray-200 rounded"
                    title="Link"
                  >
                    🔗
                  </button>
                </div>
                <textarea
                  ref="contentEditor"
                  v-model="form.content"
                  rows="12"
                  class="w-full border-0 focus:ring-0 resize-none"
                  placeholder="Write your post content here... Markdown is supported!"
                ></textarea>
              </div>
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
              <p class="mt-1 text-sm text-gray-500">Add up to 5 tags to describe your post</p>
            </div>

            <!-- Submit -->
            <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-100">
              <router-link
                to="/community"
                class="px-4 py-2 text-gray-700 hover:text-gray-900"
              >
                Cancel
              </router-link>
              <BaseButton type="submit" :loading="form.processing">
                Create Post
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
import { useRouter } from 'vue-router'
import api from '@/api'
import { useToastStore } from '@/stores/toast'

const router = useRouter()
const toast = useToastStore()

const categories = ref([])
const selectedTags = ref([])
const tagInput = ref('')
const contentEditor = ref(null)

const form = ref({
  title: '',
  content: '',
  category_id: '',
  tags: [],
  errors: {},
  processing: false
})

const fetchCategories = async () => {
  try {
    const response = await api.get('/categories')
    categories.value = response.data.data || response.data
  } catch (error) {
    console.error('Failed to fetch categories:', error)
  }
}

const insertMarkdown = (before, after) => {
  const textarea = contentEditor.value
  if (!textarea) return
  
  const start = textarea.selectionStart
  const end = textarea.selectionEnd
  const text = form.value.content
  const selectedText = text.substring(start, end)
  
  form.value.content = text.substring(0, start) + before + selectedText + after + text.substring(end)
  
  // Reset cursor position
  setTimeout(() => {
    textarea.focus()
    textarea.setSelectionRange(start + before.length, end + before.length)
  }, 0)
}

const addTag = () => {
  const tag = tagInput.value.trim().toLowerCase()
  if (tag && !selectedTags.value.includes(tag) && selectedTags.value.length < 5) {
    selectedTags.value.push(tag)
    form.value.tags = selectedTags.value
  }
  tagInput.value = ''
}

const removeTag = (tag) => {
  selectedTags.value = selectedTags.value.filter(t => t !== tag)
  form.value.tags = selectedTags.value
}

const submit = async () => {
  try {
    form.value.processing = true
    form.value.errors = {}
    
    const response = await api.post('/posts', {
      title: form.value.title,
      content: form.value.content,
      category_id: form.value.category_id,
      tags: selectedTags.value
    })
    
    toast.success('Post created successfully!')
    router.push(`/post/${response.data.data.slug}`)
  } catch (error) {
    if (error.response?.data?.errors) {
      form.value.errors = error.response.data.errors
    } else {
      toast.error('Failed to create post')
    }
  } finally {
    form.value.processing = false
  }
}

onMounted(() => {
  fetchCategories()
})
</script>
