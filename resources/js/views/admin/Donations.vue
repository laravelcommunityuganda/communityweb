<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Donation Management</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Manage donation milestones and track contributions</p>
      </div>
      <button
        @click="showCreateModal = true"
        class="px-4 py-2 bg-laravel-500 text-white rounded-lg hover:bg-laravel-600 transition flex items-center gap-2"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        New Milestone
      </button>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
      <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 dark:text-gray-400 text-sm">Total Raised</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ formatCurrency(statistics.total_donations) }}</p>
          </div>
          <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
        </div>
      </div>

      <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 dark:text-gray-400 text-sm">Total Donors</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ statistics.total_donors }}</p>
          </div>
          <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
          </div>
        </div>
      </div>

      <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 dark:text-gray-400 text-sm">Completed</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ statistics.completed_donations }}</p>
          </div>
          <div class="w-12 h-12 bg-laravel-100 dark:bg-laravel-900/30 rounded-lg flex items-center justify-center">
            <svg class="w-6 h-6 text-laravel-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
        </div>
      </div>

      <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 dark:text-gray-400 text-sm">Pending</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ statistics.pending_donations }}</p>
          </div>
          <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg flex items-center justify-center">
            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
        </div>
      </div>
    </div>

    <!-- Tabs -->
    <div class="border-b border-gray-200 dark:border-gray-700">
      <nav class="flex space-x-8">
        <button
          v-for="tab in tabs"
          :key="tab.id"
          @click="activeTab = tab.id"
          :class="[
            'py-4 px-1 border-b-2 font-medium text-sm transition-colors',
            activeTab === tab.id
              ? 'border-laravel-500 text-laravel-600 dark:text-laravel-400'
              : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'
          ]"
        >
          {{ tab.name }}
        </button>
      </nav>
    </div>

    <!-- Milestones Tab -->
    <div v-if="activeTab === 'milestones'" class="space-y-6">
      <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div 
          v-for="milestone in milestones" 
          :key="milestone.id"
          class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden"
        >
          <div class="p-6">
            <div class="flex items-start justify-between mb-4">
              <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ milestone.title }}</h3>
                <span 
                  :class="milestone.is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400'"
                  class="inline-block px-2 py-1 text-xs font-medium rounded mt-1"
                >
                  {{ milestone.is_active ? 'Active' : 'Inactive' }}
                </span>
              </div>
              <div class="flex items-center gap-2">
                <button 
                  @click="editMilestone(milestone)"
                  class="p-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                  </svg>
                </button>
                <button 
                  @click="deleteMilestone(milestone)"
                  class="p-1 text-gray-400 hover:text-danger-600"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                  </svg>
                </button>
              </div>
            </div>

            <p class="text-gray-600 dark:text-gray-400 text-sm mb-4 line-clamp-2">{{ milestone.description }}</p>

            <!-- Progress -->
            <div class="mb-4">
              <div class="flex justify-between text-sm mb-1">
                <span class="text-gray-600 dark:text-gray-400">Progress</span>
                <span class="font-medium text-gray-900 dark:text-white">{{ milestone.progress_percentage }}%</span>
              </div>
              <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                <div 
                  class="bg-laravel-500 h-2 rounded-full transition-all duration-500" 
                  :style="{ width: `${milestone.progress_percentage}%` }"
                ></div>
              </div>
            </div>

            <div class="flex items-center justify-between text-sm">
              <span class="text-gray-500 dark:text-gray-400">
                {{ formatCurrency(milestone.current_amount) }} / {{ formatCurrency(milestone.target_amount) }}
              </span>
              <span class="text-gray-500 dark:text-gray-400">
                {{ milestone.donors_count }} donors
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Donations Tab -->
    <div v-if="activeTab === 'donations'" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
          <thead class="bg-gray-50 dark:bg-gray-700">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Transaction</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Donor</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Amount</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Method</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
            <tr v-for="donation in donations" :key="donation.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="text-sm font-mono text-gray-900 dark:text-white">{{ donation.transaction_id }}</span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div>
                  <p class="text-sm font-medium text-gray-900 dark:text-white">{{ donation.donor_name }}</p>
                  <p v-if="donation.donor_email" class="text-xs text-gray-500 dark:text-gray-400">{{ donation.donor_email }}</p>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ formatCurrency(donation.amount, donation.currency) }}</span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="text-sm text-gray-600 dark:text-gray-400 capitalize">{{ donation.payment_method }}</span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span 
                  :class="{
                    'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400': donation.payment_status === 'completed',
                    'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400': donation.payment_status === 'pending',
                    'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400': donation.payment_status === 'failed'
                  }"
                  class="px-2 py-1 text-xs font-medium rounded capitalize"
                >
                  {{ donation.payment_status }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                {{ formatDate(donation.created_at) }}
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="donationsMeta.last_page > 1" class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between">
          <p class="text-sm text-gray-500 dark:text-gray-400">
            Showing {{ ((donationsMeta.current_page - 1) * 20) + 1 }} to {{ Math.min(donationsMeta.current_page * 20, donationsMeta.total) }} of {{ donationsMeta.total }} results
          </p>
          <div class="flex gap-2">
            <button 
              @click="fetchDonations(donationsMeta.current_page - 1)"
              :disabled="donationsMeta.current_page === 1"
              class="px-3 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50"
            >
              Previous
            </button>
            <button 
              @click="fetchDonations(donationsMeta.current_page + 1)"
              :disabled="donationsMeta.current_page === donationsMeta.last_page"
              class="px-3 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50"
            >
              Next
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Create/Edit Modal -->
    <div v-if="showCreateModal || editingMilestone" class="fixed inset-0 z-50 overflow-y-auto">
      <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="closeModal"></div>
        <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-xl max-w-lg w-full p-6">
          <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6">
            {{ editingMilestone ? 'Edit Milestone' : 'Create Milestone' }}
          </h3>

          <form @submit.prevent="saveMilestone">
            <div class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Title</label>
                <input 
                  v-model="milestoneForm.title"
                  type="text"
                  required
                  class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 focus:ring-2 focus:ring-laravel-500"
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                <textarea 
                  v-model="milestoneForm.description"
                  rows="3"
                  class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 focus:ring-2 focus:ring-laravel-500"
                ></textarea>
              </div>

              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Target Amount</label>
                  <input 
                    v-model.number="milestoneForm.target_amount"
                    type="number"
                    step="0.01"
                    min="1"
                    required
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 focus:ring-2 focus:ring-laravel-500"
                  />
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Currency</label>
                  <select 
                    v-model="milestoneForm.currency"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 focus:ring-2 focus:ring-laravel-500"
                  >
                    <option value="USD">USD</option>
                    <option value="EUR">EUR</option>
                    <option value="GBP">GBP</option>
                    <option value="KES">KES (KSh)</option>
                    <option value="UGX">UGX (USh)</option>
                  </select>
                </div>
              </div>

              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start Date</label>
                  <input 
                    v-model="milestoneForm.start_date"
                    type="date"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 focus:ring-2 focus:ring-laravel-500"
                  />
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">End Date</label>
                  <input 
                    v-model="milestoneForm.end_date"
                    type="date"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 focus:ring-2 focus:ring-laravel-500"
                  />
                </div>
              </div>

              <div class="flex items-center gap-6">
                <label class="flex items-center gap-2 cursor-pointer">
                  <input 
                    type="checkbox" 
                    v-model="milestoneForm.is_active"
                    class="rounded border-gray-300 text-laravel-600 focus:ring-laravel-500"
                  />
                  <span class="text-sm text-gray-700 dark:text-gray-300">Active</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                  <input 
                    type="checkbox" 
                    v-model="milestoneForm.is_featured"
                    class="rounded border-gray-300 text-laravel-600 focus:ring-laravel-500"
                  />
                  <span class="text-sm text-gray-700 dark:text-gray-300">Featured</span>
                </label>
              </div>
            </div>

            <div class="flex justify-end gap-3 mt-6">
              <button 
                type="button"
                @click="closeModal"
                class="px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg"
              >
                Cancel
              </button>
              <button 
                type="submit"
                class="px-4 py-2 bg-laravel-500 text-white rounded-lg hover:bg-laravel-600"
              >
                {{ editingMilestone ? 'Update' : 'Create' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const activeTab = ref('milestones')
const tabs = [
  { id: 'milestones', name: 'Milestones' },
  { id: 'donations', name: 'Donations' },
]

const statistics = ref({
  total_donations: 0,
  total_donors: 0,
  completed_donations: 0,
  pending_donations: 0,
})

const milestones = ref([])
const donations = ref([])
const donationsMeta = ref({ current_page: 1, last_page: 1, total: 0 })

const showCreateModal = ref(false)
const editingMilestone = ref(null)

const milestoneForm = ref({
  title: '',
  description: '',
  target_amount: 1000,
  currency: 'USD',
  start_date: '',
  end_date: '',
  is_active: true,
  is_featured: false,
})

const fetchStatistics = async () => {
  try {
    const response = await axios.get('/api/v1/admin/donations/statistics')
    statistics.value = response.data.data
  } catch (error) {
    console.error('Failed to fetch statistics:', error)
  }
}

const fetchMilestones = async () => {
  try {
    const response = await axios.get('/api/v1/admin/donations/milestones')
    milestones.value = response.data.data
  } catch (error) {
    console.error('Failed to fetch milestones:', error)
  }
}

const fetchDonations = async (page = 1) => {
  try {
    const response = await axios.get(`/api/v1/admin/donations?page=${page}`)
    donations.value = response.data.data
    donationsMeta.value = response.data.meta
  } catch (error) {
    console.error('Failed to fetch donations:', error)
  }
}

const editMilestone = (milestone) => {
  editingMilestone.value = milestone
  milestoneForm.value = {
    title: milestone.title,
    description: milestone.description || '',
    target_amount: milestone.target_amount,
    currency: milestone.currency,
    start_date: milestone.start_date || '',
    end_date: milestone.end_date || '',
    is_active: milestone.is_active,
    is_featured: milestone.is_featured,
  }
}

const deleteMilestone = async (milestone) => {
  if (!confirm(`Are you sure you want to delete "${milestone.title}"?`)) return

  try {
    await axios.delete(`/api/v1/admin/donations/milestones/${milestone.id}`)
    await fetchMilestones()
  } catch (error) {
    console.error('Failed to delete milestone:', error)
  }
}

const saveMilestone = async () => {
  try {
    if (editingMilestone.value) {
      await axios.put(`/api/v1/admin/donations/milestones/${editingMilestone.value.id}`, milestoneForm.value)
    } else {
      await axios.post('/api/v1/admin/donations/milestones', milestoneForm.value)
    }
    closeModal()
    await fetchMilestones()
  } catch (error) {
    console.error('Failed to save milestone:', error)
  }
}

const closeModal = () => {
  showCreateModal.value = false
  editingMilestone.value = null
  milestoneForm.value = {
    title: '',
    description: '',
    target_amount: 1000,
    currency: 'USD',
    start_date: '',
    end_date: '',
    is_active: true,
    is_featured: false,
  }
}

const formatCurrency = (amount, currency = 'USD') => {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: currency,
  }).format(amount)
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  })
}

onMounted(() => {
  fetchStatistics()
  fetchMilestones()
  fetchDonations()
})
</script>
