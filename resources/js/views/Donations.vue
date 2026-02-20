<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <div class="container mx-auto px-4 py-8">
      <!-- Header -->
      <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">Support Our Community</h1>
        <p class="text-lg text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
          Your donations help us maintain and improve the Laravel Uganda community platform, 
          organize events, and support local developers.
        </p>
      </div>

      <!-- Featured Milestone -->
      <div v-if="featuredMilestone" class="mb-12">
        <div class="bg-gradient-to-r from-laravel-500 to-laravel-600 rounded-2xl p-8 text-white">
          <div class="flex flex-col lg:flex-row items-center gap-8">
            <div class="flex-1">
              <span class="inline-block px-3 py-1 bg-white/20 rounded-full text-sm font-medium mb-4">
                Featured Campaign
              </span>
              <h2 class="text-3xl font-bold mb-4">{{ featuredMilestone.title }}</h2>
              <p class="text-laravel-100 mb-6">{{ featuredMilestone.description }}</p>
              
              <!-- Progress -->
              <div class="mb-4">
                <div class="flex justify-between text-sm mb-2">
                  <span>{{ formatCurrency(featuredMilestone.current_amount, featuredMilestone.currency) }} raised</span>
                  <span>{{ featuredMilestone.progress_percentage }}%</span>
                </div>
                <div class="w-full bg-white/20 rounded-full h-3">
                  <div 
                    class="bg-white h-3 rounded-full transition-all duration-500" 
                    :style="{ width: `${featuredMilestone.progress_percentage}%` }"
                  ></div>
                </div>
                <p class="text-sm text-laravel-100 mt-2">
                  Goal: {{ formatCurrency(featuredMilestone.target_amount, featuredMilestone.currency) }}
                </p>
              </div>

              <div class="flex items-center gap-4">
                <span class="flex items-center gap-2">
                  <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                  {{ featuredMilestone.donors_count }} donors
                </span>
                <span v-if="featuredMilestone.end_date" class="flex items-center gap-2">
                  <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                  </svg>
                  Ends {{ formatDate(featuredMilestone.end_date) }}
                </span>
              </div>
            </div>
            <div class="flex-shrink-0">
              <button 
                @click="openDonationModal(featuredMilestone)"
                class="px-8 py-4 bg-white text-laravel-600 font-bold rounded-xl hover:bg-gray-100 transition shadow-lg"
              >
                Donate Now
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- All Milestones -->
      <div v-if="milestones.length > 0" class="mb-12">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Active Campaigns</h2>
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div 
            v-for="milestone in milestones" 
            :key="milestone.id"
            class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden hover:shadow-md transition"
          >
            <div v-if="milestone.image" class="h-48 bg-gray-200 dark:bg-gray-700">
              <img :src="milestone.image" :alt="milestone.title" class="w-full h-full object-cover">
            </div>
            <div v-else class="h-48 bg-gradient-to-br from-laravel-400 to-laravel-600 flex items-center justify-center">
              <svg class="w-16 h-16 text-white/50" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/>
              </svg>
            </div>
            <div class="p-6">
              <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">{{ milestone.title }}</h3>
              <p class="text-gray-600 dark:text-gray-400 text-sm mb-4 line-clamp-2">{{ milestone.description }}</p>
              
              <!-- Progress -->
              <div class="mb-4">
                <div class="flex justify-between text-sm mb-1">
                  <span class="text-gray-600 dark:text-gray-400">{{ milestone.progress_percentage }}%</span>
                  <span class="font-medium text-gray-900 dark:text-white">
                    {{ formatCurrency(milestone.current_amount, milestone.currency) }}
                  </span>
                </div>
                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                  <div 
                    class="bg-laravel-500 h-2 rounded-full transition-all duration-500" 
                    :style="{ width: `${milestone.progress_percentage}%` }"
                  ></div>
                </div>
              </div>

              <div class="flex items-center justify-between">
                <span class="text-sm text-gray-500 dark:text-gray-400">
                  {{ milestone.donors_count }} donors
                </span>
                <button 
                  @click="openDonationModal(milestone)"
                  class="px-4 py-2 bg-laravel-500 text-white rounded-lg hover:bg-laravel-600 transition text-sm font-medium"
                >
                  Donate
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else-if="!loading" class="text-center py-12">
        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
        </svg>
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No Active Campaigns</h3>
        <p class="text-gray-600 dark:text-gray-400">Check back later for donation opportunities.</p>
      </div>

      <!-- Recent Donations -->
      <div v-if="recentDonations.length > 0" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Recent Donations</h2>
        <div class="space-y-4">
          <div 
            v-for="donation in recentDonations" 
            :key="donation.id"
            class="flex items-center gap-4 p-4 rounded-lg bg-gray-50 dark:bg-gray-700/50"
          >
            <div class="w-10 h-10 rounded-full bg-laravel-100 dark:bg-laravel-900/30 flex items-center justify-center">
              <svg class="w-5 h-5 text-laravel-600" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
              </svg>
            </div>
            <div class="flex-1">
              <p class="font-medium text-gray-900 dark:text-white">{{ donation.donor_name }}</p>
              <p class="text-sm text-gray-500 dark:text-gray-400">
                {{ formatCurrency(donation.amount, donation.currency) }}
                <span v-if="donation.milestone"> to {{ donation.milestone.title }}</span>
              </p>
            </div>
            <span class="text-sm text-gray-500 dark:text-gray-400">{{ formatTimeAgo(donation.created_at) }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Donation Modal -->
    <div v-if="showDonationModal" class="fixed inset-0 z-50 overflow-y-auto">
      <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="closeDonationModal"></div>
        <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-xl max-w-md w-full p-6">
          <button 
            @click="closeDonationModal"
            class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>

          <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Make a Donation</h3>
          <p v-if="selectedMilestone" class="text-gray-600 dark:text-gray-400 mb-6">
            Supporting: {{ selectedMilestone.title }}
          </p>

          <form @submit.prevent="submitDonation">
            <!-- Amount Selection -->
            <div class="mb-6">
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Select Amount
              </label>
              <div class="grid grid-cols-3 gap-2 mb-3">
                <button 
                  v-for="preset in presetAmounts" 
                  :key="preset"
                  type="button"
                  @click="donationForm.amount = preset"
                  :class="[
                    'py-2 px-4 rounded-lg border-2 transition text-center font-medium',
                    donationForm.amount === preset 
                      ? 'border-laravel-500 bg-laravel-50 dark:bg-laravel-900/20 text-laravel-600 dark:text-laravel-400' 
                      : 'border-gray-200 dark:border-gray-600 hover:border-laravel-300'
                  ]"
                >
                  ${{ preset }}
                </button>
              </div>
              <input 
                v-model.number="donationForm.amount"
                type="number"
                min="1"
                step="0.01"
                placeholder="Or enter custom amount"
                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 focus:ring-2 focus:ring-laravel-500 focus:border-laravel-500"
              >
            </div>

            <!-- Payment Method -->
            <div class="mb-6">
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Payment Method
              </label>
              <div class="space-y-2">
                <label 
                  v-for="method in paymentMethods" 
                  :key="method.value"
                  class="flex items-center p-3 border rounded-lg cursor-pointer transition"
                  :class="donationForm.payment_method === method.value 
                    ? 'border-laravel-500 bg-laravel-50 dark:bg-laravel-900/20' 
                    : 'border-gray-200 dark:border-gray-600 hover:border-laravel-300'"
                >
                  <input 
                    type="radio" 
                    :value="method.value" 
                    v-model="donationForm.payment_method"
                    class="mr-3"
                  >
                  <span class="font-medium text-gray-900 dark:text-white">{{ method.label }}</span>
                </label>
              </div>
            </div>

            <!-- Donor Info (for guests) -->
            <div v-if="!authStore.isAuthenticated" class="mb-6 space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                  Your Name
                </label>
                <input 
                  v-model="donationForm.donor_name"
                  type="text"
                  placeholder="John Doe"
                  class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 focus:ring-2 focus:ring-laravel-500"
                >
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                  Email
                </label>
                <input 
                  v-model="donationForm.donor_email"
                  type="email"
                  placeholder="john@example.com"
                  class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 focus:ring-2 focus:ring-laravel-500"
                >
              </div>
            </div>

            <!-- Message -->
            <div class="mb-6">
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Message (Optional)
              </label>
              <textarea 
                v-model="donationForm.message"
                rows="2"
                placeholder="Leave a message of support..."
                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 focus:ring-2 focus:ring-laravel-500"
              ></textarea>
            </div>

            <!-- Anonymous -->
            <div class="mb-6">
              <label class="flex items-center gap-2 cursor-pointer">
                <input 
                  type="checkbox" 
                  v-model="donationForm.is_anonymous"
                  class="rounded border-gray-300 text-laravel-600 focus:ring-laravel-500"
                >
                <span class="text-sm text-gray-700 dark:text-gray-300">Make this donation anonymous</span>
              </label>
            </div>

            <!-- Submit -->
            <button 
              type="submit"
              :disabled="!donationForm.amount || donationForm.amount < 1 || processing"
              class="w-full py-3 bg-laravel-500 text-white font-bold rounded-lg hover:bg-laravel-600 transition disabled:opacity-50 disabled:cursor-not-allowed"
            >
              {{ processing ? 'Processing...' : `Donate $${donationForm.amount || 0}` }}
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import axios from 'axios'

