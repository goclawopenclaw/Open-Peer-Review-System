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
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

export default router
