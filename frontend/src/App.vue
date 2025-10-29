<script setup>
import { RouterView } from 'vue-router'
import Navbar from './components/Navbar.vue'
</script>

<template>
  <div id="app">
    <Navbar />
    <main>
      <RouterView />
    </main>
  </div>
</template>

<style>
/* Stilim global për body */
body {
  margin: 0;
  min-height: 100vh;
  background: linear-gradient(135deg, #f5f7fa, #ffffffff);
  color: #333;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

/* Kontejneri kryesor */
#app {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}

/* Main ku shfaqet përmbajtja */
main {
  flex: 1;
  padding: 2rem;
  max-width: 1200px;
  margin: 0 auto;
  width: 100%;
}
</style>
