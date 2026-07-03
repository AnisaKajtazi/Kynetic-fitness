<template>
  <div class="trainer-selection-layout">
    <Sidebar />

    <main class="page-container">
      <h2 class="text-color">Trainers</h2>
      <p class="page-subtitle">Choose one trainer to support your workouts. You can update your selection later, but only one trainer can be active at a time.</p>

      <div v-if="loading" class="state-message">Loading trainers...</div>

      <div v-else>
        <section class="selected-trainer" v-if="selectedTrainer">
          <div class="section-head">
            <h2>Your selected trainer</h2>
            <button class="secondary-btn" @click="clearSelection">Clear selection</button>
          </div>

          <article class="trainer-card featured">
            <img :src="photoUrl(selectedTrainer)" :alt="selectedTrainer.name" class="trainer-photo" />
            <div class="trainer-copy">
              <h3>{{ selectedTrainer.name }} {{ selectedTrainer.surname }}</h3>
              <h4>{{ trainerSpecialty(selectedTrainer) }}</h4>
              <p v-if="selectedTrainer.description">{{ selectedTrainer.description }}</p>
              <p v-else>{{ trainerBio(selectedTrainer) }}</p>
              <div class="featured-actions">
                <button class="primary-btn" disabled>Selected</button>
                <button class="chat-btn" @click.prevent="openChat(selectedTrainer.UserID)">Chat <span v-if="unreadMap[selectedTrainer.UserID]" class="badge">{{ unreadMap[selectedTrainer.UserID] }}</span></button>
              </div>
            </div>
          </article>
        </section>

        <section class="trainer-list">
          <h2>Available trainers</h2>
          <div class="trainers-grid">
            <article
              v-for="trainer in trainers"
              :key="trainer.UserID"
              :class="['trainer-card', selectedTrainer && selectedTrainer.UserID === trainer.UserID ? 'active' : '']"
            >
              <img :src="photoUrl(trainer)" :alt="trainer.name" class="trainer-photo" />
              <h3>{{ trainer.name }} {{ trainer.surname }}</h3>
              <h4>{{ trainerSpecialty(trainer) }}</h4>
              <p v-if="trainer.description">{{ trainer.description }}</p>
              <p v-else>{{ trainerBio(trainer) }}</p>
              <button
                class="select-btn"
                :disabled="selectedTrainer && selectedTrainer.UserID === trainer.UserID"
                @click="chooseTrainer(trainer)"
              >
                {{ selectedTrainer && selectedTrainer.UserID === trainer.UserID ? 'Selected' : 'Choose Trainer' }}
              </button>
              <button class="chat-btn" @click.prevent="openChat(trainer.UserID)">Chat <span v-if="unreadMap[trainer.UserID]" class="badge">{{ unreadMap[trainer.UserID] }}</span></button>
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
import { showSuccess, showError } from '@/stores/notifications'

const router = useRouter()
const trainers = ref([])
const selectedTrainer = ref(null)
const loading = ref(true)
const user = ref(null)

const loadTrainers = async () => {
  loading.value = true

  const storedUser = localStorage.getItem('user')
  if (!storedUser) {
    router.push('/login')
    return
  }

  user.value = JSON.parse(storedUser)

  try {
    const res = await api.get('users', {
      params: {
        role: 'trainer',
        per_page: 100,
      },
    })

    const allUsers = Array.isArray(res.data.data) ? res.data.data : []

    trainers.value = allUsers.filter(u => u.staff_type === 'trainer')

    selectedTrainer.value = trainers.value.find(
      (trainer) => trainer.UserID === user.value.preferred_trainer_id
    ) || null
    await loadConversations()
  } catch (error) {
    console.error('Error loading trainers:', error)
    trainers.value = []
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

const photoUrl = (trainer) => {
  if (!trainer || !trainer.photo) {
    return 'https://via.placeholder.com/500x360?text=Trainer'
  }

  if (trainer.photo.startsWith('http')) {
    return trainer.photo
  }

  return `${api.defaults.baseURL.replace(/\/api\/?$/, '')}/uploads/trainers/${trainer.photo}`
}

const trainerSpecialty = (trainer) => {
  if (trainer.staff_type) {
    return trainer.staff_type.replace('_', ' ').replace(/\b\w/g, (ch) => ch.toUpperCase())
  }

  if (trainer.focus_area) {
    return trainer.focus_area
  }

  return 'Personal Trainer'
}

const trainerBio = (trainer) => {
  if (trainer.fitness_goal) {
    return `Specializes in ${trainer.fitness_goal} programs and helps clients stay consistent.`
  }

  return 'Experienced coach helping clients reach their best with personalized training and motivation.'
}

const chooseTrainer = async (trainer) => {
  if (selectedTrainer.value && selectedTrainer.value.UserID === trainer.UserID) {
    return
  }

  try {
    const res = await api.put(`users/${user.value.UserID}`, {
      preferred_trainer_id: trainer.UserID,
    })

    user.value = res.data
    localStorage.setItem('user', JSON.stringify(user.value))
    selectedTrainer.value = trainer
    showSuccess('Trainer selected successfully.')
  } catch (error) {
    console.error('Error choosing trainer:', error)
    showError('Could not select this trainer. Please try again.')
  }
}

const clearSelection = async () => {
  if (!user.value || !user.value.preferred_trainer_id) return

  try {
    const res = await api.put(`users/${user.value.UserID}`, {
      preferred_trainer_id: null,
    })

    user.value = res.data
    localStorage.setItem('user', JSON.stringify(user.value))
    selectedTrainer.value = null
    showSuccess('Trainer selection cleared.')
  } catch (error) {
    console.error('Error clearing selection:', error)
    showError('Could not clear selection. Please try again.')
  }
}

onMounted(loadTrainers)
</script>

<style scoped>
.trainer-selection-layout {
  display: flex;
  width: 100vw;
  height: 100vh;
  overflow: hidden;
  background: var(--bg-dark);
}

.trainer-selection-layout :deep(.sidebar) {
  position: fixed;
  top: 0;
  left: 0;
  width: var(--sidebar-width);
  height: 100vh;
  z-index: 1000;
}

.page-container {
  margin-left: var(--sidebar-width);
  padding: 6rem;
  width: calc(100vw - var(--sidebar-width));
  height: 100vh;
  overflow-y: auto;
  box-sizing: border-box;
  background: var(--bg-card);
  border-left: 1px solid var(--border-dark);
  color: var(--text-light);
}

.text-color {
  font-size: 2.2rem;
  margin-bottom: 0.5rem;
  color: var(--theme-ice);
}

.page-subtitle {
  color: var(--text-muted);
  max-width: 760px;
  margin: 0 0 2rem;
}

.state-message {
  color: var(--text-muted);
}

.selected-trainer,
.trainer-list {
  margin-bottom: 2rem;
}

.section-head {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  margin-bottom: 1rem;
}

.section-head h2 {
  font-size: 1.5rem;
  color: var(--theme-ice);
}

.trainers-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(min(100%, 320px), 1fr));
  gap: 1.75rem;
  align-items: stretch;
  max-width: 1850px;
  margin: 0 auto;
}

