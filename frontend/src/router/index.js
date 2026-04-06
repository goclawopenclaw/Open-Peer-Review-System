import { createRouter, createWebHistory } from 'vue-router'

const routes = [
  {
    path: '/',
    name: 'Home',
    component: () => import('../views/HomeView.vue'),
  },
  {
    path: '/login',
    name: 'Login',
    component: () => import('../views/LoginView.vue'),
  },
  {
    path: '/register',
    name: 'Register',
    component: () => import('../views/RegisterView.vue'),
  },
  {
    path: '/submit',
    name: 'Submit',
    component: () => import('../views/SubmitView.vue'),
  },
  {
    path: '/submissions/:id',
    name: 'SubmissionDetail',
    component: () => import('../views/SubmissionDetailView.vue'),
  },
  {
    path: '/dashboard',
    name: 'Dashboard',
    component: () => import('../views/DashboardView.vue'),
  },
  {
    path: '/reviewer-dashboard',
    name: 'ReviewerDashboard',
    component: () => import('../views/ReviewerDashboardView.vue'),
  },
  {
    path: '/review/:id',
    name: 'ReviewForm',
    component: () => import('../views/ReviewFormView.vue'),
  },
  {
    path: '/articles',
    name: 'PublishedArticles',
    component: () => import('../views/PublishedArticlesView.vue'),
  },
  {
    path: '/article/:id',
    name: 'ArticleDetail',
    component: () => import('../views/ArticleDetailView.vue'),
  },
  {
    path: '/editor-dashboard',
    name: 'EditorDashboard',
    component: () => import('../views/EditorDashboardView.vue'),
  },
  {
    path: '/editorial-decision/:id',
    name: 'EditorialDecision',
    component: () => import('../views/EditorialDecisionView.vue'),
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

export default router
