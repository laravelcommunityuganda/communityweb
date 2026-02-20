<template>
  <div>
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-bold text-gray-900">Reports</h1>
      <select v-model="statusFilter" class="rounded-lg border-gray-300">
        <option value="">All Status</option>
        <option value="pending">Pending</option>
        <option value="resolved">Resolved</option>
        <option value="dismissed">Dismissed</option>
      </select>
    </div>

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
      <div v-if="reports.data?.length" class="divide-y divide-gray-200">
        <div
          v-for="report in reports.data"
          :key="report.id"
          class="p-6"
        >
          <div class="flex items-start justify-between">
            <div class="flex items-start gap-4">
              <div class="h-10 w-10 rounded-full bg-red-100 flex items-center justify-center">
                <FlagIcon class="h-5 w-5 text-red-600" />
              </div>
              <div>
                <p class="font-medium text-gray-900">{{ report.reason }}</p>
                <p class="text-sm text-gray-500 mt-1">
                  Reported by {{ report.reporter?.name }} • {{ $filters.relativeTime(report.created_at) }}
                </p>
                <p class="text-sm text-gray-600 mt-2">
                  Type: <span class="font-medium">{{ report.reportable_type }}</span>
                </p>
                <div class="mt-3 flex gap-2">
                  <BaseButton size="sm" @click="viewContent(report)">View Content</BaseButton>
                  <BaseButton size="sm" variant="success" @click="resolve(report)">Resolve</BaseButton>
                  <BaseButton size="sm" variant="secondary" @click="dismiss(report)">Dismiss</BaseButton>
                </div>
              </div>
            </div>
            <BaseBadge :variant="getStatusVariant(report.status)">{{ report.status }}</BaseBadge>
          </div>
        </div>
      </div>
      <EmptyState v-else title="No reports" description="All reports have been handled" />
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import { FlagIcon } from '@heroicons/vue/20/solid'
import api from '@/api'
import { useToastStore } from '@/stores/toast'

const toast = useToastStore()
const reports = ref({ data: [], meta: {} })
const statusFilter = ref('')

const fetchReports = async () => {
  try {
    const params = new URLSearchParams()
    if (statusFilter.value) params.append('status', statusFilter.value)
    const response = await api.get(`/admin/reports?${params.toString()}`)
    reports.value = response.data
  } catch (error) {
    console.error('Failed to fetch reports:', error)
  }
}

const getStatusVariant = (status) => {
  const variants = { 'pending': 'warning', 'resolved': 'success', 'dismissed': 'default' }
  return variants[status] || 'default'
}

const viewContent = (report) => {
  if (report.reportable?.slug) {
    const routes = { 'Post': '/post/', 'Job': '/jobs/', 'Event': '/events/' }
    const route = routes[report.reportable_type] || '/'
    window.open(`${route}${report.reportable.slug}`, '_blank')
  }
}

const resolve = async (report) => {
  try {
    await api.put(`/admin/reports/${report.id}/resolve`)
    report.status = 'resolved'
    toast.success('Report resolved')
  } catch (error) {
    toast.error('Failed to resolve report')
  }
}

const dismiss = async (report) => {
  try {
    await api.put(`/admin/reports/${report.id}/dismiss`)
    report.status = 'dismissed'
    toast.success('Report dismissed')
  } catch (error) {
    toast.error('Failed to dismiss report')
  }
}

watch(statusFilter, fetchReports)
onMounted(fetchReports)
</script>
