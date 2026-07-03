<template>
  <main class="exercises-page">
    <section class="hero">
      <h2 class="hero-title heading-font page-hero-title">Discover Exercises</h2>
      <p class="hero-subtitle page-hero-subtitle">
        Explore exercises for every body part and difficulty level.
      </p>

      <div class="search-bar">
        <input type="text" placeholder="Search exercises..." v-model="searchQuery" />
      </div>

      <div class="filters">
        <select v-model="selectedCategory">
          <option value="">All Categories</option>
          <option v-for="cat in uniqueCategories" :key="cat" :value="cat">
            {{ cat }}
          </option>
        </select>

        <select v-model="selectedLevel">
          <option value="">All Levels</option>
          <option value="Beginner">Beginner</option>
          <option value="Intermediate">Intermediate</option>
          <option value="Advanced">Advanced</option>
        </select>
      </div>
    </section>

    <section class="grid">
      <div
        class="exercise-card"
        v-for="exercise in filteredExercises"
        :key="exercise.ExerciseID"
      >
        <img
          :src="getImageUrl(exercise.image)"
          :alt="exercise.name"
          class="exercise-img"
        />
        <div class="exercise-info">
          <h4>{{ exercise.name }}</h4>
          <p>Category: {{ exercise.category }}</p>
          <p>Level: {{ exercise.level }}</p>
        </div>

        <div class="exercise-actions">
          <button class="btn btn--blue" @click="viewDetails(exercise)">
            View Details
          </button>

          <button
            v-if="canUseUserActions"
            class="btn btn--red"
            @click="toggleFavorite(exercise)"
          >
            <span v-if="exercise.is_favorite">💖</span>
            <span v-else>🤍</span> Favorite
          </button>
          <button
            v-if="canUseUserActions"
            class="btn btn--green"
            @click="addToDay(exercise)"
          >
            Add To Today
          </button>
        </div>
      </div>
    </section>

    <div v-if="selectedExercise" class="modal-overlay" @click.self="closeDetails">
      <div class="modal-content">
        <h3>{{ selectedExercise.name }}</h3>
        <img
          :src="getImageUrl(selectedExercise.image)"
          :alt="selectedExercise.name"
          class="modal-img"
        />
        <p><strong>Category:</strong> {{ selectedExercise.category }}</p>
        <p><strong>Level:</strong> {{ selectedExercise.level }}</p>
        <p>{{ selectedExercise.description }}</p>
        <button class="btn btn--accent" @click="closeDetails">Close</button>
      </div>
    </div>
  </main>
</template>

<script>
import { watch } from "vue";
import { useRoute } from "vue-router";
import { loggedIn } from "../stores/auth";
import api from "../services/axios";
import { showSuccess, showWarning } from "@/stores/notifications";

