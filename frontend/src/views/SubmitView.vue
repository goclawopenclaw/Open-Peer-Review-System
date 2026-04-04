<template>
  <div class="max-w-4xl mx-auto py-8">
    <h1 class="text-4xl font-bold mb-8">Submit Article</h1>

    <form @submit.prevent="handleSubmit" class="bg-white rounded-lg shadow p-8 space-y-6">
      <!-- Title -->
      <div>
        <label class="block text-lg font-semibold mb-2">Article Title *</label>
        <input 
          v-model="form.title" 
          type="text" 
          class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
          required 
        />
      </div>

      <!-- Abstract -->
      <div>
        <label class="block text-lg font-semibold mb-2">Abstract (150-300 words) *</label>
        <textarea 
          v-model="form.abstract" 
          class="w-full px-4 py-2 border rounded h-32 focus:outline-none focus:ring-2 focus:ring-blue-500"
          required
        ></textarea>
        <p class="text-sm text-gray-500 mt-1">{{ form.abstract.length }}/5000 characters</p>
      </div>

      <!-- Keywords -->
      <div>
        <label class="block text-lg font-semibold mb-2">Keywords (comma-separated)</label>
        <input 
          v-model="form.keywordString" 
          type="text" 
          placeholder="e.g., neuroscience, machine learning, deep learning"
          class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
        />
      </div>

      <!-- Research Field -->
      <div>
        <label class="block text-lg font-semibold mb-2">Research Field</label>
        <select v-model="form.research_field" class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
          <option value="">Select a field</option>
          <option value="biology">Biology</option>
          <option value="medicine">Medicine</option>
          <option value="physics">Physics</option>
          <option value="chemistry">Chemistry</option>
          <option value="computer_science">Computer Science</option>
          <option value="other">Other</option>
        </select>
      </div>

      <!-- Funding -->
      <div>
        <label class="block text-lg font-semibold mb-2">Funding Source</label>
        <input 
          v-model="form.funding_source" 
          type="text" 
          class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
        />
      </div>

      <!-- Competing Interests -->
      <div>
        <label class="block text-lg font-semibold mb-2">Competing Interests</label>
        <textarea 
          v-model="form.competing_interests" 
          class="w-full px-4 py-2 border rounded h-24 focus:outline-none focus:ring-2 focus:ring-blue-500"
          placeholder="Declare any conflicts of interest"
        ></textarea>
      </div>

      <!-- Data Availability -->
      <div>
        <label class="block text-lg font-semibold mb-2">Data Availability Statement</label>
        <textarea 
          v-model="form.data_availability" 
          class="w-full px-4 py-2 border rounded h-24 focus:outline-none focus:ring-2 focus:ring-blue-500"
          placeholder="Describe how readers can access your data"
        ></textarea>
      </div>

      <!-- Suggested Reviewers -->
      <div>
        <label class="block text-lg font-semibold mb-2">Suggested Reviewers (3-5)</label>
        <div v-for="(reviewer, idx) in form.reviewers" :key="idx" class="mb-4 p-4 border rounded bg-gray-50 space-y-2">
          <input 
            v-model="reviewer.name" 
            type="text" 
            placeholder="Full name"
            class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
          />
          <input 
            v-model="reviewer.email" 
            type="email" 
            placeholder="Email address"
            class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
          />
          <input 
            v-model="reviewer.institution" 
            type="text" 
            placeholder="Institution"
            class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
          />
          <textarea 
            v-model="reviewer.rationale" 
            placeholder="Why is this person a good reviewer?"
            class="w-full px-3 py-2 border rounded h-16 focus:outline-none focus:ring-2 focus:ring-blue-500"
          ></textarea>
          <button 
            @click="removeReviewer(idx)" 
            type="button"
            class="text-red-600 hover:text-red-700 text-sm"
          >
            Remove
          </button>
        </div>
        <button 
          @click="addReviewer" 
          type="button"
          class="text-blue-600 hover:text-blue-700 font-semibold"
        >
          + Add Reviewer
        </button>
      </div>

      <!-- Error message -->
      <div v-if="error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
        {{ error }}
      </div>

      <!-- Submit button -->
      <button 
        type="submit" 
        :disabled="submitting"
        class="w-full px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 font-semibold"
      >
        {{ submitting ? 'Submitting...' : 'Submit Article' }}
      </button>
    </form>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useSubmissionStore } from '@/stores/submissionStore'

const router = useRouter()
const submissionStore = useSubmissionStore()

const form = ref({
  title: '',
  abstract: '',
  keywordString: '',
  research_field: '',
  funding_source: '',
  competing_interests: '',
  data_availability: '',
  reviewers: [
    { name: '', email: '', institution: '', rationale: '' },
    { name: '', email: '', institution: '', rationale: '' },
  ],
})

const submitting = ref(false)
const error = ref(null)

const addReviewer = () => {
  form.value.reviewers.push({ name: '', email: '', institution: '', rationale: '' })
}

const removeReviewer = (idx) => {
  form.value.reviewers.splice(idx, 1)
}

const handleSubmit = async () => {
  submitting.value = true
  error.value = null

  try {
    const payload = {
      title: form.value.title,
      abstract: form.value.abstract,
      keywords: form.value.keywordString.split(',').map(k => k.trim()).filter(Boolean),
      research_field: form.value.research_field,
      funding_source: form.value.funding_source,
      competing_interests: form.value.competing_interests,
      data_availability: form.value.data_availability,
      reviewer_suggestions: form.value.reviewers.filter(r => r.name && r.email),
    }

    const submission = await submissionStore.createSubmission(payload)
    router.push(`/submissions/${submission.id}`)
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to submit article'
  } finally {
    submitting.value = false
  }
}
</script>
