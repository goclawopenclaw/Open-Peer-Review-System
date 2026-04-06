<template>
  <div class="max-w-6xl mx-auto py-8">
    <h1 class="text-4xl font-bold mb-2">Editorial Dashboard</h1>
    <p class="text-gray-600 mb-8">Manage submissions, invite reviewers, and make editorial decisions</p>

    <!-- Stats Cards -->
    <div class="grid grid-cols-5 gap-4 mb-8">
      <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-gray-600 text-sm font-semibold">Screening</h3>
        <p class="text-3xl font-bold text-blue-600">{{ stats.screening }}</p>
      </div>
      <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-gray-600 text-sm font-semibold">Under Review</h3>
        <p class="text-3xl font-bold text-yellow-600">{{ stats.underReview }}</p>
      </div>
      <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-gray-600 text-sm font-semibold">Decision Pending</h3>
        <p class="text-3xl font-bold text-orange-600">{{ stats.decisionPending }}</p>
      </div>
      <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-gray-600 text-sm font-semibold">Published</h3>
        <p class="text-3xl font-bold text-green-600">{{ stats.published }}</p>
      </div>
      <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-gray-600 text-sm font-semibold">Rejected</h3>
        <p class="text-3xl font-bold text-red-600">{{ stats.rejected }}</p>
      </div>
    </div>

    <!-- Tabs -->
    <div class="mb-6 flex space-x-4 border-b border-gray-200 overflow-x-auto">
      <button
        @click="activeTab = 'screening'"
        :class="[
          'px-4 py-2 font-semibold border-b-2 whitespace-nowrap',
          activeTab === 'screening'
            ? 'border-blue-600 text-blue-600'
            : 'border-transparent text-gray-600 hover:text-gray-900'
        ]"
      >
        Screening ({{ stats.screening }})
      </button>
      <button
        @click="activeTab = 'underReview'"
        :class="[
          'px-4 py-2 font-semibold border-b-2 whitespace-nowrap',
          activeTab === 'underReview'
            ? 'border-blue-600 text-blue-600'
            : 'border-transparent text-gray-600 hover:text-gray-900'
        ]"
      >
        Under Review ({{ stats.underReview }})
      </button>
      <button
        @click="activeTab = 'decision'"
        :class="[
          'px-4 py-2 font-semibold border-b-2 whitespace-nowrap',
          activeTab === 'decision'
            ? 'border-blue-600 text-blue-600'
            : 'border-transparent text-gray-600 hover:text-gray-900'
        ]"
      >
        Make Decision ({{ stats.decisionPending }})
      </button>
      <button
        @click="activeTab = 'published'"
        :class="[
          'px-4 py-2 font-semibold border-b-2 whitespace-nowrap',
          activeTab === 'published'
            ? 'border-blue-600 text-blue-600'
            : 'border-transparent text-gray-600 hover:text-gray-900'
        ]"
      >
        Published ({{ stats.published }})
      </button>
    </div>

    <!-- Screening Tab -->
    <div v-if="activeTab === 'screening'" class="space-y-4">
      <div v-if="screeningSubmissions.length === 0" class="bg-white rounded-lg shadow p-6 text-center">
        <p class="text-gray-600">No submissions in screening queue.</p>
      </div>
      <div
        v-for="submission in screeningSubmissions"
        :key="submission.id"
        class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition"
      >
        <div class="flex justify-between items-start mb-4">
          <div>
            <h3 class="text-xl font-bold">{{ submission.title }}</h3>
            <p class="text-gray-600 text-sm mt-1">By {{ submission.author.name }}</p>
            <p class="text-gray-500 text-xs mt-1">Submitted {{ formatDate(submission.received_at) }}</p>
          </div>
          <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-semibold">
            {{ submission.research_field || 'Unassigned' }}
          </span>
        </div>

        <p class="text-gray-700 mb-4 line-clamp-2">{{ submission.abstract }}</p>

        <div class="flex space-x-3">
          <router-link
            :to="`/submission/${submission.id}`"
            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm"
          >
            Review & Screen
          </router-link>
          <button
            @click="deskReject(submission.id)"
            class="px-4 py-2 bg-red-100 text-red-700 rounded hover:bg-red-200 text-sm"
          >
            Desk Reject
          </button>
        </div>
      </div>
    </div>

    <!-- Under Review Tab -->
    <div v-if="activeTab === 'underReview'" class="space-y-4">
      <div v-if="underReviewSubmissions.length === 0" class="bg-white rounded-lg shadow p-6 text-center">
        <p class="text-gray-600">No submissions currently under review.</p>
      </div>
      <div
        v-for="submission in underReviewSubmissions"
        :key="submission.id"
        class="bg-white rounded-lg shadow p-6"
      >
        <div class="flex justify-between items-start mb-4">
          <div>
            <h3 class="text-xl font-bold">{{ submission.title }}</h3>
            <p class="text-gray-600 text-sm mt-1">By {{ submission.author.name }}</p>
          </div>
          <span class="text-sm">
            <span class="font-semibold">{{ submission.reviews?.length || 0 }}</span> reviews
          </span>
        </div>

        <div class="flex space-x-3">
          <router-link
            :to="`/submission/${submission.id}`"
            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm"
          >
            View Reviews
          </router-link>
        </div>
      </div>
    </div>

    <!-- Decision Tab -->
    <div v-if="activeTab === 'decision'" class="space-y-4">
      <div v-if="decisionPendingSubmissions.length === 0" class="bg-white rounded-lg shadow p-6 text-center">
        <p class="text-gray-600">No submissions awaiting editorial decision.</p>
      </div>
      <div
        v-for="submission in decisionPendingSubmissions"
        :key="submission.id"
        class="bg-white rounded-lg shadow p-6"
      >
        <div class="flex justify-between items-start mb-4">
          <div>
            <h3 class="text-xl font-bold">{{ submission.title }}</h3>
            <p class="text-gray-600 text-sm mt-1">By {{ submission.author.name }}</p>
            <p class="text-gray-500 text-xs mt-2">
              Reviews: <span class="font-semibold">{{ submission.reviews?.length || 0 }}</span>
              • Avg recommendation: 
              <span class="font-semibold">{{ getAverageRecommendation(submission.reviews) }}</span>
            </p>
          </div>
        </div>

        <div class="flex space-x-3">
          <router-link
            :to="`/editorial-decision/${submission.id}`"
            class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 text-sm"
          >
            Make Decision
          </router-link>
        </div>
      </div>
    </div>

    <!-- Published Tab -->
    <div v-if="activeTab === 'published'" class="space-y-4">
      <div v-if="publishedSubmissions.length === 0" class="bg-white rounded-lg shadow p-6 text-center">
        <p class="text-gray-600">No published articles yet.</p>
      </div>
      <div
        v-for="submission in publishedSubmissions"
        :key="submission.id"
        class="bg-white rounded-lg shadow p-6"
      >
        <div class="flex justify-between items-start">
          <div>
            <h3 class="text-xl font-bold">{{ submission.title }}</h3>
            <p class="text-gray-600 text-sm mt-1">By {{ submission.author.name }}</p>
            <p class="text-gray-500 text-xs mt-2">
              Published {{ formatDate(submission.published_at) }} 
              <span v-if="submission.doi" class="ml-2 font-mono">DOI: {{ submission.doi }}</span>
            </p>
          </div>
          <router-link
            :to="`/article/${submission.id}`"
            class="text-blue-600 hover:underline text-sm"
          >
            View
          </router-link>
        </div>
      </div>
    </div>

    <!-- Loading and Error states -->
    <div v-if="loading" class="text-center py-8">
      <p class="text-gray-600">Loading submissions...</p>
    </div>

    <div v-if="error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
      {{ error }}
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useEditorStore } from '@/stores/editorStore'

