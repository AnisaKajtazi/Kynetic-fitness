<template>
  <aside class="sidebar">
    <div class="sidebar__logo">🏋️‍♀️ Kynetic</div>
    <ul class="sidebar__links">
      <li @click="$emit('changeSection', 'home')">Home</li>
      <li @click="$emit('changeSection', 'exercises')">Workout Highlights</li>
      <li @click="$emit('changeSection', 'meals')">Favorite Meals</li>
      <li @click="$emit('changeSection', 'progress')">My Progress</li>
      <li @click="$emit('changeSection', 'profile')">Profile</li>
    </ul>
  </aside>
</template>

<script setup>
import { ref, watch } from 'vue';
import { loggedIn } from '@/stores/auth';


watch(loggedIn, (val) => {
  console.log("Login status changed:", val);
});
</script>

<style scoped>
.sidebar {
  position: fixed;
  top: 0;
  left: 0;
  width: 240px;
  height: 100vh;
  background-color: #111827;
  color: #fff;
  padding: 2.5rem 1rem 1.5rem;
  display: flex;
  flex-direction: column;
  justify-content: flex-start;
  box-shadow: 2px 0 12px rgba(0, 0, 0, 0.4);
  z-index: 1000;
}

.sidebar__logo {
  font-size: 1.4rem;
  font-weight: bold;
  margin-bottom: 2.5rem;
  text-align: center;
  letter-spacing: 0.5px;
}

.sidebar__links {
  list-style: none;
  padding: 0;
  margin: 0;
  display: flex;
  flex-direction: column;
  gap: 0.8rem;
}

.sidebar__links li {
  color: #d1d5db;
  font-weight: 500;
  padding: 0.7rem 1rem;
  border-radius: 8px;
  transition: all 0.3s ease;
  cursor: pointer;
}

.sidebar__links li:hover {
  background: #1f2937;
  color: #fff;
}

.sidebar__links li.active {
  background: #2563eb;
  color: #fff;
  font-weight: 600;
  box-shadow: 0 0 10px rgba(37, 99, 235, 0.3);
}
</style>
