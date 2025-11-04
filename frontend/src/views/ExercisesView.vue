<template>
  <main class="exercises-page">
    <section class="hero">
      <h2 class="text-center">Discover Exercises</h2>
      <p class="text-center text-muted">
        Explore exercises for every body part and difficulty level.
      </p>


      <div class="search-bar">
        <input
          type="text"
          placeholder="Search exercises..."
          v-model="searchQuery"
        />
      </div>


      <div class="filters">
        <select v-model="selectedCategory">
          <option value="">All Categories</option>
          <option
            v-for="cat in uniqueCategories"
            :key="cat"
            :value="cat"
          >
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
        <button class="btn btn--blue" @click="viewDetails(exercise)">
          View Details
        </button>
      </div>
    </section>


    <div
      v-if="selectedExercise"
      class="modal-overlay"
      @click.self="closeDetails"
    >
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
import axios from "axios";

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
    uniqueCategories() {
      return [...new Set(this.exercises.map(e => e.category))].filter(Boolean);
    },

    filteredExercises() {
      return this.exercises.filter(ex => {
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
        const res = await axios.get("http://localhost:8000/api/exercises");
        this.exercises = res.data.map(ex => ({
          ...ex,

          category: ex.category || "Uncategorized",
          // Siguro level default nëse nuk e ke
          level: ex.level || "Beginner",
        }));
      } catch (err) {
        console.error("Error fetching exercises:", err);
      }
    },
    getImageUrl(image) {
      return image
        ? `http://localhost:8000/uploads/${image}`
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
  },
};
</script>

<style scoped>
.exercises-page {
  padding: 2rem;
}

.hero {
  margin-bottom: 2rem;
  padding-top: 50px;
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
  grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
  gap: 1.5rem;
  justify-items: center; 
  align-items: start;
  width: 100%;
  margin: 0 auto;
}

.exercise-card {
  background: var(--bg-card);
  border-radius: var(--radius);
  box-shadow: var(--shadow-sm);
  padding: 1rem;
  text-align: center;
  transition: all 0.3s ease;
  width: 240px; 
}

.exercise-card:hover {
  transform: translateY(-4px);
  box-shadow: var(--shadow-md);
}



.exercise-img {
  width: 100%;
  height: 180px;
  object-fit: cover;
  border-radius: var(--radius);
  margin-bottom: 0.8rem;
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
  max-width: 500px;
  width: 90%;
  text-align: left;
}

.modal-content img.modal-img {
  width: 100%;
  height: 220px;
  object-fit: cover;
  margin-bottom: 1rem;
}

@media (max-width: 600px) {
  .exercise-card {
    width: 100%;
  }
}

</style>
