<template>
  <aside class="sidebar">
    <div class="sidebar__logo">🏋️‍♀️ Kynetic</div>

    <ul class="sidebar__links">

      <template v-if="roleID === 3">
        <li @click="router.push('/staff-dashboard')">Dashboard</li>
        <li @click="router.push('/my-schedule')">My Schedule</li>
        <li v-if="isTrainer" @click="router.push('/trainer-appointments')">
          Trainer Appointments
        </li>
        <li class="logout-btn" @click="logout">Logout</li>
      </template>

      <template v-else-if="roleID === 1">
        <li @click="$emit('changeSection', 'admin-dashboard')">Dashboard</li>
        <li @click="$emit('changeSection', 'users')">Users</li>
        <li @click="$emit('changeSection', 'roles')">Roles</li>
        <li @click="$emit('changeSection', 'exercises')">Exercises</li>
        <li @click="$emit('changeSection', 'meals')">Meals</li>
        <li @click="$emit('changeSection', 'schedule')">Schedule</li>
        <li class="logout-btn" @click="logout">Logout</li>
      </template>

      <template v-else-if="roleID === 2">
        <li @click="$emit('changeSection', 'home')">Dashboard</li>
        <li @click="$emit('changeSection', 'exercises')">Workout Highlights</li>
        <li @click="$emit('changeSection', 'meals')">My Personalised Menu</li>
        <li @click="$emit('changeSection', 'progress')">My Progress</li>
        <li @click="$emit('changeSection', 'mycart')">My Cart</li>
        <li class="logout-btn" @click="logout">Logout</li>
      </template>
    </ul>
  </aside>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { setLoggedIn } from '@/stores/auth'

const router = useRouter()
const roleID = ref(null)
const isTrainer = ref(false)

onMounted(() => {
  roleID.value = Number(localStorage.getItem('role'))
  const user = JSON.parse(localStorage.getItem('user'))
  isTrainer.value = user?.roleName === 'Trainer'
})

const logout = () => {
  localStorage.clear()
  setLoggedIn(false)
  router.push('/login')
}
</script>

<style scoped>
.sidebar {
  position: fixed;
  top: 0;
  left: 0;
  width: 240px;
  height: 100vh;
  background-color: #111827;
  color: #fff;
  padding: 2.5rem 1rem 1.5rem;
  display: flex;
  flex-direction: column;
  box-shadow: 2px 0 12px rgba(0, 0, 0, 0.4);
  z-index: 1000;
}

.sidebar__logo {
  font-size: 1.4rem;
  font-weight: bold;
  margin-bottom: 2.5rem;
  text-align: center;
}

.sidebar__links {
  list-style: none;
  padding: 0;
  margin: 0;
  display: flex;
  flex-direction: column;
  gap: 0.8rem;
}

.sidebar__links li {
  color: #d1d5db;
  font-weight: 500;
  padding: 0.7rem 1rem;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.25s ease;
}

.sidebar__links li:hover {
  background: #1f2937;
  color: #fff;
}

.sidebar__links li.active {
  background: #2563eb;
  color: #fff;
  font-weight: 600;
  box-shadow: 0 0 10px rgba(37, 99, 235, 0.35);
}

.logout-btn {
  margin-top: auto;
  background-color: #2563eb;
  color: white;
  font-weight: 600;
  text-align: center;
}

.logout-btn:hover {
  background-color: #1d4ed8;
}
</style>
