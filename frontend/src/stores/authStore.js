import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

export const useAuthStore = defineStore('auth', () => {
  const user = ref(null)
  const token = ref(localStorage.getItem('auth_token') || null)
  const isAuthenticated = computed(() => !!token.value && !!user.value)

  const setUser = (userData, authToken) => {
    user.value = userData
    token.value = authToken
    localStorage.setItem('auth_token', authToken)
  }

  const login = async (email, password) => {
    // API call will go here
    console.log('Login:', email)
  }

  const register = async (name, email, password, affiliation) => {
    // API call will go here
    console.log('Register:', { name, email, affiliation })
  }

  const logout = () => {
    user.value = null
    token.value = null
    localStorage.removeItem('auth_token')
  }

  return { user, token, isAuthenticated, setUser, login, register, logout }
})
