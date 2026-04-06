<template>
  <div class="max-w-6xl mx-auto py-8">
    <!-- Article Header -->
    <div v-if="article" class="bg-white rounded-lg shadow p-8 mb-8">
      <h1 class="text-4xl font-bold mb-4">{{ article.title }}</h1>

      <div class="space-y-3 mb-6">
        <p class="text-lg text-gray-700">
          By <span class="font-semibold">{{ article.author.name }}</span>
          <span v-if="article.author.affiliation" class="text-gray-600">
            ({{ article.author.affiliation }})
          </span>
        </p>
        <p class="text-gray-600">
          Published {{ formatDate(article.published_at) }}
          <span v-if="article.doi" class="ml-4 font-mono text-sm">
            DOI: <a :href="`https://doi.org/${article.doi}`" class="text-blue-600 hover:underline">{{ article.doi }}</a>
          </span>
        </p>
      </div>

      <div v-if="article.research_field || article.funding_source" class="flex gap-4 mb-6 text-sm">
        <span v-if="article.research_field" class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full">
          {{ article.research_field }}
        </span>
        <span v-if="article.funding_source" class="px-3 py-1 bg-green-100 text-green-800 rounded-full">
          Funding: {{ article.funding_source }}
        </span>
      </div>

      <!-- Abstract -->
      <div class="mb-6">
        <h2 class="text-xl font-bold mb-2">Abstract</h2>
        <p class="text-gray-700 leading-relaxed">{{ article.abstract }}</p>
      </div>

      <!-- Keywords -->
      <div v-if="article.keywords?.length" class="mb-6">
        <h3 class="font-semibold mb-2">Keywords</h3>
        <div class="flex flex-wrap gap-2">
          <span
            v-for="keyword in article.keywords"
            :key="keyword"
            class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm"
          >
            {{ keyword }}
          </span>
        </div>
      </div>

      <!-- Metadata -->
      <div v-if="article.data_availability || article.competing_interests" class="border-t pt-4">
        <h3 class="font-semibold mb-2">Additional Information</h3>
        <div class="space-y-2 text-sm">
          <div v-if="article.data_availability">
            <strong>Data Availability:</strong> {{ article.data_availability }}
          </div>
          <div v-if="article.competing_interests">
            <strong>Competing Interests:</strong> {{ article.competing_interests }}
          </div>
        </div>
      </div>
    </div>

    <!-- Reviews Section -->
    <div class="mb-8">
      <h2 class="text-3xl font-bold mb-6">
        Peer Reviews <span class="text-gray-600 text-lg">({{ reviews.length }} reviews)</span>
      </h2>

      <div v-if="reviews.length === 0" class="bg-white rounded-lg shadow p-8 text-center">
        <p class="text-gray-600">No reviews published for this article yet.</p>
      </div>

      <div v-for="review in reviews" :key="review.id" class="bg-white rounded-lg shadow p-6 mb-4">
        <!-- Review Header -->
        <div class="flex justify-between items-start mb-4 pb-4 border-b">
          <div>
            <h3 class="text-lg font-bold">
              {{ review.is_signed ? review.reviewer.name : 'Anonymous' }}
            </h3>
            <p v-if="review.is_signed && review.reviewer.affiliation" class="text-gray-600 text-sm">
              {{ review.reviewer.affiliation }}
            </p>
            <p class="text-gray-500 text-sm mt-1">
              {{ formatDate(review.submitted_at) }}
            </p>
          </div>
          <span :class="[
            'px-3 py-1 rounded-full text-sm font-semibold',
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

        <!-- Review Content -->
        <div class="space-y-4 mb-4">
          <div>
            <h4 class="font-semibold text-gray-900 mb-2">Summary</h4>
            <p class="text-gray-700 whitespace-pre-wrap">{{ review.summary }}</p>
          </div>

          <div>
            <h4 class="font-semibold text-gray-900 mb-2">Strengths</h4>
            <p class="text-gray-700 whitespace-pre-wrap">{{ review.strengths }}</p>
          </div>

          <div>
            <h4 class="font-semibold text-gray-900 mb-2">Weaknesses</h4>
            <p class="text-gray-700 whitespace-pre-wrap">{{ review.weaknesses }}</p>
          </div>

          <div>
            <h4 class="font-semibold text-gray-900 mb-2">Detailed Comments</h4>
            <p class="text-gray-700 whitespace-pre-wrap">{{ review.detailed_comments }}</p>
          </div>
        </div>

        <!-- Confidence and Inline Comments -->
        <div class="flex justify-between items-center pt-4 border-t text-sm text-gray-600">
          <span>
            Confidence: <span class="font-semibold capitalize">{{ review.confidence }}</span>
          </span>
          <span v-if="review.inline_comments?.length">
            {{ review.inline_comments.length }} inline comments
          </span>
        </div>
      </div>
    </div>

    <!-- Author Response (if exists) -->
    <div v-if="authorResponse" class="bg-blue-50 rounded-lg shadow p-6 mb-8">
      <h2 class="text-2xl font-bold mb-4">Author Response</h2>
      <p class="text-gray-700 whitespace-pre-wrap mb-4">{{ authorResponse.response_text }}</p>
      <p class="text-sm text-gray-600">
        Submitted {{ formatDate(authorResponse.submitted_at) }}
      </p>
      <a
        v-if="authorResponse.response_document_url"
        :href="authorResponse.response_document_url"
        class="text-blue-600 hover:underline text-sm mt-2 inline-block"
      >
        Download response document
      </a>
    </div>

    <!-- Revision History -->
    <div v-if="revisionHistory.length > 1" class="bg-white rounded-lg shadow p-6">
      <h2 class="text-2xl font-bold mb-4">Revision History</h2>
      <div class="space-y-2">
        <div v-for="(version, idx) in revisionHistory" :key="idx" class="flex items-center text-sm">
          <span class="px-2 py-1 bg-gray-200 text-gray-800 rounded font-mono text-xs mr-3">
            v{{ version }}
          </span>
          <span class="text-gray-600">Version {{ version }}</span>
        </div>
      </div>
    </div>

    <!-- Loading and Error states -->
    <div v-if="loading" class="text-center py-12">
      <p class="text-gray-600">Loading article...</p>
    </div>

    <div v-if="error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
      {{ error }}
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import api from '@/services/api'

const route = useRoute()
const articleId = route.params.id

const article = ref(null)
const reviews = ref([])
const authorResponse = ref(null)
const revisionHistory = ref([])
const loading = ref(false)
const error = ref(null)

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric',
  })
}

const formatRecommendation = (rec) => {
  const map = {
    accept: 'Accept',
    minor_revisions: 'Minor Revisions',
    major_revisions: 'Major Revisions',
    reject: 'Reject',
  }
  return map[rec] || rec
}

onMounted(async () => {
  loading.value = true
  try {
    const response = await api.get(`/public/articles/${articleId}`)
    article.value = response.data.article
    reviews.value = response.data.reviews || []
    authorResponse.value = response.data.author_response || null
    
    // Build revision history
    if (article.value.version) {
      revisionHistory.value = Array.from({ length: article.value.version }, (_, i) => i + 1)
    }

    error.value = null
  } catch (err) {
    error.value = 'Failed to load article'
  } finally {
    loading.value = false
  }
})
</script>
