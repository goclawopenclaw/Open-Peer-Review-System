<template>
  <div id="app">
    <nav class="bg-blue-600 text-white p-4">
      <div class="max-w-6xl mx-auto flex justify-between items-center">
        <h1 class="text-2xl font-bold">Open Peer Review System</h1>
        <div class="space-x-4">
          <router-link to="/" class="hover:bg-blue-700 px-3 py-2 rounded">Home</router-link>
          <router-link to="/dashboard" class="hover:bg-blue-700 px-3 py-2 rounded">Dashboard</router-link>
        </div>
      </div>
    </nav>
    <main class="p-4">
      <router-view />
    </main>
  </div>
</template>

<script setup>
</script>

<style scoped>
</style>
