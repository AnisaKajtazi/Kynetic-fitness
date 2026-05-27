<template>
  <div class="progress-page">

    <div class="main-header">
      <h1>Fitness Progress Dashboard</h1>
      <p>
        Track your nutrition, workouts, and weekly fitness journey.
      </p>
    </div>

    <MealsProgress />
    <ExerciseProgress />

  </div>
</template>

<script>
import MealsProgress from "@/views/MealsProgress.vue";
import ExerciseProgress from "@/views/ExerciseProgress.vue";

export default {
  name: "ProgressView",
  components: {
    MealsProgress,
    ExerciseProgress,
  },
};
</script>

<style scoped>
.progress-page {
  width: 100%;
  padding: 2rem;
}

.main-header {
  text-align: center;
  margin-bottom: 3rem;
}

.main-header h1 {
  font-size: 2.7rem;
  font-weight: 800;
  margin-bottom: 1rem;
  background: linear-gradient(90deg, var(--accent-blue), var(--accent-blue));
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  font-family: Lucida Handwriting;
}

.main-header p {
  color: #d1d5db;
  font-size: 1rem;
  max-width: 650px;
  margin: 0 auto;
  opacity: 0.9;
}
</style>