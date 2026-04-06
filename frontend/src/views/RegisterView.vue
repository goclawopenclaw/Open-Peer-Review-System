<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50">
    <div class="max-w-md w-full bg-white rounded-lg shadow p-8">
      <h2 class="text-3xl font-bold mb-2 text-center">Create Account</h2>
      <p class="text-center text-gray-600 mb-6">Join the peer review community</p>
      
      <form @submit.prevent="handleRegister" class="space-y-4">
        <!-- Full Name -->
        <div>
          <label class="block text-sm font-medium text-gray-700">Full Name *</label>
          <input 
            v-model="form.name" 
            type="text" 
            placeholder="John Doe"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            required 
          />
          <p v-if="errors.name" class="text-red-600 text-xs mt-1">{{ errors.name[0] }}</p>
        </div>

        <!-- Email -->
        <div>
          <label class="block text-sm font-medium text-gray-700">Email *</label>
          <input 
            v-model="form.email" 
            type="email" 
            placeholder="you@example.com"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            required 
          />
          <p v-if="errors.email" class="text-red-600 text-xs mt-1">{{ errors.email[0] }}</p>
        </div>

        <!-- Affiliation -->
        <div>
          <label class="block text-sm font-medium text-gray-700">Affiliation (optional)</label>
          <input 
            v-model="form.affiliation" 
            type="text" 
            placeholder="University / Institute"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
          />
        </div>

        <!-- Password -->
        <div>
          <label class="block text-sm font-medium text-gray-700">Password *</label>
          <input 
            v-model="form.password" 
            type="password" 
            placeholder="Min 8 characters"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            required 
          />
          <p v-if="errors.password" class="text-red-600 text-xs mt-1">{{ errors.password[0] }}</p>
        </div>

        <!-- Confirm Password -->
        <div>
          <label class="block text-sm font-medium text-gray-700">Confirm Password *</label>
          <input 
            v-model="form.password_confirmation" 
            type="password" 
            placeholder="Confirm password"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            required 
          />
          <p v-if="errors.password_confirmation" class="text-red-600 text-xs mt-1">{{ errors.password_confirmation[0] }}</p>
        </div>

        <!-- Terms -->
        <div class="flex items-start">
          <input 
            v-model="form.agreeTerms" 
            type="checkbox" 
            id="terms"
            class="mt-1"
            required
          />
          <label for="terms" class="ml-2 text-sm text-gray-600">
            I agree to the <a href="#" class="text-blue-600 hover:underline">Terms of Service</a> and 
            <a href="#" class="text-blue-600 hover:underline">Privacy Policy</a> *
          </label>
        </div>

        <!-- Error message -->
        <div v-if="error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
          {{ error }}
        </div>

        <!-- Submit button -->
        <button 
          type="submit" 
          :disabled="loading"
          class="w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700 disabled:opacity-50 font-semibold"
        >
          {{ loading ? 'Creating account...' : 'Create Account' }}
        </button>
      </form>

      <!-- Login link -->
      <p class="mt-6 text-center text-sm text-gray-600">
        Already have an account? 
        <router-link to="/login" class="text-blue-600 hover:underline font-semibold">Login</router-link>
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/authStore'
import authService from '@/services/authService'

const router = useRouter()
const authStore = useAuthStore()

const form = ref({
  name: '',
  email: '',
  affiliation: '',
  password: '',
  password_confirmation: '',
  agreeTerms: false,
})

const loading = ref(false)
const error = ref(null)
const errors = ref({})

const handleRegister = async () => {
  loading.value = true
  error.value = null
  errors.value = {}

  try {
    const response = await authService.register(
      form.value.name,
      form.value.email,
      form.value.password,
      form.value.affiliation
    )

    localStorage.setItem('auth_token', response.data.token)
    authStore.user = response.data.user
    authStore.token = response.data.token

    router.push('/dashboard')
  } catch (err) {
    if (err.response?.status === 422) {
      errors.value = err.response.data.errors || {}
    }
    error.value = err.response?.data?.message || 'Registration failed. Please try again.'
  } finally {
    loading.value = false
  }
}
</script>
