<template>
  <nav class="navbar">
    <div class="navbar__inner">
      <div class="logo">🏋️‍♀️ <span class="brand">Kynetic</span></div>

      <button class="menu-btn" @click="toggleMenu" aria-label="Toggle menu">
        <span v-if="!menuOpen">☰</span>
        <span v-else>✕</span>
      </button>

      <ul :class="['nav-links', { open: menuOpen }]">
        <li><RouterLink to="/">Home</RouterLink></li>
        <li><a href="#" @click.prevent="scrollToSection('about-us')">About Us</a></li>
        <li><RouterLink to="/exercises">Exercises</RouterLink></li>
        <li><RouterLink to="/meals">Meals</RouterLink></li>
        <li><RouterLink to="/progress">Progress</RouterLink></li>
        <li><RouterLink to="/contact">Contact</RouterLink></li>
        <li><RouterLink to="/login">Login</RouterLink></li>
      </ul>
    </div>
  </nav>
</template>

<script setup>
import { ref } from 'vue';
const menuOpen = ref(false);
const toggleMenu = () => (menuOpen.value = !menuOpen.value);

const scrollToSection = (id) => {
  const section = document.getElementById(id);
  if (section) {
    section.scrollIntoView({ behavior: 'smooth' });
  }
};

</script>

<style scoped>
:root {
  --nav-bg: #111827;
  --accent: #ff4d4d;
  --text: #ffffff;
}

.navbar {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  z-index: 1200;
  background: linear-gradient(180deg, rgba(17,24,39,0.95), rgba(17,24,39,0.88));
  backdrop-filter: blur(6px);
  border-bottom: 1px solid rgba(255,255,255,0.03);
}

.navbar__inner {
  max-width: 1200px;
  margin: 0 auto;
  padding: .8rem 1rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
}

.logo {
  display: flex;
  align-items: center;
  gap: .5rem;
  color: var(--text);
  font-weight: 700;
  font-size: 1.05rem;
}

.brand {
  font-weight: 800;
  letter-spacing: .2px;
}

.menu-btn {
  display: none;
  background: none;
  border: none;
  color: var(--text);
  font-size: 1.25rem;
  cursor: pointer;
}

.nav-links {
  display: flex;
  gap: 1.25rem;
  list-style: none;
  align-items: center;
  margin: 0;
  padding: 0;
}

.nav-links a {
  color: var(--text);
  text-decoration: none;
  font-weight: 500;
  padding: .25rem .5rem;
  border-radius: 6px;
}

.nav-links a.router-link-exact-active {
  background: rgba(255,77,77,0.12);
  color: var(--accent);
}

.nav-links a:hover {
  color: var(--accent);
}


@media (max-width: 880px) {
  .menu-btn {
    display: block;
  }
  .nav-links {
    display: none;
    position: absolute;
    top: 62px;
    right: 12px;
    background: var(--nav-bg);
    flex-direction: column;
    padding: .8rem;
    border-radius: 10px;
    box-shadow: 0 8px 30px rgba(2,6,23,0.6);
    min-width: 180px;
    gap: .6rem;
  }
  .nav-links.open {
    display: flex;
  }
}
</style>
