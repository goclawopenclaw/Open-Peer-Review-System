import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '@/services/api'

export const useSubmissionStore = defineStore('submission', () => {
  const submissions = ref([])
  const currentSubmission = ref(null)
  const loading = ref(false)
  const error = ref(null)

  const fetchSubmissions = async () => {
    loading.value = true
    try {
      const response = await api.get('/submissions')
      submissions.value = response.data.data
      error.value = null
    } catch (err) {
      error.value = err.message
    } finally {
      loading.value = false
    }
  }

  const fetchSubmission = async (id) => {
    loading.value = true
    try {
      const response = await api.get(`/submissions/${id}`)
      currentSubmission.value = response.data.submission
      error.value = null
    } catch (err) {
      error.value = err.message
    } finally {
      loading.value = false
    }
  }

  const createSubmission = async (data) => {
    loading.value = true
    try {
      const response = await api.post('/submissions', data)
      submissions.value.push(response.data.submission)
      error.value = null
      return response.data.submission
    } catch (err) {
      error.value = err.message
      throw err
    } finally {
      loading.value = false
    }
  }

  const submitSubmission = async (id) => {
    loading.value = true
    try {
      const response = await api.post(`/submissions/${id}/submit`)
      currentSubmission.value = response.data.submission
      error.value = null
      return response.data.submission
    } catch (err) {
      error.value = err.message
      throw err
    } finally {
      loading.value = false
    }
  }

  const updateSubmission = async (id, data) => {
    loading.value = true
    try {
      const response = await api.patch(`/submissions/${id}`, data)
      const index = submissions.value.findIndex(s => s.id === id)
      if (index !== -1) {
        submissions.value[index] = response.data.submission
      }
      error.value = null
      return response.data.submission
    } catch (err) {
      error.value = err.message
      throw err
    } finally {
      loading.value = false
    }
  }

  const deleteSubmission = async (id) => {
    loading.value = true
    try {
      await api.delete(`/submissions/${id}`)
      submissions.value = submissions.value.filter(s => s.id !== id)
      error.value = null
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
    fetchSubmission,
    createSubmission,
    submitSubmission,
    updateSubmission,
    deleteSubmission,
  }
})