const authStore = useAuthStore()

const loading = ref(true)
const milestones = ref([])
const featuredMilestone = ref(null)
const recentDonations = ref([])
const showDonationModal = ref(false)
const selectedMilestone = ref(null)
const processing = ref(false)

const presetAmounts = [10, 25, 50, 100, 250, 500]
const paymentMethods = [
  { value: 'paypal', label: 'PayPal' },
  { value: 'stripe', label: 'Credit Card (Stripe)' },
  { value: 'mpesa', label: 'M-Pesa' },
]

const donationForm = ref({
  amount: 25,
  payment_method: 'paypal',
  donor_name: '',
  donor_email: '',
  message: '',
  is_anonymous: false,
})

const fetchMilestones = async () => {
  try {
    const response = await axios.get('/api/v1/donations/milestones')
    milestones.value = response.data.data.filter(m => !m.is_featured)
    featuredMilestone.value = response.data.data.find(m => m.is_featured) || milestones.value[0]
  } catch (error) {
    console.error('Failed to fetch milestones:', error)
  } finally {
    loading.value = false
  }
}

const openDonationModal = (milestone) => {
  selectedMilestone.value = milestone
  showDonationModal.value = true
}

const closeDonationModal = () => {
  showDonationModal.value = false
  selectedMilestone.value = null
}

