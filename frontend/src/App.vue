<script setup>
import { RouterView, useRoute } from 'vue-router'
import { computed } from 'vue'
import Navbar from './components/Navbar.vue'

const route = useRoute()
const showNavbar = computed(() => !route.path.startsWith('/admin'))
</script>

<template>
  <div id="app">
    <Navbar v-if="showNavbar" />
    <main>
      <RouterView />
    </main>
  </div>
</template>

<style>
html, body, #app {
  margin: 0;
  min-height: 100vh;
  width: 100%;
  background: var(--bg-dark);
  background-image: linear-gradient(180deg, rgba(var(--theme-night-rgb), 1), rgba(var(--theme-plum-rgb), 0.95));
  color: var(--text-light);
  font-family: var(--font-body);
  font-size: var(--text-base);
}

#app {
  display: flex;
  flex-direction: column;
}

main {
  flex: 1;
  padding: 0;
  max-width: 100%;
  margin: 0;
  width: 100%;
}
</style>
