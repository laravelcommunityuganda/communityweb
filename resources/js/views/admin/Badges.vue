<template>
  <div>
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-bold text-gray-900">Badges</h1>
      <BaseButton @click="showModal = true">Add Badge</BaseButton>
    </div>

    <div class="grid grid-cols-3 gap-6">
      <div
        v-for="badge in badges"
        :key="badge.id"
        class="bg-white rounded-xl shadow-sm p-6"
      >
        <div class="flex items-center gap-4 mb-4">
          <span class="text-4xl">{{ badge.icon }}</span>
          <div>
            <h3 class="font-semibold text-gray-900">{{ badge.name }}</h3>
            <p class="text-sm text-gray-500">{{ badge.users_count || 0 }} users</p>
          </div>
        </div>
        <p class="text-sm text-gray-600 mb-4">{{ badge.description }}</p>
        <div class="flex justify-end">
          <BaseDropdown :items="getActions(badge)" variant="ghost" />
        </div>
      </div>
    </div>

    <!-- Modal -->
    <BaseModal :isOpen="showModal" :title="editing ? 'Edit Badge' : 'Add Badge'" @close="closeModal">
      <form @submit.prevent="submit">
        <div class="space-y-4">
          <BaseInput id="name" label="Name" v-model="form.name" required />
          <BaseInput id="icon" label="Icon (emoji)" v-model="form.icon" placeholder="🏆" />
          <textarea
            v-model="form.description"
            placeholder="Description"
            class="w-full rounded-lg border-gray-300"
            rows="3"
          ></textarea>
          <BaseInput id="points_required" label="Points Required" v-model="form.points_required" type="number" />
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
const badges = ref([])
const showModal = ref(false)
const editing = ref(null)

const form = ref({
  name: '',
  icon: '🏆',
  description: '',
  points_required: 0,
  processing: false
})

const fetchBadges = async () => {
  try {
    const response = await api.get('/admin/badges')
    badges.value = response.data.data || response.data
  } catch (error) {
    console.error('Failed to fetch badges:', error)
  }
}

const getActions = (badge) => [
  { label: 'Edit', action: () => edit(badge) },
  { label: 'Delete', action: () => deleteBadge(badge) }
]

const edit = (badge) => {
  editing.value = badge
  form.value.name = badge.name
  form.value.icon = badge.icon
  form.value.description = badge.description
  form.value.points_required = badge.points_required
  showModal.value = true
}

const submit = async () => {
  try {
    form.value.processing = true
    if (editing.value) {
      await api.put(`/admin/badges/${editing.value.id}`, form.value)
      const index = badges.value.findIndex(b => b.id === editing.value.id)
      badges.value[index] = { ...badges.value[index], ...form.value }
      toast.success('Badge updated')
    } else {
      const response = await api.post('/admin/badges', form.value)
      badges.value.push(response.data.data || response.data)
      toast.success('Badge created')
    }
    closeModal()
  } catch (error) {
    toast.error('Failed to save badge')
  } finally {
    form.value.processing = false
  }
}

const deleteBadge = async (badge) => {
  if (!confirm('Are you sure?')) return
  try {
    await api.delete(`/admin/badges/${badge.id}`)
    badges.value = badges.value.filter(b => b.id !== badge.id)
    toast.success('Badge deleted')
  } catch (error) {
    toast.error('Failed to delete badge')
  }
}

const closeModal = () => {
  showModal.value = false
  editing.value = null
  form.value = { name: '', icon: '🏆', description: '', points_required: 0, processing: false }
}

onMounted(fetchBadges)
</script>
