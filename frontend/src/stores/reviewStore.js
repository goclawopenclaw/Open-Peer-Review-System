import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '@/services/api'

export const useReviewStore = defineStore('review', () => {
  const assignments = ref([])
  const currentReview = ref(null)
  const loading = ref(false)
  const error = ref(null)

  const fetchPendingAssignments = async () => {
    loading.value = true
    try {
      const response = await api.get('/reviews/pending')
      assignments.value = response.data.data || []
      error.value = null
    } catch (err) {
      error.value = err.message
      throw err
    } finally {
      loading.value = false
    }
  }

  const fetchAssignment = async (assignmentId) => {
    loading.value = true
    try {
      const response = await api.get(`/review-assignments/${assignmentId}`)
      error.value = null
      return response.data
    } catch (err) {
      error.value = err.message
      throw err
    } finally {
      loading.value = false
    }
  }

  const acceptAssignment = async (assignmentId) => {
    loading.value = true
    try {
      const response = await api.post(`/review-assignments/${assignmentId}/accept`)
      const index = assignments.value.findIndex(a => a.id === assignmentId)
      if (index !== -1) {
        assignments.value[index] = response.data.assignment
      }
      error.value = null
      return response.data.assignment
    } catch (err) {
      error.value = err.message
      throw err
    } finally {
      loading.value = false
    }
  }

  const declineAssignment = async (assignmentId, reason = null) => {
    loading.value = true
    try {
      const response = await api.post(`/review-assignments/${assignmentId}/decline`, {
        reason: reason,
      })
      assignments.value = assignments.value.filter(a => a.id !== assignmentId)
      error.value = null
      return response.data.assignment
    } catch (err) {
      error.value = err.message
      throw err
    } finally {
      loading.value = false
    }
  }

  const submitReview = async (assignmentId, reviewData) => {
    loading.value = true
    try {
      const payload = {
        assignment_id: assignmentId,
        ...reviewData,
      }
      const response = await api.post('/reviews', payload)
      currentReview.value = response.data.review
      error.value = null
      return response.data.review
    } catch (err) {
      error.value = err.message
      throw err
    } finally {
      loading.value = false
    }
  }

  const fetchReview = async (reviewId) => {
    loading.value = true
    try {
      const response = await api.get(`/reviews/${reviewId}`)
      currentReview.value = response.data.review
      error.value = null
      return response.data.review
    } catch (err) {
      error.value = err.message
      throw err
    } finally {
      loading.value = false
    }
  }

  const fetchSubmissionReviews = async (submissionId) => {
    loading.value = true
    try {
      const response = await api.get(`/submissions/${submissionId}/reviews`)
      error.value = null
      return response.data.reviews
    } catch (err) {
      error.value = err.message
      throw err
    } finally {
      loading.value = false
    }
  }

  return {
    assignments,
    currentReview,
    loading,
    error,
    fetchPendingAssignments,
    fetchAssignment,
    acceptAssignment,
    declineAssignment,
    submitReview,
    fetchReview,
    fetchSubmissionReviews,
  }
})
