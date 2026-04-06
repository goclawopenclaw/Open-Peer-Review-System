<template>
  <div class="max-w-6xl mx-auto py-8">
    <router-link to="/editor-dashboard" class="text-blue-600 hover:underline mb-4 inline-block">
      ← Back to Dashboard
    </router-link>

    <div v-if="submission" class="grid grid-cols-3 gap-8">
      <!-- Article Summary (Left) -->
      <div class="col-span-1 bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-bold mb-4">Article</h2>
        <div class="space-y-3">
          <div>
            <h3 class="font-semibold text-lg">{{ submission.title }}</h3>
            <p class="text-sm text-gray-600 mt-1">By {{ submission.author.name }}</p>
          </div>

          <div>
            <h4 class="font-semibold text-sm text-gray-700 mb-1">Abstract</h4>
            <p class="text-sm text-gray-600 line-clamp-3">{{ submission.abstract }}</p>
          </div>

          <div v-if="submission.keywords?.length">
            <h4 class="font-semibold text-sm text-gray-700 mb-1">Keywords</h4>
            <div class="flex flex-wrap gap-1">
              <span
                v-for="keyword in submission.keywords.slice(0, 3)"
                :key="keyword"
                class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded"
              >
                {{ keyword }}
              </span>
              <span v-if="submission.keywords.length > 3" class="text-xs text-gray-600">
                +{{ submission.keywords.length - 3 }} more
              </span>
            </div>
          </div>

          <div class="pt-4 border-t">
            <h4 class="font-semibold text-sm text-gray-700 mb-2">Reviews Summary</h4>
            <div class="space-y-2 text-sm">
              <div>
                <span class="text-gray-600">Total Reviews:</span>
                <span class="font-semibold ml-2">{{ reviews.length }}</span>
              </div>
              <div>
                <span class="text-gray-600">Recommendations:</span>
                <div class="mt-1 space-y-1 text-xs">
                  <div>
                    Accept: <span class="font-semibold">{{ reviewCounts.accept }}</span>
                  </div>
                  <div>
                    Minor: <span class="font-semibold">{{ reviewCounts.minorRevisions }}</span>
                  </div>
                  <div>
                    Major: <span class="font-semibold">{{ reviewCounts.majorRevisions }}</span>
                  </div>
                  <div>
                    Reject: <span class="font-semibold">{{ reviewCounts.reject }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Reviews (Middle) -->
      <div class="col-span-1 bg-white rounded-lg shadow p-6 max-h-screen overflow-y-auto">
        <h2 class="text-xl font-bold mb-4">Peer Reviews</h2>
        <div class="space-y-3">
          <div
            v-for="review in reviews"
            :key="review.id"
            class="p-3 border rounded hover:bg-gray-50"
          >
            <div class="flex justify-between items-start mb-2">
              <span class="font-semibold text-sm">
                {{ review.is_signed ? review.reviewer.name : 'Anonymous' }}
              </span>
              <span :class="[
                'px-2 py-1 rounded text-xs font-semibold',
                {
                  'bg-green-100 text-green-800': review.recommendation === 'accept',
                  'bg-yellow-100 text-yellow-800': review.recommendation === 'minor_revisions',
                  'bg-orange-100 text-orange-800': review.recommendation === 'major_revisions',
                  'bg-red-100 text-red-800': review.recommendation === 'reject'
                }
              ]">
                {{ formatRecommendation(review.recommendation) }}
              </span>
            </div>
            <p class="text-xs text-gray-600">{{ review.summary?.substring(0, 100) }}...</p>
          </div>
        </div>
      </div>

      <!-- Decision Form (Right) -->
      <div class="col-span-1 bg-white rounded-lg shadow p-6">
        <h2 class="text-2xl font-bold mb-6">Make Decision</h2>

        <form @submit.prevent="submitDecision" class="space-y-6">
          <!-- Decision -->
          <div>
            <label class="block font-semibold mb-2">
              Editorial Decision <span class="text-red-600">*</span>
            </label>
            <div class="space-y-2">
              <label class="flex items-center">
                <input
                  v-model="decisionForm.decision"
                  type="radio"
                  value="accept"
                  class="mr-2"
                />
                <span>Accept - Ready for publication</span>
              </label>
              <label class="flex items-center">
                <input
                  v-model="decisionForm.decision"
                  type="radio"
                  value="minor_revisions"
                  class="mr-2"
                />
                <span>Minor Revisions - Request revisions and resubmit</span>
              </label>
              <label class="flex items-center">
                <input
                  v-model="decisionForm.decision"
                  type="radio"
                  value="major_revisions"
                  class="mr-2"
                />
                <span>Major Revisions - Substantial work needed, re-review</span>
              </label>
              <label class="flex items-center">
                <input
                  v-model="decisionForm.decision"
                  type="radio"
                  value="reject"
                  class="mr-2"
                />
                <span>Reject - Not suitable</span>
              </label>
            </div>
          </div>

          <!-- Decision Letter -->
          <div>
            <label class="block font-semibold mb-2">
              Decision Letter <span class="text-red-600">*</span>
            </label>
            <textarea
              v-model="decisionForm.decision_letter"
              class="w-full px-3 py-2 border border-gray-300 rounded-md h-32 focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="Provide a summary of the reviews and your editorial decision..."
              required
            ></textarea>
          </div>

          <!-- Revision Deadline (for revisions) -->
          <div v-if="decisionForm.decision && decisionForm.decision !== 'accept' && decisionForm.decision !== 'reject'">
            <label class="block font-semibold mb-2">
              Revision Deadline <span class="text-red-600">*</span>
            </label>
            <input
              v-model="decisionForm.revision_deadline"
              type="date"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              required
            />
            <p class="text-xs text-gray-500 mt-1">Authors have until this date to submit revisions</p>
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
              class="flex-1 px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 disabled:opacity-50 font-semibold"
            >
              {{ submitting ? 'Sending...' : 'Send Decision' }}
            </button>
            <button
              type="button"
              @click="$router.back()"
              class="flex-1 px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300"
            >
              Cancel
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Loading state -->
    <div v-if="loading" class="text-center py-12">
      <p class="text-gray-600">Loading submission...</p>
    </div>

    <!-- Error state -->
    <div v-if="error && !submission" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
      {{ error }}
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useEditorStore } from '@/stores/editorStore'

