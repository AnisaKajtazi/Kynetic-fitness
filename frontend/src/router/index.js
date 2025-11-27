import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import Dashboard from '../views/Dashboard.vue'
import AdminDashboard from '../views/AdminDashboard.vue'
import MyCart from '../views/MyCart.vue'

const routes = [
  { path: '/', name: 'home', component: HomeView },
  { path: '/exercises', name: 'exercises', component: () => import('../views/ExercisesView.vue') },
  { path: '/meals', name: 'meals', component: () => import('../views/MealsView.vue') },
  { path: '/contact', name: 'contact', component: () => import('../views/ContactView.vue') },
  { path: '/login', name: 'login', component: () => import('../views/LoginView.vue') },
  { path: '/forgot-password', name: 'forgot-password', component: () => import('../views/ForgotPassword.vue') },
  { path: '/reset-password', name: 'ResetPassword', component: () => import('../views/ResetPassword.vue') },
  { path: '/signup', name: 'signup', component: () => import('../views/SignupView.vue') },
  { path: '/admin-dashboard', name: 'admin-dashboard', component: AdminDashboard, meta: { requiresAuth: true, role: 1 } },
  { path: '/dashboard', name: 'dashboard', component: Dashboard, meta: { requiresAuth: true } },
  { path: '/my-cart', name: 'my-cart', component: MyCart, meta: { requiresAuth: true } },
]

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes,
})

router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('token')
  const role = Number(localStorage.getItem('role'))

  if (to.meta.requiresAuth && !token) {
    return next('/login')
  }

  if (to.meta.role && to.meta.role !== role) {
    return next('/')
  }

  next()
})

export default router
