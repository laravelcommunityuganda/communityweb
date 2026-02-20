<template>
  <div>
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-bold text-gray-900">Users</h1>
      <div class="flex gap-4">
        <input
          v-model="search"
          type="search"
          placeholder="Search users..."
          class="rounded-lg border-gray-300 focus:ring-primary-500 focus:border-primary-500"
        />
        <select v-model="roleFilter" class="rounded-lg border-gray-300">
          <option value="">All Roles</option>
          <option value="admin">Admin</option>
          <option value="moderator">Moderator</option>
          <option value="member">Member</option>
        </select>
      </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joined</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="user in users.data" :key="user.id">
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex items-center gap-3">
                <BaseAvatar :src="user.avatar" :name="user.name" size="sm" />
                <div>
                  <p class="font-medium text-gray-900">{{ user.name }}</p>
                  <p class="text-sm text-gray-500">{{ user.email }}</p>
                </div>
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <BaseBadge :variant="getRoleVariant(user.role)">{{ user.role }}</BaseBadge>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ $filters.date(user.created_at) }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <BaseBadge :variant="user.email_verified_at ? 'success' : 'warning'">
                {{ user.email_verified_at ? 'Verified' : 'Unverified' }}
              </BaseBadge>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <BaseDropdown
                :items="getUserActions(user)"
                variant="ghost"
              />
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

const users = ref({ data: [], meta: {} })
const search = ref('')
const roleFilter = ref('')

const fetchUsers = async () => {
  try {
    const params = new URLSearchParams()
    if (search.value) params.append('search', search.value)
    if (roleFilter.value) params.append('role', roleFilter.value)
    
    const response = await api.get(`/admin/users?${params.toString()}`)
    users.value = response.data
  } catch (error) {
    console.error('Failed to fetch users:', error)
  }
}

const getUserActions = (user) => {
  return [
    { label: 'View Profile', action: () => window.open(`/profile/${user.username}`, '_blank') },
    { label: 'Edit Role', action: () => editRole(user) },
    { label: user.banned_at ? 'Unban' : 'Ban', action: () => toggleBan(user) },
    { label: 'Delete', action: () => deleteUser(user) }
  ]
}

const getRoleVariant = (role) => {
  const variants = {
    'admin': 'danger',
    'moderator': 'warning',
    'member': 'default'
  }
  return variants[role] || 'default'
}

const editRole = async (user) => {
  const role = prompt('Enter new role (admin, moderator, member):', user.role)
  if (role && ['admin', 'moderator', 'member'].includes(role)) {
    try {
      await api.put(`/admin/users/${user.id}/role`, { role })
      user.role = role
      toast.success('Role updated')
    } catch (error) {
      toast.error('Failed to update role')
    }
  }
}

const toggleBan = async (user) => {
  if (!confirm(`Are you sure you want to ${user.banned_at ? 'unban' : 'ban'} this user?`)) return
  
  try {
    await api.post(`/admin/users/${user.id}/${user.banned_at ? 'unban' : 'ban'}`)
    user.banned_at = user.banned_at ? null : new Date().toISOString()
    toast.success(`User ${user.banned_at ? 'banned' : 'unbanned'}`)
  } catch (error) {
    toast.error('Failed to update user')
  }
}

const deleteUser = async (user) => {
  if (!confirm('Are you sure you want to delete this user?')) return
  
  try {
    await api.delete(`/admin/users/${user.id}`)
    users.value.data = users.value.data.filter(u => u.id !== user.id)
    toast.success('User deleted')
  } catch (error) {
    toast.error('Failed to delete user')
  }
}

watch([search, roleFilter], () => {
  fetchUsers()
})

onMounted(() => {
  fetchUsers()
})
</script>
