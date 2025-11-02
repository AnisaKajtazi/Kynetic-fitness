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
import { ref, computed } from 'vue';
import Sidebar from '@/components/Sidebar.vue';


import DashboardHome from './DashboardHome.vue';
import DashboardExercises from './DashboardExercises.vue';
import DashboardProgress from './DashboardProgress.vue';

const activeSection = ref('home');

const sections = {
  home: DashboardHome,
  exercises: DashboardExercises,
  progress: DashboardProgress,
};

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
  width: 230px;
  height: 100vh;
  z-index: 1000;
}


.dashboard__main {
  flex: 1;
  margin-left: 230px;
  height: 100vh;
  width: calc(100vw - 230px);
  background: var(--bg-card);
  padding: 2rem;
  overflow-y: auto;
  box-sizing: border-box;
  border-left: 1px solid var(--border-dark);
}


.dashboard__main > * {
  width: 100%;
  max-width: 100%;
  height: auto;
}


@media (max-width: 1024px) {
  .dashboard__main {
    padding: 1.5rem;
  }
}

@media (max-width: 768px) {
  .dashboard {
    flex-direction: column;
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