.trainer-card {
  background: var(--bg-card);
  padding: 1.35rem;
  border-radius: var(--radius);
  border: 1px solid var(--border-dark);
  box-shadow: var(--shadow-sm);
  display: flex;
  flex-direction: column;
  justify-content: flex-start;
  gap: 0.65rem;
  min-width: 0;
}

.trainer-card.active {
  border-color: var(--accent-blue);
  box-shadow: var(--shadow-md);
}

.trainer-card.featured {
  grid-column: 1 / -1;
  flex-direction: row;
  gap: 1.5rem;
  align-items: center;
}

.trainer-photo {
  width: 100%;
  max-width: 360px;
  aspect-ratio: 1 / 1;
  height: auto;
  object-fit: cover;
  display: block;
  border-radius: var(--radius);
  margin: 0 auto 0.65rem;
}

.chat-btn {
  background: transparent;
  border: 1px solid var(--border-dark);
  color: var(--text-muted);
  padding: 0.7rem 1rem;
  border-radius: 10px;
  cursor: pointer;
  margin-top: 0.5rem;
  font-size: 1rem;
  line-height: 1.2;
}

.chat-btn .badge {
  background: var(--accent-plum);
  color: var(--text-strong);
  padding: 0.1rem 0.45rem;
  border-radius: 999px;
  margin-left: 0.5rem;
  font-size: 0.75rem;
}

.trainer-card.featured .trainer-photo {
  width: 360px;
  height: 320px;
  flex-shrink: 0;
}

.trainer-copy {
  flex: 1;
}

.trainer-card h3 {
  margin-bottom: 0.15rem;
  color: var(--text-light);
}

.trainer-card h4 {
  margin-bottom: 0.25rem;
  color: var(--theme-ice);
}

.trainer-card p {
  color: var(--text-muted);
  line-height: 1.6;
  margin-bottom: 0.35rem;
  flex: 1;
}

@media (min-width: 1500px) {
  .trainers-grid {
    grid-template-columns: repeat(3, minmax(0, 1fr));
  }
}

@media (max-width: 1280px) {
  .trainers-grid {
    grid-template-columns: repeat(auto-fit, minmax(min(100%, 280px), 1fr));
  }
}

.primary-btn,
  .secondary-btn,
  .select-btn {
  border: none;
  border-radius: 999px;
  padding: 1rem 1.6rem;
  cursor: pointer;
  font-weight: 700;
  font-size: 1rem;
  transition: transform 0.2s ease, background-color 0.2s ease;
}

.primary-btn {
  background: var(--accent-blue);
  color: var(--accent-purple);
}

.secondary-btn {
  background: transparent;
  color: var(--text-light);
  border: 1px solid var(--border-dark);
}

.select-btn {
  background: var(--accent-lavender);
  color: var(--text-strong);
}

.primary-btn:disabled,
.select-btn:disabled {
  opacity: 0.75;
  cursor: not-allowed;
}

.primary-btn:hover:not(:disabled),
.secondary-btn:hover,
.select-btn:hover:not(:disabled) {
  transform: translateY(-1px);
}

@media (max-width: 768px) {
  .trainer-selection-layout {
    flex-direction: column;
    height: auto;
    overflow: visible;
  }

  .trainer-selection-layout :deep(.sidebar) {
    position: relative;
    width: 100%;
    height: auto;
    padding: 1rem;
  }

  .page-container {
    margin-left: 0;
    width: 100%;
    height: auto;
    padding: 1rem;
    border-left: none;
  }

  .trainers-grid {
    grid-template-columns: 1fr;
    max-width: 420px;
  }

  .trainer-card.featured {
    flex-direction: column;
    align-items: flex-start;
  }

  .trainer-card.featured .trainer-photo {
    width: 100%;
    height: 260px;
  }
}
</style>
