<template>
  <div class="section">
    <h1>Welcome back, {{ user.name }}</h1>
    <p>Track your exercises and progress this week.</p>

    <div v-if="weeklyExercises.length" class="weekly-exercises">
      <h2>Exercises of the Week</h2>
      <div class="exercise-cards">
        <div
          class="exercise-card"
          v-for="ex in weeklyExercises"
          :key="ex.ExerciseID"
        >
          <img :src="getImageUrl(ex.image)" :alt="ex.name" class="exercise-img" />
          <h4>{{ ex.name }}</h4>
          <button
            v-if="isLoggedIn"
            @click="toggleFavorite(ex)"
            class="favorite-btn"
          >
            <span v-if="ex.is_favorite">💖</span>
            <span v-else>🤍</span>
          </button>
        </div>
      </div>
    </div>

    <p v-else>No exercises for this week yet.</p>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import api from '@/services/axios';
import { loggedIn } from '@/stores/auth';


const user = ref(JSON.parse(localStorage.getItem('user')) || { name: 'User', id: 0 });
const weeklyExercises = ref([]);
const isLoggedIn = computed(() => loggedIn.value);


const getImageUrl = (image) => {
  return image
    ? `http://localhost:8000/uploads/${image}`
    : 'https://via.placeholder.com/300x200?text=No+Image';
};


const fetchWeeklyExercises = async () => {
  if (!user.value.id || !isLoggedIn.value) return;

  try {
    const res = await api.get(`weekly-exercises?user_id=${user.value.id}`);
    weeklyExercises.value = res.data.map((ex) => ({
      ...ex,
      is_favorite: ex.is_favorite || false
    }));
  } catch (err) {
    console.error('Error fetching weekly exercises:', err);
  }
};


const toggleFavorite = async (exercise) => {
  if (!isLoggedIn.value) return;

  try {
    if (exercise.is_favorite) {
      await api.delete(`/favorites/${exercise.ExerciseID}`);
      exercise.is_favorite = false;
    } else {
      await api.post('/favorites', { exercise_id: exercise.ExerciseID });
      exercise.is_favorite = true;
    }
  } catch (err) {
    console.error('Error toggling favorite:', err);
  }
};


onMounted(() => {
  fetchWeeklyExercises();
});
</script>

<style scoped>
.section {
  padding: 2rem;
}
h1 {
  font-size: 1.8rem;
  margin-bottom: 1rem;
}
p {
  color: var(--text-muted);
}

.weekly-exercises {
  margin-top: 2rem;
}

.exercise-cards {
  display: flex;
  flex-wrap: wrap;
  gap: 1rem;
}

.exercise-card {
  background: var(--bg-card);
  padding: 1rem;
  border-radius: var(--radius);
  width: 200px;
  text-align: center;
  box-shadow: var(--shadow-sm);
  transition: transform 0.2s;
}

.exercise-card:hover {
  transform: translateY(-4px);
  box-shadow: var(--shadow-md);
}

.exercise-img {
  width: 100%;
  height: 120px;
  object-fit: cover;
  border-radius: var(--radius);
  margin-bottom: 0.5rem;
}

.favorite-btn {
  background: none;
  border: none;
  font-size: 1.2rem;
  cursor: pointer;
}
</style>
