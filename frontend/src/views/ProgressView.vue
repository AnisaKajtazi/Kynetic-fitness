<template>
  <div class="progress-page">
    <h2 class="text-color">Fitness Progress Dashboard</h2>
    <p class="page-subtitle">
      Track your nutrition, workouts, and weekly fitness journey.
    </p>

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
  max-width: none;
  margin: 0;
  padding: 5rem;
  color: var(--text-light);
}

.text-color {
  color: var(--theme-ice);
  font-size: 2.2rem;
  margin-bottom: 0.5rem;
}

.page-subtitle {
  color: var(--text-muted);
  font-size: 1rem;
  margin: 0 0 2rem;
}
</style>
