import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '@/services/api'

export const useEditorStore = defineStore('editor', () => {
  const submissions = ref([])
  const currentSubmission = ref(null)
  const loading = ref(false)
  const error = ref(null)

  const fetchSubmissions = async () => {
    loading.value = true
    try {
      const response = await api.get('/editor/submissions')
      submissions.value = response.data.submissions || []
      error.value = null
    } catch (err) {
      error.value = err.message
      throw err
    } finally {
      loading.value = false
    }
  }

  const fetchSubmissionWithReviews = async (submissionId) => {
    loading.value = true
    try {
      const response = await api.get(`/submissions/${submissionId}`)
      currentSubmission.value = response.data.submission
      error.value = null
      return response.data
    } catch (err) {
      error.value = err.message
      throw err
    } finally {
      loading.value = false
    }
  }

  const deskReject = async (submissionId) => {
    loading.value = true
    try {
      const response = await api.post(`/editor/submissions/${submissionId}/desk-reject`)
      submissions.value = submissions.value.filter(s => s.id !== submissionId)
      error.value = null
      return response.data
    } catch (err) {
      error.value = err.message
      throw err
    } finally {
      loading.value = false
    }
  }

  const inviteReviewers = async (submissionId, reviewerIds) => {
    loading.value = true
    try {
      const response = await api.post(`/editor/submissions/${submissionId}/invite-reviewers`, {
        reviewer_ids: reviewerIds,
      })
      error.value = null
      return response.data
    } catch (err) {
      error.value = err.message
      throw err
    } finally {
      loading.value = false
    }
  }

  const makeDecision = async (submissionId, decisionData) => {
    loading.value = true
    try {
      const response = await api.post(`/editor/submissions/${submissionId}/make-decision`, decisionData)
      
      // Update submission in list
      const index = submissions.value.findIndex(s => s.id === submissionId)
      if (index !== -1) {
        submissions.value[index] = response.data.submission
      }
      
      error.value = null
      return response.data
    } catch (err) {
      error.value = err.message
      throw err
    } finally {
      loading.value = false
    }
  }

  const publishArticle = async (submissionId) => {
    loading.value = true
    try {
      const response = await api.post(`/editor/submissions/${submissionId}/publish`)
      
      // Update submission in list
      const index = submissions.value.findIndex(s => s.id === submissionId)
      if (index !== -1) {
        submissions.value[index] = response.data.submission
      }
      
      error.value = null
      return response.data
    } catch (err) {
      error.value = err.message
      throw err
    } finally {
      loading.value = false
    }
  }

  const getAnalytics = async () => {
    loading.value = true
    try {
      const response = await api.get('/editor/analytics')
      error.value = null
      return response.data
    } catch (err) {
      error.value = err.message
      throw err
    } finally {
      loading.value = false
    }
  }

  return {
    submissions,
    currentSubmission,
    loading,
    error,
    fetchSubmissions,
    fetchSubmissionWithReviews,
    deskReject,
    inviteReviewers,
    makeDecision,
    publishArticle,
    getAnalytics,
  }
})
