<template>
  <aside class="sidebar">
    <div class="sidebar__logo">🏋️‍♀️ Kynetic</div>

    <ul class="sidebar__links">

      <template v-if="roleID === 3">
        <li @click="router.push('/staff-dashboard')">Dashboard</li>
        <li @click="router.push('/my-schedule')">My Schedule</li>
        <li @click="router.push('/trainer-appointments')">
          My Clients
        </li>
        <li class="logout-btn" @click="logout">Logout</li>
      </template>

      <template v-else-if="roleID === 1">
        <li @click="navigate('users','/admin-dashboard')">Dashboard</li>
        <li @click="navigate('users','/admin-dashboard')">Users</li>
        <li @click="navigate('roles','/admin-dashboard')">Roles</li>
        <li @click="navigate('exercises','/admin-dashboard')">Exercises</li>
        <li @click="navigate('meals','/admin-dashboard')">Meals</li>
        <li @click="navigate('schedule','/admin-dashboard')">Schedule</li>
        <li class="logout-btn" @click="logout">Logout</li>
      </template>

      <template v-else-if="roleID === 2">
        <li @click="navigate('home','/dashboard')">Dashboard</li>
        <li @click="navigate('exercises','/dashboard')">Workout Highlights</li>
        <li @click="navigate('exercisesoftheweek','/dashboard')">Exercises Of The Week</li>
        <li @click="navigate('progress','/dashboard')">My Progress</li>
        <li @click="navigate('mycart','/dashboard')">My Cart</li>
        <li @click="navigate(null,'/trainers')">Trainers</li>
        <li class="logout-btn" @click="logout">Logout</li>
      </template>
    </ul>
  </aside>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { setLoggedIn } from '@/stores/auth'

const emit = defineEmits(['changeSection'])
const router = useRouter()
const route = useRoute()
const roleID = ref(null)
const isTrainer = ref(false)

onMounted(() => {
  roleID.value = Number(localStorage.getItem('role'))
  const user = JSON.parse(localStorage.getItem('user'))
  isTrainer.value = user?.roleName === 'Trainer'
})

const navigate = (section, path) => {
  // emit for parent dashboards that listen
  try { emit('changeSection', section) } catch (e) {}
  // always try to push route so standalone pages work
  if (path) {
    router.push({ path, query: { activeSection: section } })
  }
}

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
  background-color: var(--bg-card);
  color: var(--text-strong);
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
  background: var(--bg-card);
  color: var(--text-strong);
}

.sidebar__links li.active {
  background: var(--accent-blue);
  color: var(--text-strong);
  font-weight: 600;
  box-shadow: 0 0 10px rgba(37, 99, 235, 0.35);
}

.logout-btn {
  margin-top: auto;
  background-color: var(--accent-blue);
  color: white;
  font-weight: 600;
  text-align: center;
}

.logout-btn:hover {
  background-color: #1d4ed8;
}
</style>
