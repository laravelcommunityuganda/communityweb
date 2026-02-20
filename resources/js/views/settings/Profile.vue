<template>
  <div class="bg-white rounded-xl shadow-sm p-6">
    <h2 class="text-xl font-bold text-gray-900 mb-6">Profile Settings</h2>

    <form @submit.prevent="submit">
      <div class="space-y-6">
        <!-- Avatar -->
        <div class="flex items-center gap-6">
          <BaseAvatar
            :src="form.avatar"
            :name="form.name"
            size="xl"
          />
          <div>
            <input
              type="file"
              ref="avatarInput"
              @change="handleAvatarChange"
              accept="image/*"
              class="hidden"
            />
            <BaseButton type="button" @click="$refs.avatarInput.click()" variant="secondary">
              Change Avatar
            </BaseButton>
            <p class="text-sm text-gray-500 mt-2">JPG, PNG or GIF. Max 2MB.</p>
          </div>
        </div>

        <!-- Name -->
        <div class="grid grid-cols-2 gap-4">
          <BaseInput
            id="name"
            label="Full Name"
            v-model="form.name"
            :error="form.errors.name"
            required
          />
          <BaseInput
            id="username"
            label="Username"
            v-model="form.username"
            :error="form.errors.username"
            required
          />
        </div>

        <!-- Bio -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Bio</label>
          <textarea
            v-model="form.bio"
            rows="4"
            class="w-full rounded-lg border-gray-300 focus:ring-primary-500 focus:border-primary-500"
            placeholder="Tell us about yourself..."
          ></textarea>
        </div>

        <!-- Location -->
        <BaseInput
          id="location"
          label="Location"
          v-model="form.location"
          placeholder="e.g. Kampala, Uganda"
        />

        <!-- Skills -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Skills</label>
          <div class="flex flex-wrap gap-2 mb-2">
            <span
              v-for="skill in form.skills"
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

        <!-- Links -->
        <div class="grid grid-cols-2 gap-4">
          <BaseInput
            id="website"
            label="Website"
            v-model="form.website"
            placeholder="https://yourwebsite.com"
          />
          <BaseInput
            id="github"
            label="GitHub Username"
            v-model="form.github"
            placeholder="username"
          />
        </div>

        <!-- Submit -->
        <div class="flex justify-end pt-4 border-t border-gray-100">
          <BaseButton type="submit" :loading="form.processing">
            Save Changes
          </BaseButton>
        </div>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/api'
import { useAuthStore } from '@/stores/auth'
import { useToastStore } from '@/stores/toast'

const authStore = useAuthStore()
const toast = useToastStore()

const skillInput = ref('')

const form = ref({
  name: '',
  username: '',
  bio: '',
  location: '',
  skills: [],
  website: '',
  github: '',
  avatar: '',
  errors: {},
  processing: false
})

const fetchProfile = async () => {
  try {
    const response = await api.get('/user/profile')
    const user = response.data.data || response.data
    form.value.name = user.name
    form.value.username = user.username
    form.value.bio = user.bio
    form.value.location = user.location
    form.value.skills = user.skills || []
    form.value.website = user.website
    form.value.github = user.github
    form.value.avatar = user.avatar
  } catch (error) {
    console.error('Failed to fetch profile:', error)
  }
}

const handleAvatarChange = async (event) => {
  const file = event.target.files[0]
  if (!file) return
  
  const formData = new FormData()
  formData.append('avatar', file)
  
  try {
    const response = await api.post('/user/avatar', formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })
    form.value.avatar = response.data.url
    toast.success('Avatar updated!')
  } catch (error) {
    toast.error('Failed to upload avatar')
  }
}

const addSkill = () => {
  const skill = skillInput.value.trim()
  if (skill && !form.value.skills.includes(skill)) {
    form.value.skills.push(skill)
  }
  skillInput.value = ''
}

const removeSkill = (skill) => {
  form.value.skills = form.value.skills.filter(s => s !== skill)
}

const submit = async () => {
  try {
    form.value.processing = true
    form.value.errors = {}
    
    await api.put('/user/profile', form.value)
    toast.success('Profile updated!')
    authStore.fetchUser()
  } catch (error) {
    if (error.response?.data?.errors) {
      form.value.errors = error.response.data.errors
    } else {
      toast.error('Failed to update profile')
    }
  } finally {
    form.value.processing = false
  }
}

onMounted(() => {
  fetchProfile()
})
</script>
