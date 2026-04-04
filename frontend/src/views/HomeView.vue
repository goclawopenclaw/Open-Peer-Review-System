<template>
  <div class="max-w-6xl mx-auto">
    <h1 class="text-4xl font-bold mb-4">Welcome to Open Peer Review System</h1>
    <p class="text-lg text-gray-600 mb-8">
      A transparent, ORE-aligned platform for peer review.
    </p>
    <div class="grid grid-cols-2 gap-4">
      <router-link to="/submit" class="bg-blue-600 text-white p-6 rounded hover:bg-blue-700">
        <h2 class="text-2xl font-bold mb-2">Submit Article</h2>
        <p>Start your peer review journey</p>
      </router-link>
      <router-link to="/dashboard" class="bg-green-600 text-white p-6 rounded hover:bg-green-700">
        <h2 class="text-2xl font-bold mb-2">Dashboard</h2>
        <p>View your submissions and reviews</p>
      </router-link>
    </div>
  </div>
</template>
