<template>
  <div>
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-bold text-gray-900">Jobs Management</h1>
      <select v-model="statusFilter" class="rounded-lg border-gray-300">
        <option value="">All Status</option>
        <option value="approved">Approved</option>
        <option value="pending">Pending</option>
        <option value="rejected">Rejected</option>
      </select>
    </div>

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Job</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Company</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Applications</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <tr v-for="job in jobs.data" :key="job.id">
            <td class="px-6 py-4">
              <p class="font-medium text-gray-900">{{ job.title }}</p>
              <p class="text-sm text-gray-500">{{ job.location }}</p>
            </td>
            <td class="px-6 py-4 text-sm text-gray-500">{{ job.company }}</td>
            <td class="px-6 py-4">
              <BaseBadge>{{ job.type }}</BaseBadge>
            </td>
            <td class="px-6 py-4">
              <BaseBadge :variant="getStatusVariant(job.status)">{{ job.status }}</BaseBadge>
            </td>
            <td class="px-6 py-4 text-sm text-gray-500">{{ job.applications_count || 0 }}</td>
            <td class="px-6 py-4 text-right">
              <BaseDropdown :items="getJobActions(job)" variant="ghost" />
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
const jobs = ref({ data: [], meta: {} })
const statusFilter = ref('')

const fetchJobs = async () => {
  try {
    const params = new URLSearchParams()
    if (statusFilter.value) params.append('status', statusFilter.value)
    const response = await api.get(`/admin/jobs?${params.toString()}`)
    jobs.value = response.data
  } catch (error) {
    console.error('Failed to fetch jobs:', error)
  }
}

const getStatusVariant = (status) => {
  const variants = { 'approved': 'success', 'pending': 'warning', 'rejected': 'danger' }
  return variants[status] || 'default'
}

const getJobActions = (job) => {
  const actions = [
    { label: 'View', action: () => window.open(`/jobs/${job.slug}`, '_blank') }
  ]
  if (job.status === 'pending') {
    actions.push({ label: 'Approve', action: () => updateStatus(job, 'approved') })
    actions.push({ label: 'Reject', action: () => updateStatus(job, 'rejected') })
  }
  actions.push({ label: 'Feature', action: () => featureJob(job) })
  actions.push({ label: 'Delete', action: () => deleteJob(job) })
  return actions
}

const updateStatus = async (job, status) => {
  try {
    await api.put(`/admin/jobs/${job.id}/status`, { status })
    job.status = status
    toast.success(`Job ${status}`)
  } catch (error) {
    toast.error('Failed to update job')
  }
}

const featureJob = async (job) => {
  try {
    await api.post(`/admin/jobs/${job.id}/feature`)
    toast.success('Job featured')
  } catch (error) {
    toast.error('Failed to feature job')
  }
}

const deleteJob = async (job) => {
  if (!confirm('Are you sure?')) return
  try {
    await api.delete(`/admin/jobs/${job.id}`)
    jobs.value.data = jobs.value.data.filter(j => j.id !== job.id)
    toast.success('Job deleted')
  } catch (error) {
    toast.error('Failed to delete job')
  }
}

watch(statusFilter, fetchJobs)
onMounted(fetchJobs)
</script>
