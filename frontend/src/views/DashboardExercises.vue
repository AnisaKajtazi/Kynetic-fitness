<template>
  <div class="dashboard-exercises">
    <h2 class="text-color">Your Favorite Exercises</h2>

    <div v-if="exercises.length === 0">
      You haven't saved any favorite exercises yet.
    </div>

    <div class="grid">
      <div
        class="exercise-card"
        v-for="exercise in exercises"
        :key="exercise.ExerciseID"
      >
        <img
          :src="getImageUrl(exercise.image)"
          :alt="exercise.name"
          class="exercise-img"
        />
        <div class="exercise-info">
          <h4>{{ exercise.name }}</h4>
          <p>Category: {{ exercise.category || 'Uncategorized' }}</p>
          <p>Level: {{ exercise.level || 'Beginner' }}</p>
        </div>

        <div class="exercise-actions">
          <button
            v-if="isLoggedIn"
            @click="removeFavorite(exercise)"
            class="btn btn--red"
          >
            <span>💖</span> Remove
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { loggedIn } from "@/stores/auth";
import api from "@/services/axios";
import { watch } from "vue";

export default {
  name: "DashboardExercises",
  data() {
    return {
      exercises: [],
    };
  },
  computed: {
    isLoggedIn() {
      return loggedIn.value;
    },
  },
  methods: {
    async fetchFavorites() {
      if (!this.isLoggedIn) {
        this.exercises = [];
        return;
      }

      try {
        const res = await api.get("/favorites");
        this.exercises = res.data.map((ex) => ({
          ExerciseID: ex.ExerciseID,
          name: ex.name,
          description: ex.description,
          image: ex.image,
          category: ex.category || "Uncategorized",
          level: ex.level || "Beginner",
        }));
      } catch (err) {
        console.error("Error fetching favorites:", err.response || err);
      }
    },

    async removeFavorite(exercise) {
      if (!this.isLoggedIn) return;

      try {
        await api.delete(`/favorites/${exercise.ExerciseID}`);
        this.exercises = this.exercises.filter(
          (e) => e.ExerciseID !== exercise.ExerciseID
        );
      } catch (err) {
        console.error("Error removing favorite:", err.response || err);
      }
    },

    getImageUrl(image) {
      return image
        ? `http://127.0.0.1:8000/uploads/${image}`
        : "https://via.placeholder.com/300x200?text=No+Image";
    },
  },
  mounted() {
    if (this.isLoggedIn) {
      this.fetchFavorites();
    }

    watch(loggedIn, async (newVal) => {
      if (newVal) {
        await this.fetchFavorites();
      } else {
        this.exercises = [];
      }
    });
  },
};
</script>

<style scoped>
.dashboard-exercises {
  width: 100%;
  max-width: none;
  margin: 0 auto;
  padding: 2rem 1rem 2.5rem;
}

.grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 1.25rem;
  width: 100%;
  margin: 0 auto;
}

.exercise-card {
  background: var(--bg-card);
  border-radius: var(--radius);
  box-shadow: var(--shadow-sm);
  padding: 1.25rem;
  text-align: center;
  transition: all 0.3s ease;
  width: 100%;
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.exercise-info h4 {
  font-size: var(--text-md);
  margin-bottom: 0.25rem;
}

.exercise-info p {
  font-size: var(--text-sm);
  color: var(--text-muted);
}

.exercise-card:hover {
  transform: translateY(-4px);
  box-shadow: var(--shadow-md);
}

.exercise-img {
  width: 100%;
  height: 210px;
  object-fit: cover;
  border-radius: var(--radius);
  margin-bottom: 0.5rem;
}

.exercise-actions {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  margin-top: auto;
}

.exercise-actions .btn {
  width: 100%;
}

.btn--red {
  background: var(--accent-plum);
  color: var(--text-strong);
}

.btn--red:hover {
  background: var(--accent-plum);
}
.text-color{
  color: #97dffc;
  font-size: clamp(1.75rem, 3vw, 2.35rem);
  margin-bottom: 1rem;
}

@media (max-width: 1200px) {
  .grid {
    grid-template-columns: repeat(4, 1fr);
  }
}

@media (max-width: 992px) {
  .grid {
    grid-template-columns: repeat(3, 1fr);
  }
}

@media (max-width: 768px) {
  .grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 480px) {
  .grid {
    grid-template-columns: 1fr;
  }
}
</style>
