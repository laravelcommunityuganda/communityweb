import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '@/api'

export const useAuthStore = defineStore('auth', () => {
  const user = ref(null)
  const token = ref(localStorage.getItem('token'))

  const isAuthenticated = computed(() => !!token.value && !!user.value)
  
  const isAdmin = computed(() => {
    // Check if user has admin role from Spatie roles (roles is array of names)
    if (user.value?.roles?.includes('admin')) {
      return true
    }
    // Check if user has admin role from direct role column
    if (user.value?.role === 'admin') {
      return true
    }
    return false
  })

  const isModerator = computed(() => {
    // Check if user has moderator or admin role from Spatie roles (roles is array of names)
    if (user.value?.roles?.some(r => r === 'moderator' || r === 'admin')) {
      return true
    }
    // Check if user has moderator or admin role from direct role column
    if (user.value?.role === 'moderator' || user.value?.role === 'admin') {
      return true
    }
    return false
  })

  const login = async (credentials) => {
    try {
      const response = await api.post('/auth/login', credentials)
      token.value = response.data.token
      user.value = response.data.user
      localStorage.setItem('token', response.data.token)
      return response
    } catch (error) {
      throw error
    }
  }

  const register = async (data) => {
    try {
      const response = await api.post('/auth/register', data)
      token.value = response.data.token
      user.value = response.data.user
      localStorage.setItem('token', response.data.token)
      return response
    } catch (error) {
      throw error
    }
  }

  const logout = async () => {
    try {
      await api.post('/auth/logout')
    } catch (error) {
      console.error('Logout error:', error)
    } finally {
      token.value = null
      user.value = null
      localStorage.removeItem('token')
    }
  }

  const fetchUser = async () => {
    if (!token.value) return
    
    try {
      const response = await api.get('/auth/user')
      user.value = response.data.user || response.data
    } catch (error) {
      token.value = null
      user.value = null
      localStorage.removeItem('token')
    }
  }

  const updateUser = (data) => {
    user.value = { ...user.value, ...data }
  }

  return {
    user,
    token,
    isAuthenticated,
    isAdmin,
    isModerator,
    login,
    register,
    logout,
    fetchUser,
    updateUser
  }
})
