<template>
  <div>
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-bold text-gray-900">Categories</h1>
      <BaseButton @click="showModal = true">Add Category</BaseButton>
    </div>

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Slug</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Posts</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <tr v-for="category in categories" :key="category.id">
            <td class="px-6 py-4 font-medium text-gray-900">{{ category.name }}</td>
            <td class="px-6 py-4 text-sm text-gray-500">{{ category.slug }}</td>
            <td class="px-6 py-4 text-sm text-gray-500">{{ category.posts_count || 0 }}</td>
            <td class="px-6 py-4 text-right">
              <BaseDropdown :items="getActions(category)" variant="ghost" />
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal -->
    <BaseModal :isOpen="showModal" :title="editing ? 'Edit Category' : 'Add Category'" @close="closeModal">
      <form @submit.prevent="submit">
        <div class="space-y-4">
          <BaseInput id="name" label="Name" v-model="form.name" required />
          <BaseInput id="slug" label="Slug" v-model="form.slug" />
          <textarea
            v-model="form.description"
            placeholder="Description"
            class="w-full rounded-lg border-gray-300"
            rows="3"
          ></textarea>
        </div>
        <div class="flex justify-end gap-4 mt-6">
          <BaseButton variant="secondary" @click="closeModal">Cancel</BaseButton>
          <BaseButton type="submit" :loading="form.processing">Save</BaseButton>
        </div>
      </form>
    </BaseModal>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/api'
import { useToastStore } from '@/stores/toast'

const toast = useToastStore()
const categories = ref([])
const showModal = ref(false)
const editing = ref(null)

const form = ref({
  name: '',
  slug: '',
  description: '',
  processing: false
})

const fetchCategories = async () => {
  try {
    const response = await api.get('/admin/categories')
    categories.value = response.data.data || response.data
  } catch (error) {
    console.error('Failed to fetch categories:', error)
  }
}

const getActions = (category) => [
  { label: 'Edit', action: () => edit(category) },
  { label: 'Delete', action: () => deleteCategory(category) }
]

const edit = (category) => {
  editing.value = category
  form.value.name = category.name
  form.value.slug = category.slug
  form.value.description = category.description
  showModal.value = true
}

const submit = async () => {
  try {
    form.value.processing = true
    if (editing.value) {
      await api.put(`/admin/categories/${editing.value.id}`, form.value)
      const index = categories.value.findIndex(c => c.id === editing.value.id)
      categories.value[index] = { ...categories.value[index], ...form.value }
      toast.success('Category updated')
    } else {
      const response = await api.post('/admin/categories', form.value)
      categories.value.push(response.data.data || response.data)
      toast.success('Category created')
    }
    closeModal()
  } catch (error) {
    toast.error('Failed to save category')
  } finally {
    form.value.processing = false
  }
}

const deleteCategory = async (category) => {
  if (!confirm('Are you sure?')) return
  try {
    await api.delete(`/admin/categories/${category.id}`)
    categories.value = categories.value.filter(c => c.id !== category.id)
    toast.success('Category deleted')
  } catch (error) {
    toast.error('Failed to delete category')
  }
}

const closeModal = () => {
  showModal.value = false
  editing.value = null
  form.value = { name: '', slug: '', description: '', processing: false }
}

onMounted(fetchCategories)
</script>
