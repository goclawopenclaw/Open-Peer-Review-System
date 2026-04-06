<template>
  <div class="max-w-6xl mx-auto py-8">
    <div class="grid grid-cols-3 gap-8">
      <!-- Article Viewer (Left) -->
      <div class="col-span-1 bg-white rounded-lg shadow p-6 max-h-screen overflow-y-auto">
        <h2 class="text-xl font-bold mb-4">Article</h2>
        <div v-if="submission" class="space-y-4">
          <div>
            <h3 class="font-semibold text-lg">{{ submission.title }}</h3>
            <p class="text-sm text-gray-600 mt-2">
              By {{ submission.author.name }}
            </p>
          </div>

          <div>
            <h4 class="font-semibold text-sm text-gray-700 mb-2">Abstract</h4>
            <p class="text-sm text-gray-600">{{ submission.abstract }}</p>
          </div>

          <div>
            <h4 class="font-semibold text-sm text-gray-700 mb-2">Keywords</h4>
            <div class="flex flex-wrap gap-2">
              <span
                v-for="keyword in submission.keywords"
                :key="keyword"
                class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded"
              >
                {{ keyword }}
              </span>
            </div>
          </div>

          <div>
            <h4 class="font-semibold text-sm text-gray-700 mb-2">Metadata</h4>
            <dl class="space-y-1 text-sm">
              <div v-if="submission.research_field" class="flex justify-between">
                <dt class="font-medium text-gray-600">Field:</dt>
                <dd class="text-gray-900">{{ submission.research_field }}</dd>
              </div>
              <div v-if="submission.funding_source" class="flex justify-between">
                <dt class="font-medium text-gray-600">Funding:</dt>
                <dd class="text-gray-900">{{ submission.funding_source }}</dd>
              </div>
              <div v-if="submission.doi" class="flex justify-between">
                <dt class="font-medium text-gray-600">DOI:</dt>
                <dd class="text-gray-900 font-mono text-xs">{{ submission.doi }}</dd>
              </div>
            </dl>
          </div>
        </div>

        <div v-if="loading" class="text-center py-4">
          <p class="text-gray-600">Loading article...</p>
        </div>
      </div>

      <!-- Review Form (Right) -->
      <div class="col-span-2 bg-white rounded-lg shadow p-6">
        <h2 class="text-2xl font-bold mb-6">Your Review</h2>

        <form @submit.prevent="submitReview" class="space-y-6">
          <!-- Summary -->
          <div>
            <label class="block font-semibold mb-2">
              Summary of the Work <span class="text-red-600">*</span>
            </label>
            <textarea
              v-model="reviewForm.summary"
              class="w-full px-3 py-2 border border-gray-300 rounded-md h-24 focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="200-300 words summarizing the key aspects of the article"
              required
            ></textarea>
            <p class="text-xs text-gray-500 mt-1">{{ reviewForm.summary.length }}/300</p>
          </div>

          <!-- Strengths -->
          <div>
            <label class="block font-semibold mb-2">
              Strengths <span class="text-red-600">*</span>
            </label>
            <textarea
              v-model="reviewForm.strengths"
              class="w-full px-3 py-2 border border-gray-300 rounded-md h-20 focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="What are the main strengths of this work?"
              required
            ></textarea>
          </div>

          <!-- Weaknesses -->
          <div>
            <label class="block font-semibold mb-2">
              Weaknesses <span class="text-red-600">*</span>
            </label>
            <textarea
              v-model="reviewForm.weaknesses"
              class="w-full px-3 py-2 border border-gray-300 rounded-md h-20 focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="What are the main weaknesses and areas for improvement?"
              required
            ></textarea>
          </div>

          <!-- Detailed Comments -->
          <div>
            <label class="block font-semibold mb-2">
              Detailed Comments <span class="text-red-600">*</span>
            </label>
            <textarea
              v-model="reviewForm.detailed_comments"
              class="w-full px-3 py-2 border border-gray-300 rounded-md h-24 focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="Provide specific, constructive feedback. Reference specific sections or figures if applicable."
              required
            ></textarea>
          </div>

          <!-- Recommendation -->
          <div>
            <label class="block font-semibold mb-2">
              Recommendation <span class="text-red-600">*</span>
            </label>
            <select
              v-model="reviewForm.recommendation"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              required
            >
              <option value="">Select a recommendation</option>
              <option value="accept">Accept - Ready for publication</option>
              <option value="minor_revisions">Minor Revisions - Accept with minor changes</option>
              <option value="major_revisions">Major Revisions - Substantial work needed</option>
              <option value="reject">Reject - Not suitable for publication</option>
            </select>
          </div>

          <!-- Confidence -->
          <div>
            <label class="block font-semibold mb-2">
              Confidence Level <span class="text-red-600">*</span>
            </label>
            <select
              v-model="reviewForm.confidence"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              required
            >
              <option value="">Select confidence level</option>
              <option value="high">High - I am confident in this review</option>
              <option value="medium">Medium - Reasonably confident</option>
              <option value="low">Low - Some uncertainty</option>
            </select>
          </div>

          <!-- Signed Review -->
          <div class="flex items-center">
            <input
              v-model="reviewForm.is_signed"
              type="checkbox"
              id="signed"
              class="mr-2"
            />
            <label for="signed" class="text-sm text-gray-700">
              Sign this review (your name will be visible to the author)
            </label>
          </div>

          <!-- Error message -->
          <div v-if="error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            {{ error }}
          </div>

          <!-- Submit buttons -->
          <div class="flex space-x-3">
            <button
              type="submit"
              :disabled="submitting"
              class="px-6 py-2 bg-green-600 text-white rounded hover:bg-green-700 disabled:opacity-50 font-semibold"
            >
              {{ submitting ? 'Submitting...' : 'Submit Review' }}
            </button>
            <button
              type="button"
              @click="saveDraft"
              class="px-6 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300"
            >
              Save Draft
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useReviewStore } from '@/stores/reviewStore'

const route = useRoute()
const router = useRouter()
const reviewStore = useReviewStore()

const assignmentId = route.params.id
const submission = ref(null)
const loading = ref(false)
const submitting = ref(false)
const error = ref(null)

const reviewForm = ref({
  summary: '',
  strengths: '',
  weaknesses: '',
  detailed_comments: '',
  recommendation: '',
  confidence: '',
  is_signed: true,
})

const submitReview = async () => {
  if (!reviewForm.value.recommendation || !reviewForm.value.confidence) {
    error.value = 'Please fill in all required fields'
    return
  }

  submitting.value = true
  error.value = null

  try {
    await reviewStore.submitReview(assignmentId, reviewForm.value)
    router.push('/reviewer-dashboard')
  } catch (err) {
    error.value = 'Failed to submit review. Please try again.'
  } finally {
    submitting.value = false
  }
}

const saveDraft = () => {
  localStorage.setItem(`review_draft_${assignmentId}`, JSON.stringify(reviewForm.value))
  alert('Draft saved locally')
}

onMounted(async () => {
  loading.value = true
  try {
    // Load submission details
    const assignment = await reviewStore.fetchAssignment(assignmentId)
    submission.value = assignment.submission

    // Load saved draft if exists
    const saved = localStorage.getItem(`review_draft_${assignmentId}`)
    if (saved) {
      reviewForm.value = JSON.parse(saved)
    }

    error.value = null
  } catch (err) {
    error.value = 'Failed to load article'
  } finally {
    loading.value = false
  }
})
</script>
