<template>
  <div class="trainer-appointments-layout">
    <Sidebar />

    <main class="page-container">
      <div class="page-header">
        <h1>My Clients</h1>
        <p>See the users who selected you as their trainer and stay connected to your assigned clients.</p>
      </div>

      <div v-if="loading" class="state-message">Loading your clients...</div>

      <div v-else>
        <section class="trainer-summary">
          <div class="profile-card">
            <img :src="photoUrl(trainer)" :alt="trainer.name" class="profile-photo" />
            <div class="profile-copy">
              <h2>{{ trainer.name }} {{ trainer.surname }}</h2>
              <p class="role">{{ trainerSpecialty(trainer) }}</p>
              <p>{{ trainerBio(trainer) }}</p>
            </div>
          </div>
        </section>

        <section class="clients-section">
          <div class="section-head">
            <h2>Assigned clients</h2>
            <span class="client-count">{{ clients.length }} client{{ clients.length === 1 ? '' : 's' }}</span>
          </div>

          <div v-if="clients.length === 0" class="empty-state">
            <p>No users have chosen you yet. Your assigned clients will appear here when they select you.</p>
          </div>

          <div v-else class="clients-grid">
            <article v-for="client in clients" :key="client.UserID" class="client-card">
              <img :src="photoUrl(client)" :alt="client.name" class="client-photo" />
              <div class="client-copy">
                <h3>{{ client.name }} {{ client.surname }}</h3>
                <p class="client-meta">{{ client.email }}</p>
                <div class="client-details">
                  <p><span class="label">Fitness Goal:</span> {{ client.fitness_goal || 'Not specified' }}</p>
                  <p><span class="label">Activity Level:</span> {{ client.activity_level || 'Not specified' }}</p>
                  <p><span class="label">Focus Area:</span> {{ client.focus_area || 'Not specified' }}</p>
                  <p><span class="label">Training Days:</span> {{ client.training_days ?? 'Not specified' }}</p>
                  <p v-if="client.phone"><span class="label">Phone:</span> {{ client.phone }}</p>
                </div>
                <div class="client-actions">
                  <button class="chat-btn" @click.prevent="openChat(client.UserID)">Chat <span v-if="unreadMap[client.UserID]" class="badge">{{ unreadMap[client.UserID] }}</span></button>
                </div>
              </div>
            </article>
          </div>
        </section>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import Sidebar from '@/components/Sidebar.vue'
import api from '@/services/axios'
import { useRouter } from 'vue-router'

const trainer = ref(null)
const clients = ref([])
const loading = ref(true)
const router = useRouter()

const loadClients = async () => {
  loading.value = true
  const storedUser = localStorage.getItem('user')
  if (!storedUser) {
    router.push('/login')
    return
  }

  trainer.value = JSON.parse(storedUser)

  try {
    const res = await api.get('users', {
      params: {
        preferred_trainer_id: trainer.value.UserID,
        per_page: 100,
      },
    })

    clients.value = Array.isArray(res.data.data) ? res.data.data : []
    await loadConversations()
  } catch (error) {
    console.error('Error loading clients:', error)
    clients.value = []
  } finally {
    loading.value = false
  }
}

const unreadMap = ref({})

const loadConversations = async () => {
  try {
    const res = await api.get('chat/conversations')
    const conv = res.data || []
    const map = {}
    conv.forEach(c => { map[c.user.UserID] = c.unread_count || 0 })
    unreadMap.value = map
  } catch (e) {
    console.error('Error loading conversations', e)
  }
}

const openChat = (userId) => {
  router.push({ path: '/chats', query: { peer: userId } })
}

const photoUrl = (user) => {
  if (!user || !user.photo) {
    return 'https://via.placeholder.com/500x360?text=Profile'
  }

  if (user.photo.startsWith('http')) {
    return user.photo
  }

  return `http://127.0.0.1:8000/uploads/profilephotos/${user.photo}`
}

const trainerSpecialty = (user) => {
  if (user.staff_type) {
    return user.staff_type.replace('_', ' ').replace(/\b\w/g, (ch) => ch.toUpperCase())
  }

  return 'Fitness Trainer'
}

