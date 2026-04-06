<template>
  <div id="app">
    <nav class="bg-blue-600 text-white p-4 shadow">
      <div class="max-w-6xl mx-auto flex justify-between items-center">
        <router-link to="/" class="text-2xl font-bold hover:opacity-80">
          Open Peer Review System
        </router-link>
        <div class="space-x-4">
          <router-link to="/articles" class="hover:bg-blue-700 px-3 py-2 rounded">
            Articles
          </router-link>
          <router-link v-if="!isAuthenticated" to="/login" class="hover:bg-blue-700 px-3 py-2 rounded">
            Login
          </router-link>
          <router-link v-if="!isAuthenticated" to="/register" class="hover:bg-blue-700 px-3 py-2 rounded">
            Register
          </router-link>
          <router-link v-if="isAuthenticated" to="/dashboard" class="hover:bg-blue-700 px-3 py-2 rounded">
            Dashboard
          </router-link>
          <router-link v-if="isAuthenticated" to="/reviewer-dashboard" class="hover:bg-blue-700 px-3 py-2 rounded">
            Reviews
          </router-link>
          <router-link v-if="isEditor" to="/editor-dashboard" class="hover:bg-blue-700 px-3 py-2 rounded bg-blue-800">
            Editor
          </router-link>
          <router-link v-if="isAuthenticated" to="/submit" class="hover:bg-blue-700 px-3 py-2 rounded">
            Submit
          </router-link>
          <button v-if="isAuthenticated" @click="logout" class="hover:bg-blue-700 px-3 py-2 rounded">
            Logout
          </button>
        </div>
      </div>
    </nav>
    <main class="p-4">
      <router-view />
    </main>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/authStore'

const router = useRouter()
const authStore = useAuthStore()

const isAuthenticated = computed(() => authStore.isAuthenticated)
const isEditor = computed(() => authStore.isAuthenticated && authStore.user?.is_editor)

const logout = () => {
  authStore.logout()
  router.push('/')
}
</script>

<style scoped>
</style>
