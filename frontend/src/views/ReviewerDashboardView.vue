<template>
  <div class="max-w-6xl mx-auto py-8">
    <h1 class="text-4xl font-bold mb-8">Reviewer Dashboard</h1>

    <!-- Stats Cards -->
    <div class="grid grid-cols-3 gap-4 mb-8">
      <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-gray-600 text-sm font-semibold">Pending Reviews</h3>
        <p class="text-4xl font-bold text-blue-600">{{ stats.pending }}</p>
      </div>
      <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-gray-600 text-sm font-semibold">In Progress</h3>
        <p class="text-4xl font-bold text-yellow-600">{{ stats.inProgress }}</p>
      </div>
      <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-gray-600 text-sm font-semibold">Completed</h3>
        <p class="text-4xl font-bold text-green-600">{{ stats.completed }}</p>
      </div>
    </div>

    <!-- Tabs -->
    <div class="mb-6 flex space-x-4 border-b border-gray-200">
      <button
        @click="activeTab = 'pending'"
        :class="[
          'px-4 py-2 font-semibold border-b-2',
          activeTab === 'pending'
            ? 'border-blue-600 text-blue-600'
            : 'border-transparent text-gray-600 hover:text-gray-900'
        ]"
      >
        Pending ({{ stats.pending }})
      </button>
      <button
        @click="activeTab = 'inProgress'"
        :class="[
          'px-4 py-2 font-semibold border-b-2',
          activeTab === 'inProgress'
            ? 'border-blue-600 text-blue-600'
            : 'border-transparent text-gray-600 hover:text-gray-900'
        ]"
      >
        In Progress ({{ stats.inProgress }})
      </button>
      <button
        @click="activeTab = 'completed'"
        :class="[
          'px-4 py-2 font-semibold border-b-2',
          activeTab === 'completed'
            ? 'border-blue-600 text-blue-600'
            : 'border-transparent text-gray-600 hover:text-gray-900'
        ]"
      >
        Completed ({{ stats.completed }})
      </button>
    </div>

    <!-- Pending Reviews -->
    <div v-if="activeTab === 'pending'" class="space-y-4">
      <div v-if="pendingReviews.length === 0" class="bg-white rounded-lg shadow p-6 text-center">
        <p class="text-gray-600">No pending reviews at the moment.</p>
      </div>
      <div
        v-for="assignment in pendingReviews"
        :key="assignment.id"
        class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition"
      >
        <div class="flex justify-between items-start mb-4">
          <div>
            <h3 class="text-xl font-bold">{{ assignment.submission.title }}</h3>
            <p class="text-gray-600 text-sm mt-1">
              Submitted by {{ assignment.submission.author.name }}
            </p>
            <p class="text-gray-500 text-sm mt-1">
              Field: {{ assignment.submission.research_field || 'N/A' }}
            </p>
          </div>
          <div class="text-right">
            <p class="text-sm text-gray-600">
              <strong>Deadline:</strong> {{ formatDate(assignment.deadline_at) }}
            </p>
            <p :class="[
              'text-sm font-semibold mt-1',
              daysUntilDeadline(assignment.deadline_at) <= 7 ? 'text-red-600' : 'text-green-600'
            ]">
              {{ daysUntilDeadline(assignment.deadline_at) }} days left
            </p>
          </div>
        </div>

        <p class="text-gray-700 mb-4 line-clamp-2">
          {{ assignment.submission.abstract }}
        </p>

        <div class="flex space-x-3">
          <router-link
            :to="`/review/${assignment.id}`"
            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
          >
            Start Review
          </router-link>
          <button
            @click="declineReview(assignment.id)"
            class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300"
          >
            Decline
          </button>
        </div>
      </div>
    </div>

    <!-- In Progress Reviews -->
    <div v-if="activeTab === 'inProgress'" class="space-y-4">
      <div v-if="inProgressReviews.length === 0" class="bg-white rounded-lg shadow p-6 text-center">
        <p class="text-gray-600">No reviews in progress.</p>
      </div>
      <div
        v-for="assignment in inProgressReviews"
        :key="assignment.id"
        class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition"
      >
        <div class="flex justify-between items-start mb-4">
          <div>
            <h3 class="text-xl font-bold">{{ assignment.submission.title }}</h3>
            <p class="text-gray-600 text-sm mt-1">
              Started: {{ formatDate(assignment.accepted_at) }}
            </p>
          </div>
          <p :class="[
            'text-sm font-semibold',
            daysUntilDeadline(assignment.deadline_at) <= 7 ? 'text-red-600' : 'text-green-600'
          ]">
            {{ daysUntilDeadline(assignment.deadline_at) }} days left
          </p>
        </div>

        <div class="flex space-x-3">
          <router-link
            :to="`/review/${assignment.id}`"
            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
          >
            Continue Review
          </router-link>
        </div>
      </div>
    </div>

    <!-- Completed Reviews -->
    <div v-if="activeTab === 'completed'" class="space-y-4">
      <div v-if="completedReviews.length === 0" class="bg-white rounded-lg shadow p-6 text-center">
        <p class="text-gray-600">No completed reviews yet.</p>
      </div>
      <div
        v-for="assignment in completedReviews"
        :key="assignment.id"
        class="bg-white rounded-lg shadow p-6"
      >
        <div class="flex justify-between items-start">
          <div>
            <h3 class="text-xl font-bold">{{ assignment.submission.title }}</h3>
            <p class="text-gray-600 text-sm mt-1">
              Submitted: {{ formatDate(assignment.submitted_at) }}
            </p>
          </div>
          <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-semibold">
            Submitted
          </span>
        </div>
      </div>
    </div>

    <!-- Loading state -->
    <div v-if="loading" class="text-center py-8">
      <p class="text-gray-600">Loading reviews...</p>
    </div>

    <!-- Error state -->
    <div v-if="error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
      {{ error }}
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useReviewStore } from '@/stores/reviewStore'

const reviewStore = useReviewStore()

const activeTab = ref('pending')
const loading = ref(false)
const error = ref(null)

const pendingReviews = computed(() =>
  reviewStore.assignments.filter(a => a.status === 'pending')
)

const inProgressReviews = computed(() =>
  reviewStore.assignments.filter(a => a.status === 'accepted')
)

const completedReviews = computed(() =>
  reviewStore.assignments.filter(a => a.status === 'submitted')
)

const stats = computed(() => ({
  pending: pendingReviews.value.length,
  inProgress: inProgressReviews.value.length,
  completed: completedReviews.value.length,
}))

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric',
  })
}

const daysUntilDeadline = (deadline) => {
  const now = new Date()
  const deadlineDate = new Date(deadline)
  const diff = deadlineDate - now
  return Math.ceil(diff / (1000 * 60 * 60 * 24))
}

const declineReview = async (assignmentId) => {
  if (!confirm('Are you sure you want to decline this review?')) return

  try {
    await reviewStore.declineAssignment(assignmentId)
    error.value = null
  } catch (err) {
    error.value = 'Failed to decline review'
  }
}

onMounted(async () => {
  loading.value = true
  try {
    await reviewStore.fetchPendingAssignments()
    error.value = null
  } catch (err) {
    error.value = 'Failed to load reviews'
  } finally {
    loading.value = false
  }
})
</script>