const route = useRoute()
const router = useRouter()
const editorStore = useEditorStore()

const submissionId = route.params.id

const submission = ref(null)
const reviews = ref([])
const loading = ref(false)
const submitting = ref(false)
const error = ref(null)

const decisionForm = ref({
  decision: '',
  decision_letter: '',
  revision_deadline: '',
})

const reviewCounts = computed(() => ({
  accept: reviews.value.filter(r => r.recommendation === 'accept').length,
  minorRevisions: reviews.value.filter(r => r.recommendation === 'minor_revisions').length,
  majorRevisions: reviews.value.filter(r => r.recommendation === 'major_revisions').length,
  reject: reviews.value.filter(r => r.recommendation === 'reject').length,
}))

const formatRecommendation = (rec) => {
  const map = {
    accept: 'Accept',
    minor_revisions: 'Minor',
    major_revisions: 'Major',
    reject: 'Reject',
  }
  return map[rec] || rec
}

const submitDecision = async () => {
  if (!decisionForm.value.decision || !decisionForm.value.decision_letter) {
    error.value = 'Please fill in all required fields'
    return
  }

  submitting.value = true
  error.value = null

  try {
    await editorStore.makeDecision(submissionId, decisionForm.value)
    router.push('/editor-dashboard')
  } catch (err) {
    error.value = 'Failed to submit decision. Please try again.'
  } finally {
    submitting.value = false
  }
}

onMounted(async () => {
  loading.value = true
  try {
    const data = await editorStore.fetchSubmissionWithReviews(submissionId)
    submission.value = data.submission
    reviews.value = data.reviews || []
    error.value = null
  } catch (err) {
    error.value = 'Failed to load submission'
  } finally {
    loading.value = false
  }
})
</script>