const submitDonation = async () => {
  if (!donationForm.value.amount || donationForm.value.amount < 1) return

  processing.value = true
  try {
    const response = await axios.post('/api/v1/donations/initialize', {
      milestone_id: selectedMilestone.value?.id,
      amount: donationForm.value.amount,
      payment_method: donationForm.value.payment_method,
      donor_name: donationForm.value.donor_name,
      donor_email: donationForm.value.donor_email,
      message: donationForm.value.message,
      is_anonymous: donationForm.value.is_anonymous,
    })

    const { payment } = response.data.data

    // Redirect to payment gateway
    if (payment.checkout_url) {
      window.location.href = payment.checkout_url
    } else if (payment.method === 'stripe') {
      // Handle Stripe payment
      console.log('Stripe payment initialized:', payment)
      // You would integrate Stripe.js here
    } else if (payment.method === 'mpesa') {
      // Handle M-Pesa payment
      console.log('M-Pesa payment initialized:', payment)
      // Show M-Pesa prompt
    }
  } catch (error) {
    console.error('Failed to initialize donation:', error)
  } finally {
    processing.value = false
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
    month: 'short',
    day: 'numeric',
    year: 'numeric',
  })
}

const formatTimeAgo = (date) => {
  const seconds = Math.floor((new Date() - new Date(date)) / 1000)
  const intervals = {
    year: 31536000,
    month: 2592000,
    week: 604800,
    day: 86400,
    hour: 3600,
    minute: 60,
  }
  for (const [unit, secondsInUnit] of Object.entries(intervals)) {
    const interval = Math.floor(seconds / secondsInUnit)
    if (interval >= 1) {
      return `${interval} ${unit}${interval > 1 ? 's' : ''} ago`
    }
  }
  return 'Just now'
}

onMounted(() => {
  fetchMilestones()
})
</script>