const editorStore = useEditorStore()

const activeTab = ref('screening')
const loading = ref(false)
const error = ref(null)

const submissions = computed(() => editorStore.submissions)

const screeningSubmissions = computed(() =>
  submissions.value.filter(s => s.status === 'submitted' || s.status === 'screening')
)

const underReviewSubmissions = computed(() =>
  submissions.value.filter(s => s.status === 'under_review')
)

const decisionPendingSubmissions = computed(() =>
  submissions.value.filter(s => s.status === 'under_review' && s.reviews?.length > 0)
)

const publishedSubmissions = computed(() =>
  submissions.value.filter(s => s.status === 'published')
)

const stats = computed(() => ({
  screening: screeningSubmissions.value.length,
  underReview: underReviewSubmissions.value.length,
  decisionPending: decisionPendingSubmissions.value.length,
  published: publishedSubmissions.value.length,
  rejected: submissions.value.filter(s => s.status === 'rejected').length,
}))

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric',
  })
}

const getAverageRecommendation = (reviews) => {
  if (!reviews?.length) return 'N/A'
  const map = { accept: 1, minor_revisions: 2, major_revisions: 3, reject: 4 }
  const avg = reviews.reduce((sum, r) => sum + (map[r.recommendation] || 2), 0) / reviews.length
  if (avg < 1.5) return 'Accept'
  if (avg < 2.5) return 'Minor Revisions'
  if (avg < 3.5) return 'Major Revisions'
  return 'Reject'
}

const deskReject = async (submissionId) => {
  if (!confirm('Are you sure you want to desk reject this submission?')) return

  try {
    await editorStore.deskReject(submissionId)
    error.value = null
  } catch (err) {
    error.value = 'Failed to desk reject submission'
  }
}

onMounted(async () => {
  loading.value = true
  try {
    await editorStore.fetchSubmissions()
    error.value = null
  } catch (err) {
    error.value = 'Failed to load submissions'
  } finally {
    loading.value = false
  }
})
</script>
