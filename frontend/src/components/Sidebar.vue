<template>
  <aside class="sidebar">
    <div class="sidebar__logo">🏋️‍♀️ Kynetic</div>
    <ul class="sidebar__links">
      <li @click="$emit('changeSection', 'home')">Home</li>
      <li @click="$emit('changeSection', 'exercises')">Exercises</li>
      <li @click="$emit('changeSection', 'meals')">Meals</li>
      <li @click="$emit('changeSection', 'progress')">Progress</li>
      <li @click="$emit('changeSection', 'profile')">Profile</li>
    </ul>

    <button class="sidebar__logout" @click="logout">Logout</button>
  </aside>
</template>

<script setup>
import { useRouter } from 'vue-router';
const router = useRouter();

const logout = () => {
  localStorage.removeItem('token');
  router.push('/login');
};
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
  justify-content: space-between;
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
  flex-grow: 1;
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

.sidebar__logout {
  background: #ef4444;
  border: none;
  color: white;
  padding: 0.7rem 1rem;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.sidebar__logout:hover {
  background: #dc2626;
}
</style>
