<template>
  <div class="min-h-screen bg-gray-50">
    <main class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="bg-white rounded-xl shadow-sm p-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Post a New Job</h1>

        <form @submit.prevent="submit">
          <div class="space-y-6">
            <!-- Job Title -->
            <BaseInput
              id="title"
              label="Job Title"
              v-model="form.title"
              :error="form.errors.title"
              placeholder="e.g. Senior Laravel Developer"
              required
            />

            <!-- Company -->
            <BaseInput
              id="company"
              label="Company Name"
              v-model="form.company"
              :error="form.errors.company"
              placeholder="Your company name"
              required
            />

            <!-- Job Type & Location -->
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                  Job Type <span class="text-red-500">*</span>
                </label>
                <select
                  v-model="form.type"
                  class="w-full rounded-lg border-gray-300 focus:ring-primary-500 focus:border-primary-500"
                >
                  <option value="">Select type</option>
                  <option value="full-time">Full-time</option>
                  <option value="part-time">Part-time</option>
                  <option value="contract">Contract</option>
                  <option value="freelance">Freelance</option>
                  <option value="internship">Internship</option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                <select
                  v-model="form.location"
                  class="w-full rounded-lg border-gray-300 focus:ring-primary-500 focus:border-primary-500"
                >
                  <option value="">Select location</option>
                  <option v-for="loc in locations" :key="loc" :value="loc">{{ loc }}</option>
                </select>
              </div>
            </div>

            <!-- Remote -->
            <div class="flex items-center gap-4">
              <label class="flex items-center">
                <input
                  type="checkbox"
                  v-model="form.remote"
                  class="rounded border-gray-300 text-primary-500 focus:ring-primary-500"
                />
                <span class="ml-2 text-sm text-gray-700">Remote work available</span>
              </label>
            </div>

            <!-- Salary Range -->
            <div class="grid grid-cols-2 gap-4">
              <BaseInput
                id="salary_min"
                label="Minimum Salary (UGX)"
                v-model="form.salary_min"
                type="number"
                placeholder="e.g. 1500000"
              />
              <BaseInput
                id="salary_max"
                label="Maximum Salary (UGX)"
                v-model="form.salary_max"
                type="number"
                placeholder="e.g. 2500000"
              />
            </div>

            <!-- Description -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                Job Description <span class="text-red-500">*</span>
              </label>
              <textarea
                v-model="form.description"
                rows="6"
                class="w-full rounded-lg border-gray-300 focus:ring-primary-500 focus:border-primary-500"
                placeholder="Describe the role, responsibilities, and what makes this opportunity exciting..."
              ></textarea>
              <p v-if="form.errors.description" class="mt-1 text-sm text-red-500">
                {{ form.errors.description }}
              </p>
            </div>

            <!-- Requirements -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Requirements</label>
              <textarea
                v-model="form.requirements"
                rows="4"
                class="w-full rounded-lg border-gray-300 focus:ring-primary-500 focus:border-primary-500"
                placeholder="List the skills, experience, and qualifications required..."
              ></textarea>
            </div>

            <!-- Skills -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Required Skills</label>
              <div class="flex flex-wrap gap-2 mb-2">
                <span
                  v-for="skill in selectedSkills"
                  :key="skill"
                  class="inline-flex items-center gap-1 px-3 py-1 bg-primary-100 text-primary-700 text-sm rounded-full"
                >
                  {{ skill }}
                  <button type="button" @click="removeSkill(skill)" class="hover:text-primary-900">×</button>
                </span>
              </div>
              <input
                v-model="skillInput"
                @keydown.enter.prevent="addSkill"
                type="text"
                class="w-full rounded-lg border-gray-300 focus:ring-primary-500 focus:border-primary-500"
                placeholder="Type a skill and press Enter"
              />
            </div>

            <!-- Deadline -->
            <BaseInput
              id="deadline"
              label="Application Deadline"
              v-model="form.deadline"
              type="date"
            />

            <!-- Submit -->
            <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-100">
              <router-link to="/jobs" class="px-4 py-2 text-gray-700 hover:text-gray-900">
                Cancel
              </router-link>
              <BaseButton type="submit" :loading="form.processing">
                Post Job
              </BaseButton>
            </div>
          </div>
        </form>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/api'
import { useToastStore } from '@/stores/toast'

const router = useRouter()
const toast = useToastStore()

const locations = ref(['Kampala', 'Entebbe', 'Jinja', 'Mbarara', 'Gulu', 'Remote'])
const selectedSkills = ref([])
const skillInput = ref('')

const form = ref({
  title: '',
  company: '',
  type: '',
  location: '',
  remote: false,
  salary_min: '',
  salary_max: '',
  description: '',
  requirements: '',
  skills: [],
  deadline: '',
  errors: {},
  processing: false
})

const addSkill = () => {
  const skill = skillInput.value.trim()
  if (skill && !selectedSkills.value.includes(skill)) {
    selectedSkills.value.push(skill)
  }
  skillInput.value = ''
}

const removeSkill = (skill) => {
  selectedSkills.value = selectedSkills.value.filter(s => s !== skill)
}

const submit = async () => {
  try {
    form.value.processing = true
    form.value.errors = {}
    
    const response = await api.post('/jobs', {
      ...form.value,
      skills: selectedSkills.value
    })
    
    toast.success('Job posted successfully!')
    router.push(`/jobs/${response.data.data.slug}`)
  } catch (error) {
    if (error.response?.data?.errors) {
      form.value.errors = error.response.data.errors
    } else {
      toast.error('Failed to post job')
    }
  } finally {
    form.value.processing = false
  }
}
</script>
