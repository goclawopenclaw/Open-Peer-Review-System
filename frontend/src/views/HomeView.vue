<template>
  <div class="max-w-6xl mx-auto">
    <h1 class="text-4xl font-bold mb-4">Welcome to Open Peer Review System</h1>
    <p class="text-lg text-gray-600 mb-8">
      A transparent, community-driven platform for peer-reviewed research. Modeled on Open Research Europe.
    </p>
    <div class="grid grid-cols-3 gap-4 mb-12">
      <router-link to="/articles" class="bg-purple-600 text-white p-6 rounded hover:bg-purple-700">
        <h2 class="text-2xl font-bold mb-2">Browse Articles</h2>
        <p>Read peer-reviewed research</p>
      </router-link>
      <router-link to="/submit" class="bg-blue-600 text-white p-6 rounded hover:bg-blue-700">
        <h2 class="text-2xl font-bold mb-2">Submit Article</h2>
        <p>Share your research</p>
      </router-link>
      <router-link to="/dashboard" class="bg-green-600 text-white p-6 rounded hover:bg-green-700">
        <h2 class="text-2xl font-bold mb-2">Dashboard</h2>
        <p>Your submissions & reviews</p>
      </router-link>
    </div>

    <!-- Features -->
    <div class="grid grid-cols-3 gap-4 mt-12">
      <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-bold mb-2">🔍 Transparent Reviews</h3>
        <p class="text-gray-600">All peer reviews are visible by default, signed by reviewers for accountability.</p>
      </div>
      <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-bold mb-2">👥 Community-Driven</h3>
        <p class="text-gray-600">Authors select reviewers, fostering dialogue and constructive feedback.</p>
      </div>
      <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-bold mb-2">📚 Open Access</h3>
        <p class="text-gray-600">All research is freely available. No paywalls. Science for everyone.</p>
      </div>
    </div>
  </div>
</template>
