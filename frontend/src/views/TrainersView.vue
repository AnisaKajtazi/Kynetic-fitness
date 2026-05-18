<template>
  <div class="trainers-page">
    <div class="hero">
      <h1 class="hero-title">Meet Our Expert Trainers</h1>
      <p class="hero-subtitle">Professional fitness coaches ready to guide your journey</p>
    </div>

    <div v-if="loading" class="loading">Loading trainers...</div>

    <div v-else class="trainers-container">
      <div class="trainers-grid">
        <article
          v-for="trainer in trainers"
          :key="trainer.UserID"
          class="trainer-card"
        >
          <img
            :src="getTrainerPhoto(trainer.photo)"
            :alt="trainer.fullName"
            class="trainer-photo"
          />
          <div class="trainer-info">
            <h3>{{ trainer.fullName }}</h3>
            <h4 class="trainer-focus">{{ trainer.focus_area || 'Personal Trainer' }}</h4>
            <p class="trainer-description">
              {{ trainer.description || 'This trainer has not added a description yet.' }}
            </p>
          </div>

          <button class="btn btn--blue" @click="viewDetails(trainer)">
            View Profile
          </button>
        </article>
      </div>

      <div v-if="selectedTrainer" class="modal-overlay" @click.self="closeDetails">
        <div class="modal-content">
          <button class="close-btn" @click="closeDetails">✕</button>
          <img
            :src="getTrainerPhoto(selectedTrainer.photo)"
            :alt="selectedTrainer.fullName"
            class="modal-photo"
          />
          <div class="modal-body">
            <h2>{{ selectedTrainer.fullName }}</h2>
            <p class="specialty"><strong>Specialty:</strong> {{ selectedTrainer.focus_area || 'General Training' }}</p>
            <p class="description">{{ selectedTrainer.description || 'This trainer has not added a description yet.' }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import api from '@/services/axios';

const trainers = ref([]);
const selectedTrainer = ref(null);
const loading = ref(true);

const fetchTrainers = async () => {
  try {
    const response = await api.get('/trainers');
    trainers.value = response.data.map((trainer) => ({
      ...trainer,
      fullName: `${trainer.name} ${trainer.surname}`.trim(),
    }));

    // If id in query string, show that trainer
    const route = useRoute();
    const trainerId = route.query.id;
    if (trainerId) {
      const trainer = trainers.value.find(
        (t) => t.UserID === parseInt(trainerId)
      );
      if (trainer) {
        selectedTrainer.value = trainer;
      }
    }
  } catch (error) {
    console.error('Unable to load trainers:', error);
  } finally {
    loading.value = false;
  }
};

const getTrainerPhoto = (photo) => {
  if (!photo) {
    return 'https://via.placeholder.com/360x320?text=Trainer+Photo';
  }
  return `http://127.0.0.1:8000/uploads/profilephotos/${photo}`;
};

const viewDetails = (trainer) => {
  selectedTrainer.value = trainer;
};

const closeDetails = () => {
  selectedTrainer.value = null;
};

onMounted(() => {
  fetchTrainers();
});
</script>

<style scoped>
.trainers-page {
  padding: 2rem;
  background: var(--bg-dark);
  min-height: 100vh;
}

.hero {
  text-align: center;
  margin-bottom: 3rem;
  padding-top: 2rem;
}

.hero-title {
  font-size: 3.2rem;
  font-weight: 800;
  color: #ffffff;
  margin-bottom: 0.5rem;
}

.hero-subtitle {
  font-size: 1.15rem;
  color: #a5b4fc;
  max-width: 600px;
  margin: 0 auto;
}

.loading {
  text-align: center;
  font-size: 1.1rem;
  color: #9ca3af;
  padding: 3rem;
}

.trainers-container {
  max-width: 1200px;
  margin: 0 auto;
}

.trainers-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 2rem;
  margin-bottom: 2rem;
}

.trainer-card {
  background: rgba(25, 28, 34, 0.8);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 20px;
  padding: 1.5rem;
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  gap: 1rem;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
}

.trainer-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 15px 40px rgba(79, 141, 255, 0.15);
  border-color: rgba(79, 141, 255, 0.3);
}

.trainer-photo {
  width: 180px;
  height: 180px;
  border-radius: 50%;
  object-fit: cover;
  border: 3px solid #4f8dff;
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
}

.trainer-info {
  flex: 1;
}

.trainer-info h3 {
  font-size: 1.3rem;
  color: #ffffff;
  margin-bottom: 0.3rem;
}

.trainer-focus {
  color: #93c5fd;
  font-size: 0.95rem;
  margin-bottom: 0.8rem;
  font-weight: 500;
}

.trainer-description {
  color: #d1d5db;
  font-size: 0.9rem;
  line-height: 1.6;
  margin-bottom: 0.5rem;
}

.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.7);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal-content {
  background: rgba(25, 28, 34, 0.98);
  border: 1px solid rgba(79, 141, 255, 0.2);
  border-radius: 24px;
  padding: 2.5rem;
  max-width: 500px;
  width: 90%;
  position: relative;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
}

.close-btn {
  position: absolute;
  top: 1rem;
  right: 1rem;
  background: none;
  border: none;
  font-size: 1.5rem;
  color: #9ca3af;
  cursor: pointer;
  transition: color 0.2s ease;
}

.close-btn:hover {
  color: #ffffff;
}

.modal-photo {
  width: 200px;
  height: 200px;
  border-radius: 50%;
  object-fit: cover;
  border: 3px solid #4f8dff;
  margin: 0 auto 1.5rem;
  display: block;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
}

.modal-body {
  text-align: center;
}

.modal-body h2 {
  font-size: 1.8rem;
  color: #ffffff;
  margin-bottom: 1rem;
}

.specialty {
  color: #93c5fd;
  font-size: 1rem;
  margin-bottom: 1rem;
}

.description {
  color: #d1d5db;
  line-height: 1.8;
  font-size: 0.95rem;
}
</style>
