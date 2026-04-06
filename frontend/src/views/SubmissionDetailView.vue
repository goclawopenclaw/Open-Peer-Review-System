<template>
  <div class="max-w-4xl mx-auto py-8">
    <div v-if="submission" class="bg-white rounded-lg shadow p-8">
      <h1 class="text-4xl font-bold mb-4">{{ submission.title }}</h1>
      
      <div class="mb-6 pb-6 border-b">
        <p class="text-gray-600 mb-4">
          <strong>Status:</strong> <span class="capitalize px-3 py-1 rounded-full text-sm" :class="{
            'bg-yellow-100 text-yellow-800': submission.status === 'draft',
            'bg-blue-100 text-blue-800': submission.status === 'submitted',
            'bg-purple-100 text-purple-800': submission.status === 'under_review',
            'bg-green-100 text-green-800': submission.status === 'published',
          }">{{ submission.status }}</span>
        </p>
      </div>

      <div class="space-y-6">
        <div>
          <h2 class="text-2xl font-bold mb-2">Abstract</h2>
          <p class="text-gray-700 leading-relaxed">{{ submission.abstract }}</p>
        </div>

        <div v-if="submission.keywords?.length">
          <h3 class="text-xl font-bold mb-2">Keywords</h3>
          <div class="flex flex-wrap gap-2">
            <span
              v-for="keyword in submission.keywords"
              :key="keyword"
              class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm"
            >
              {{ keyword }}
            </span>
          </div>
        </div>

        <div v-if="submission.research_field" class="grid grid-cols-2 gap-4">
          <div>
            <h3 class="font-semibold text-gray-700">Research Field</h3>
            <p class="text-gray-600">{{ submission.research_field }}</p>
          </div>
          <div v-if="submission.funding_source">
            <h3 class="font-semibold text-gray-700">Funding Source</h3>
            <p class="text-gray-600">{{ submission.funding_source }}</p>
          </div>
        </div>

        <div v-if="submission.competing_interests" class="bg-gray-50 p-4 rounded">
          <h3 class="font-semibold text-gray-700 mb-2">Competing Interests</h3>
          <p class="text-gray-600">{{ submission.competing_interests }}</p>
        </div>

        <div v-if="submission.data_availability" class="bg-gray-50 p-4 rounded">
          <h3 class="font-semibold text-gray-700 mb-2">Data Availability</h3>
          <p class="text-gray-600">{{ submission.data_availability }}</p>
        </div>
      </div>

      <div class="mt-8 space-x-4 flex">
        <router-link to="/submit" class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
          Submit Another
        </router-link>
        <router-link to="/dashboard" class="px-6 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">
          Back to Dashboard
        </router-link>
      </div>
    </div>

    <div v-else class="text-center py-12">
      <p class="text-gray-600 mb-4">Loading submission...</p>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { useSubmissionStore } from '@/stores/submissionStore'

const route = useRoute()
const submissionStore = useSubmissionStore()

const submission = ref(null)

onMounted(async () => {
  const id = route.params.id
  try {
    await submissionStore.fetchSubmission(id)
    submission.value = submissionStore.currentSubmission
  } catch (err) {
    console.error('Failed to load submission:', err)
  }
})
</script>
