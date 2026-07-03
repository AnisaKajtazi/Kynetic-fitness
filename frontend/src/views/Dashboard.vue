<template>
  <div class="dashboard">
    <Sidebar
      :activeSection="activeSection"
      @changeSection="activeSection = $event"
    />

    <main class="dashboard__main">
      <component :is="currentSection" />
    </main>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import Sidebar from '@/components/Sidebar.vue';

import DashboardHome from './DashboardHome.vue';
import DashboardExercises from './DashboardExercises.vue';
import ExercisesOfTheWeek from './ExercisesOfTheWeek.vue';
import ProgressView from './ProgressView.vue';
import MyCart from './MyCart.vue';
import OrderSuccess from './OrderSuccess.vue';

const sections = {
  home: DashboardHome,
  exercises: DashboardExercises,
  exercisesoftheweek: ExercisesOfTheWeek,
  progress: ProgressView,
  mycart: MyCart,
  ordersuccess: OrderSuccess,
};

const route = useRoute();
const router = useRouter();
const activeSection = ref('home');

onMounted(() => {
  const section = route.query.activeSection;
  if (section && sections[section]) {
    activeSection.value = section;
  }
});

watch(
  () => route.query.activeSection,
  (val) => {
    if (val && sections[val]) {
      activeSection.value = val;
    }
  }
);

const currentSection = computed(() => sections[activeSection.value]);
</script>

<style scoped>
.dashboard {
  display: flex;
  width: 100vw;
  height: 100vh;
  overflow: hidden;
  background: var(--bg-dark);
}

.dashboard :deep(.sidebar) {
  position: fixed;
  top: 0;
  left: 0;
  width: var(--sidebar-width);
  height: 100vh;
  z-index: 1000;
}

.dashboard__main {
  flex: 1;
  margin-left: var(--sidebar-width);
  height: 100vh;
  width: calc(100vw - var(--sidebar-width));
  background: var(--bg-card);
  padding: var(--page-top-with-navbar) 2rem 2rem;
  overflow-y: auto;
  box-sizing: border-box;
  border-left: 1px solid var(--border-dark);
}

.dashboard__main > * {
  width: 100%;
  max-width: 100%;
  height: auto;
}

.dashboard__main :deep(.my-cart-page),
.dashboard__main :deep(.progress-page),
.dashboard__main :deep(.order-success-page) {
  padding-top: 0;
}

@media (max-width: 1024px) {
  .dashboard__main {
    padding: var(--page-top-with-navbar) 1.5rem 1.5rem;
  }
}

@media (max-width: 768px) {
  .dashboard {
    flex-direction: column;
    height: auto;
    overflow: visible;
    padding-top: var(--navbar-height);
  }

  .dashboard :deep(.sidebar) {
    position: relative;
    width: 100%;
    height: auto;
    padding: 1rem;
  }

  .dashboard__main {
    margin-left: 0;
    width: 100%;
    height: auto;
    border-left: none;
    padding: 1rem;
  }
}
</style>
