<template>
  <div class="max-w-6xl mx-auto py-8">
    <h1 class="text-4xl font-bold mb-2">Published Articles</h1>
    <p class="text-gray-600 mb-8">Browse peer-reviewed research with open, transparent reviews</p>

    <!-- Search and Filters -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
      <div class="grid grid-cols-2 gap-4 mb-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search by title, author, or keywords..."
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
          />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Research Field</label>
          <select
            v-model="selectedField"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
          >
            <option value="">All Fields</option>
            <option value="biology">Biology</option>
            <option value="medicine">Medicine</option>
            <option value="physics">Physics</option>
            <option value="chemistry">Chemistry</option>
            <option value="computer_science">Computer Science</option>
          </select>
        </div>
      </div>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Sort By</label>
          <select
            v-model="sortBy"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
          >
            <option value="newest">Newest First</option>
            <option value="oldest">Oldest First</option>
            <option value="most-reviewed">Most Reviewed</option>
            <option value="trending">Trending</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Reviews</label>
          <select
            v-model="reviewCount"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
          >
            <option value="">Any</option>
            <option value="3+">3+ reviews</option>
            <option value="5+">5+ reviews</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Articles Grid -->
    <div class="space-y-4">
      <div v-if="filteredArticles.length === 0" class="bg-white rounded-lg shadow p-12 text-center">
        <p class="text-gray-600 mb-2">No articles found matching your criteria.</p>
        <p class="text-sm text-gray-500">Try adjusting your search or filters.</p>
      </div>

      <div
        v-for="article in filteredArticles"
        :key="article.id"
        class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition cursor-pointer"
        @click="viewArticle(article.id)"
      >
        <div class="flex justify-between items-start mb-4">
          <div class="flex-1">
            <h2 class="text-2xl font-bold text-blue-600 hover:underline">{{ article.title }}</h2>
            <p class="text-gray-600 mt-2">
              By <span class="font-semibold">{{ article.author.name }}</span>
              <span v-if="article.author.affiliation" class="text-gray-500">
                ({{ article.author.affiliation }})
              </span>
            </p>
          </div>
          <span v-if="article.research_field" class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm font-medium">
            {{ article.research_field }}
          </span>
        </div>

        <p class="text-gray-700 mb-4 line-clamp-2">{{ article.abstract }}</p>

        <div class="flex flex-wrap gap-2 mb-4">
          <span v-for="keyword in article.keywords" :key="keyword" class="px-2 py-1 bg-blue-50 text-blue-700 text-xs rounded">
            {{ keyword }}
          </span>
        </div>

        <div class="flex justify-between items-center text-sm text-gray-600">
          <div class="space-x-4 flex">
            <div>
              <span class="font-semibold">{{ article.reviews?.length || 0 }}</span> reviews
            </div>
            <div>
              Published {{ formatDate(article.published_at) }}
            </div>
            <div v-if="article.doi" class="font-mono text-xs">
              DOI: {{ article.doi }}
            </div>
          </div>
          <button
            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm font-medium"
            @click.stop="viewArticle(article.id)"
          >
            View Article
          </button>
        </div>
      </div>
    </div>

    <!-- Loading and Error states -->
    <div v-if="loading" class="text-center py-12">
      <p class="text-gray-600">Loading articles...</p>
    </div>

    <div v-if="error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
      {{ error }}
    </div>

    <!-- Pagination -->
    <div v-if="totalPages > 1" class="flex justify-center mt-8 space-x-2">
      <button
        v-for="page in totalPages"
        :key="page"
        @click="currentPage = page"
        :class="[
          'px-3 py-2 rounded',
          page === currentPage
            ? 'bg-blue-600 text-white'
            : 'bg-gray-200 text-gray-800 hover:bg-gray-300'
        ]"
      >
        {{ page }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/services/api'

const router = useRouter()

const articles = ref([])
const loading = ref(false)
const error = ref(null)

const searchQuery = ref('')
const selectedField = ref('')
const sortBy = ref('newest')
const reviewCount = ref('')
const currentPage = ref(1)

const filteredArticles = computed(() => {
  let filtered = articles.value

  // Search
  if (searchQuery.value) {
    const q = searchQuery.value.toLowerCase()
    filtered = filtered.filter(a =>
      a.title.toLowerCase().includes(q) ||
      a.author.name.toLowerCase().includes(q) ||
      a.keywords?.some(k => k.toLowerCase().includes(q))
    )
  }

  // Field filter
  if (selectedField.value) {
    filtered = filtered.filter(a => a.research_field === selectedField.value)
  }

  // Review count filter
  if (reviewCount.value === '3+') {
    filtered = filtered.filter(a => (a.reviews?.length || 0) >= 3)
  } else if (reviewCount.value === '5+') {
    filtered = filtered.filter(a => (a.reviews?.length || 0) >= 5)
  }

  // Sort
  if (sortBy.value === 'newest') {
    filtered.sort((a, b) => new Date(b.published_at) - new Date(a.published_at))
  } else if (sortBy.value === 'oldest') {
    filtered.sort((a, b) => new Date(a.published_at) - new Date(b.published_at))
  } else if (sortBy.value === 'most-reviewed') {
    filtered.sort((a, b) => (b.reviews?.length || 0) - (a.reviews?.length || 0))
  }

  return filtered
})

const totalPages = computed(() => Math.ceil(filteredArticles.value.length / 10))

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric',
  })
}

const viewArticle = (articleId) => {
  router.push(`/article/${articleId}`)
}

onMounted(async () => {
  loading.value = true
  try {
    const response = await api.get('/public/articles')
    articles.value = response.data.data || []
    error.value = null
  } catch (err) {
    error.value = 'Failed to load articles'
  } finally {
    loading.value = false
  }
})
</script>