const trainerBio = (user) => {
  return 'Professional coach focused on helping clients stay consistent, build better habits, and enjoy their fitness journey.'
}

const clientGoal = (client) => {
  if (client.fitness_goal) {
    return `Goal: ${client.fitness_goal}`
  }

  return 'Focused on healthier habits and better workouts.'
}

onMounted(loadClients)
</script>

<style scoped>
.trainer-appointments-layout {
  display: flex;
  width: 100%;
  min-height: 100vh;
  background: var(--bg-dark);
}

.page-container {
  margin-left: 240px;
  padding: 2rem;
  width: calc(100% - 240px);
}

.page-header {
  margin-bottom: 2rem;
}

.page-header h1 {
  font-size: 2.2rem;
  margin-bottom: 0.5rem;
  color: #f9fafb;
}

.page-header p {
  color: #d1d5db;
  max-width: 760px;
}

.state-message {
  color: #d1d5db;
}

.profile-card {
  display: flex;
  gap: 1.5rem;
  background: var(--bg-card);
  border-radius: 20px;
  padding: 1.75rem;
  box-shadow: var(--shadow-sm);
  align-items: center;
}

.profile-photo,
.client-photo {
  width: 180px;
  height: 180px;
  object-fit: cover;
  border-radius: 20px;
}

.profile-copy h2 {
  margin-bottom: 0.5rem;
  color: #f9fafb;
}

.role {
  color: #93c5fd;
  margin-bottom: 1rem;
}

.profile-copy p {
  color: #d1d5db;
  line-height: 1.7;
}

.clients-section {
  margin-top: 2rem;
}

.section-head {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  margin-bottom: 1rem;
}

.section-head h2 {
  color: #f9fafb;
  font-size: 1.5rem;
}

.client-count {
  color: #9ca3af;
}

.empty-state {
  background: var(--bg-card);
  padding: 1.5rem;
  border-radius: 16px;
  color: #d1d5db;
}

.clients-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 1.5rem;
}

.client-card {
  display: flex;
  gap: 1rem;
  background: var(--bg-card);
  border-radius: 16px;
  padding: 1.25rem;
  box-shadow: var(--shadow-sm);
  align-items: center;
}

.client-copy h3 {
  margin-bottom: 0.5rem;
  color: #f9fafb;
}

.client-meta {
  color: #93c5fd;
  margin-bottom: 0.75rem;
}

.client-details {
  display: grid;
  gap: 0.5rem;
  margin-bottom: 0.75rem;
}

.client-details p {
  color: #d1d5db;
  margin: 0;
  line-height: 1.6;
}

.client-details .label {
  color: #f8fafc;
  font-weight: 600;
}

.client-copy p {
  color: #d1d5db;
  margin-bottom: 0.75rem;
  line-height: 1.6;
}

.client-badges {
  display: flex;
  gap: 0.75rem;
  flex-wrap: wrap;
}

.client-badges span {
  background: #111827;
  color: #d1d5db;
  padding: 0.5rem 0.75rem;
  border-radius: 999px;
  font-size: 0.85rem;
}

.client-actions {
  margin-top: 0.75rem;
}

.chat-btn {
  background: transparent;
  border: 1px solid rgba(255,255,255,0.06);
  color: #d1d5db;
  padding: 0.45rem 0.7rem;
  border-radius: 8px;
  cursor: pointer;
}

.chat-btn .badge {
  background: #ef4444;
  color: #fff;
  padding: 0.1rem 0.45rem;
  border-radius: 999px;
  margin-left: 0.5rem;
  font-size: 0.75rem;
}

@media (max-width: 768px) {
  .trainer-appointments-layout {
    flex-direction: column;
  }

  .page-container {
    margin-left: 0;
    width: 100%;
    padding: 1rem;
  }

  .profile-card,
  .client-card {
    flex-direction: column;
    align-items: flex-start;
  }

  .profile-photo,
  .client-photo {
    width: 100%;
    height: 240px;
  }
}
</style>
