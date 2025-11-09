import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import Dashboard from '../views/Dashboard.vue'

const routes = [
  { path: '/', name: 'home', component: HomeView },
  { path: '/exercises', name: 'exercises', component: () => import('../views/ExercisesView.vue') },
  { path: '/meals', name: 'meals', component: () => import('../views/MealsView.vue') },
  { path: '/contact', name: 'contact', component: () => import('../views/ContactView.vue') },
  { path: '/login', name: 'login', component: () => import('../views/LoginView.vue') },
  { path: '/forgot-password', name: 'forgot-password', component: () => import('../views/ForgotPassword.vue') },
  { path: '/reset-password', name: 'ResetPassword', component: () => import('../views/ResetPassword.vue') },
  { path: '/signup', name: 'signup', component: () => import('../views/SignupView.vue') },
  { path: '/dashboard', name: 'dashboard', component: Dashboard, meta: { requiresAuth: true } },
]

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes,
})


router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('token')

  if (to.meta.requiresAuth && !token) {
    return next('/login')
  }


  next()
})

export default router
