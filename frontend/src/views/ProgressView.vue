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
  margin: 0 auto;
  padding: var(--page-top-with-navbar) 1rem 2.5rem;
  color: var(--text-light);
}

@media (max-width: 900px) {
  .progress-page {
    padding: var(--page-top-with-navbar) 0.75rem 2rem;
  }
}

.text-color {
  color: var(--theme-ice);
  font-size: clamp(1.75rem, 3vw, 2.35rem);
  margin-bottom: 0.75rem;
}

.page-subtitle {
  color: var(--text-muted);
  font-size: 1.15rem;
  margin: 0 0 2rem;
}
</style>
