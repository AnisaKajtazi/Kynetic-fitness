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
        <h4>{{ exercise.name }}</h4>
        <p>Category: {{ exercise.category || 'Uncategorized' }}</p>
        <p>Level: {{ exercise.level || 'Beginner' }}</p>

        <button
          v-if="isLoggedIn"
          @click="removeFavorite(exercise)"
          class="btn-favorite"
        >
          <span>💖</span> Remove
        </button>
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
  max-width: var(--page-max-width);
  margin: 0 auto;
  padding: var(--page-padding-y) var(--page-padding-x);
}

.grid {
  display: grid;
  grid-template-columns: repeat(5, 1fr);
  gap: 1.25rem;
}

.exercise-card {
  background: var(--bg-card);
  padding: 1.25rem;
  border-radius: var(--radius);
  text-align: center;
  box-shadow: var(--shadow-sm);
  transition: transform 0.2s;
  width: 100%;
}

.exercise-card h4 {
  font-size: var(--text-md);
  margin: 0.5rem 0 0.35rem;
}

.exercise-card p {
  font-size: var(--text-sm);
  color: var(--text-muted);
  margin-bottom: 0.25rem;
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

.btn-favorite {
  background: none;
  border: none;
  font-size: 1rem;
  cursor: pointer;
  color: var(--text-accent);
  display: inline;
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
