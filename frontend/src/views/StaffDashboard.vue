<template>
  <div class="staff-dashboard">
    <Sidebar />

    <main class="dashboard-main">
      <section class="profile-section">
        <div class="profile-card">
          <div class="profile-header">
            <img :src="user.photo || defaultPhoto" alt="Profile Photo" class="profile-photo" />
            <div class="profile-info">
              <h2>Welcome back, {{ user.name }} {{ user.surname }}</h2>
              <p class="role">{{ user.roleName }}</p>
            </div>
          </div>

          <div class="profile-details">
            <p><strong>Email:</strong> {{ user.email }}</p>
            <p v-if="user.phone"><strong>Phone:</strong> {{ user.phone }}</p>
            <p v-if="user.address"><strong>Address:</strong> {{ user.address }}</p>
            <p v-if="user.dob"><strong>DOB:</strong> {{ formatDate(user.dob) }}</p>
            <p v-if="user.gender"><strong>Gender:</strong> {{ user.gender }}</p>
            <p v-if="user.fitness_goal"><strong>Fitness Goal:</strong> {{ user.fitness_goal }}</p>
            <p v-if="user.activity_level"><strong>Activity Level:</strong> {{ user.activity_level }}</p>
            <p v-if="user.training_days"><strong>Training Days:</strong> {{ user.training_days }}</p>
            <p v-if="user.focus_area"><strong>Focus Area:</strong> {{ user.focus_area }}</p>
          </div>
        </div>
      </section>

      <section class="overview-section">
        <h3>Quick Actions</h3>

        <div class="overview-grid">
          <div class="overview-card" @click="goToSchedule">
            <h4>My Schedule</h4>
            <p>Check and manage your schedule.</p>
          </div>

          <div class="overview-card" v-if="user.roleName === 'Trainer'" @click="goToAppointments">
            <h4>Trainer Appointments</h4>
            <p>View all your training appointments.</p>
          </div>

          <div class="overview-card" v-if="user.roleName === 'Maintenance'" @click="goToTasks">
            <h4>Assigned Tasks</h4>
            <p>Check current maintenance tasks.</p>
          </div>

          <div class="overview-card general-card" @click="goToProfile">
            <h4>My Profile</h4>
            <p>View or update your profile details.</p>
          </div>
        </div>
      </section>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import Sidebar from '@/components/Sidebar.vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const user = ref({})
//const defaultPhoto = '/assets/default-avatar.png'

onMounted(() => {
  const storedUser = localStorage.getItem('user')
  if (storedUser) {
    user.value = JSON.parse(storedUser)
  } else {
    router.push('/login')
  }
})

const formatDate = (dateStr) => {
  const d = new Date(dateStr)
  return d.toLocaleDateString()
}

const goToSchedule = () => router.push('/my-schedule')
const goToTasks = () => router.push('/maintenance-tasks')
const goToAppointments = () => router.push('/trainer-appointments')
const goToProfile = () => router.push('/staff-profile')
</script>

<style scoped>
.staff-dashboard {
  display: flex;
  width: 100vw;
  min-height: 100vh;
  background: linear-gradient(135deg, #0f1115, #1c1f26);
  color: #f9fafb;
}

.dashboard-main {
  flex: 1;
  margin-left: 240px;
  padding: 2rem;
}

/* Profile Section */
.profile-section {
  margin-bottom: 2.5rem;
}

.profile-card {
  background: #1f2937;
  border-radius: 12px;
  padding: 2rem;
  box-shadow: 0 4px 15px rgba(0,0,0,0.4);
  color: #f9fafb;
}

.profile-header {
  display: flex;
  align-items: center;
  gap: 1.5rem;
  margin-bottom: 1.5rem;
}

.profile-photo {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  object-fit: cover;
  border: 2px solid #2563eb;
}

.profile-info h2 {
  margin: 0;
  font-size: 1.5rem;
  font-weight: 600;
}

.profile-info .role {
  color: #60a5fa;
  font-weight: 500;
}

.profile-details p {
  margin: 0.3rem 0;
  color: #cbd5e1;
}

.overview-section h3 {
  font-size: 1.25rem;
  font-weight: 600;
  color: #f9fafb;
  margin-bottom: 1rem;
}

.overview-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 1rem;
}

.overview-card {
  background: #111827;
  padding: 1.2rem 1.5rem;
  border-radius: 12px;
  box-shadow: 0 3px 10px rgba(0,0,0,0.4);
  cursor: pointer;
  transition: all 0.25s ease;
  color: #f9fafb;
}

.overview-card h4 {
  margin: 0 0 0.4rem 0;
  color: #60a5fa;
}

.overview-card p {
  margin: 0;
  color: #cbd5e1;
}

.overview-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 6px 15px rgba(0,0,0,0.6);
}

.general-card {
  background-color: #2563eb;
  color: #f9fafb;
}

.general-card:hover {
  background-color: #1d4ed8;
}
</style>