export default {
  name: "Exercises",

  data() {
    return {
      searchQuery: "",
      selectedCategory: "",
      selectedLevel: "",
      selectedExercise: null,
      exercises: [],
    };
  },

  computed: {
    isLoggedIn() {
      return loggedIn.value;
    },

    canUseUserActions() {
      return this.isLoggedIn && Number(localStorage.getItem("role")) === 2;
    },

    uniqueCategories() {
      return [...new Set(this.exercises.map((e) => e.category))].filter(Boolean);
    },

    filteredExercises() {
      return this.exercises.filter((ex) => {
        const matchesCategory = this.selectedCategory
          ? ex.category === this.selectedCategory
          : true;

        const matchesLevel = this.selectedLevel
          ? ex.level === this.selectedLevel
          : true;

        const matchesSearch = this.searchQuery
          ? ex.name.toLowerCase().includes(this.searchQuery.toLowerCase())
          : true;

        return matchesCategory && matchesLevel && matchesSearch;
      });
    },
  },

  methods: {
    async fetchExercises() {
      try {
        const res = await api.get("/exercises/all");

        this.exercises = res.data.map((ex) => ({
          ...ex,
          category: ex.category || "Uncategorized",
          level: ex.level || "Beginner",
          is_favorite: false,
        }));

        if (this.canUseUserActions) {
          await this.fetchFavorites();
        }

        // If id in query string, show that exercise
        const route = useRoute();
        const exerciseId = route.query.id;
        if (exerciseId) {
          const exercise = this.exercises.find(
            (e) => e.ExerciseID === parseInt(exerciseId)
          );
          if (exercise) {
            this.selectedExercise = exercise;
          }
        }
      } catch (err) {
        console.error("Error fetching exercises:", err.response || err);
      }
    },

    async fetchFavorites() {
      try {
        const res = await api.get("/favorites");

        const favIds = res.data.map(
          (f) => f.ExerciseID || f.exercise_id
        );

        this.exercises = this.exercises.map((ex) => ({
          ...ex,
          is_favorite: favIds.includes(ex.ExerciseID),
        }));
      } catch (err) {
        console.error("Error fetching favorites:", err.response || err);
      }
    },

    async toggleFavorite(exercise) {
      if (!this.canUseUserActions) return;

      try {
        if (exercise.is_favorite) {
          await api.delete(`/favorites/${exercise.ExerciseID}`);
          exercise.is_favorite = false;
        } else {
          await api.post("/favorites", {
            exercise_id: exercise.ExerciseID,
          });
          exercise.is_favorite = true;
        }
      } catch (err) {
        console.error("Error toggling favorite:", err.response || err);
      }
    },

    async addToDay(exercise) {
      if (!this.canUseUserActions) return;

      try {
        const today = new Date().toLocaleDateString("en-US", {
          weekday: "long",
        });

      const res = await api.post("/exercise-week/add", {
        exercise_id: exercise.ExerciseID,
        day_of_week: today,
        reps: 3,
      });

      if (res.data.already_exists) {
        showWarning("Already added. Reps updated.");
      } else {
        showSuccess("Added to today's plan.");
      }

      } catch (err) {
        console.error("Error adding to day:", err.response || err);
      }
},

    getImageUrl(image) {
      return image
        ? `http://127.0.0.1:8000/uploads/${image}`
        : "https://via.placeholder.com/300x200?text=No+Image";
    },

    viewDetails(exercise) {
      this.selectedExercise = exercise;
    },

    closeDetails() {
      this.selectedExercise = null;
    },
  },

  mounted() {
    this.fetchExercises();

    watch(loggedIn, async (newVal) => {
      if (newVal && this.canUseUserActions) {
        await this.fetchFavorites();
      } else {
        this.exercises = this.exercises.map((ex) => ({
          ...ex,
          is_favorite: false,
        }));
      }
    });
  },
};
</script>

<style scoped>
.exercises-page {
  width: 100%;
  max-width: none;
  margin: 0 auto;
  padding: var(--page-top-with-navbar) 1rem 2.5rem;
}

.hero {
  margin-bottom: 1.5rem;
}

.hero-title {
  text-align: center;
}

.hero-subtitle {
  text-align: center;
  padding-bottom: 1.5rem;
}

.search-bar input {
  padding: 0.8rem 1rem;
  width: 100%;
  margin-bottom: 1rem;
  border-radius: var(--radius);
  border: 1px solid var(--border-dark);
}

.filters {
  display: flex;
  gap: 1rem;
  margin-bottom: 2rem;
}

.filters select {
  padding: 0.8rem 1rem;
  border-radius: var(--radius);
  border: 1px solid var(--border-dark);
  background: var(--bg-card);
  color: var(--text-light);
}

.grid {
  display: grid;
  grid-template-columns: repeat(5, 1fr);
  gap: 1rem;
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

.exercise-actions {
  display: flex;
  flex-direction: column;
  gap: 0.8rem;
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

.btn--green {
  background: var(--accent-lavender);
  color: var(--text-strong);
}

.btn--green:hover {
  background: var(--accent-lavender);
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

.exercise-info h4 {
  font-size: var(--text-md);
  margin-bottom: 0.25rem;
}

.exercise-info p {
  font-size: var(--text-sm);
  color: var(--text-muted);
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
  background: var(--bg-card);
  padding: 2rem;
  border-radius: var(--radius-lg);
  max-width: 560px;
  width: 90%;
  text-align: left;
  font-size: var(--text-base);
}

.modal-content img.modal-img {
  width: 100%;
  height: 260px;
  object-fit: cover;
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

@media (max-width: 900px) {
  .exercises-page {
    padding: var(--page-top-with-navbar) 0.75rem 2rem;
  }

  .grid {
    gap: 0.9rem;
  }
}
</style>
