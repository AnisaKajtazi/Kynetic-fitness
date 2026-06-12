<template>
  <nav class="navbar">
    <div class="navbar__inner">
      <div class="logo"><span class="brand heading-font">Kynetic</span></div>

      
      <button class="menu-btn" @click="toggleMenu" aria-label="Toggle menu">
        <span v-if="!menuOpen">☰</span>
        <span v-else>✕</span>
      </button>

      <ul :class="['nav-links', { open: menuOpen }]">
        <li><RouterLink to="/">Home</RouterLink></li>
        <li><a href="#" @click.prevent="scrollToSection('about-us')">About Us</a></li>
        <li><RouterLink to="/exercises">Exercises</RouterLink></li>
        <li><RouterLink to="/meals">Meals</RouterLink></li>
        <li><RouterLink to="/contact">Contact</RouterLink></li>

        <li v-if="isUser"><RouterLink to="/dashboard">My Zone</RouterLink></li>   

        <li v-if="isStaff"><RouterLink to="/staff-dashboard">Staff Zone</RouterLink></li>

        <li v-if="isGuest"><RouterLink to="/login">Login</RouterLink></li>
        <li v-else>
          <RouterLink to="/chats" class="chat-link">
            <span
              class="chat-icon"
              :style="{ '--chat-icon': `url(${chatIcon})` }"
              role="img"
              aria-label="Chats"
            ></span>
            <span v-if="totalUnread" class="chat-badge">{{ totalUnread }}</span>
          </RouterLink>
        </li>
      </ul>
    </div>
  </nav>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { isUser, isStaff, isGuest } from '@/stores/auth'
import api from '@/services/axios'
import chatIcon from '@/icons/conversation.png'

const router = useRouter()
const route = useRoute()
const menuOpen = ref(false)
const toggleMenu = () => (menuOpen.value = !menuOpen.value)

const scrollToSection = async (id) => {
  // Close menu if open
  menuOpen.value = false

  // If not on home page, navigate to home first
  if (route.path !== '/') {
    await router.push('/')
    // Wait for page to render before scrolling
    setTimeout(() => {
      const section = document.getElementById(id)
      if (section) section.scrollIntoView({ behavior: 'smooth' })
    }, 100)
  } else {
    // Already on home page, just scroll
    const section = document.getElementById(id)
    if (section) section.scrollIntoView({ behavior: 'smooth' })
  }
}

const totalUnread = ref(0)

const loadUnread = async () => {
  try {
    const res = await api.get('chat/conversations')
    const conv = res.data || []
    const total = conv.reduce((acc, c) => acc + (c.unread_count || 0), 0)
    totalUnread.value = total
  } catch (e) {
    console.error('Error loading unread', e)
  }
}

onMounted(() => {
  loadUnread()
  setInterval(loadUnread, 5000)
})
</script>


<style scoped>
:root {
  --nav-bg: var(--bg-card);
  --accent: var(--accent-plum);
  --text: var(--text-strong);
}

.navbar {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  z-index: 1200;
  background: linear-gradient(180deg, rgba(78,20,140,0.96), rgba(44,7,53,0.88));
  backdrop-filter: blur(6px);
  border-bottom: 1px solid rgba(255,255,255,0.08);
}

.navbar__inner {
  max-width: 1500px;
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
  font-size: var(--text-xl);
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
  font-size: var(--text-sm);
  padding: .25rem .5rem;
  border-radius: 6px;
}

.nav-links a.router-link-exact-active {
  background: rgba(185, 211, 241, 0.12);
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

.chat-link { position: relative; color: var(--text); font-size: 1.1rem; display: inline-flex; align-items: center; }
.chat-badge { position: absolute; top: -6px; right: -10px; background: var(--accent-plum); color: var(--text-strong); padding: 0.1rem 0.45rem; border-radius: 999px; font-size: 0.75rem; }
.chat-icon {
  width: 37px;
  height: 37px;
  display: inline-block;
  background: var(--theme-lavender);
  mask: var(--chat-icon) center / contain no-repeat;
  -webkit-mask: var(--chat-icon) center / contain no-repeat;
}
</style>
