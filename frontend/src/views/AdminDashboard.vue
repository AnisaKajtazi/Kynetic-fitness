<template>
  <div 
    class="admin-dashboard"
    :style="{ backgroundImage: backgroundUrl }"
  >
    <Sidebar
      :activeSection="activeSection"
      @changeSection="activeSection = $event"
    />

    <main class="admin-dashboard__main">
      <h3 class="text-center text-white mb-4">Admin Dashboard</h3>
      <component :is="currentSection" />
    </main>
  </div>
</template>

<script>
import Sidebar from '@/components/Sidebar.vue'
import UsersList from './UsersList.vue'
import RolesList from './RolesList.vue'
import ExercisesList from './ExercisesList.vue'

export default {
  name: 'AdminDashboard',
  components: {
    Sidebar,
    UsersList,
    RolesList,
    ExercisesList
  },
  data() {
    return {
      backgroundImage: 'admindashboardbackground.avif',
      activeSection: 'users',
    }
  },
  computed: {
    backgroundUrl() {
      return this.backgroundImage
        ? `url(http://127.0.0.1:8000/uploads/${this.backgroundImage})`
        : 'none'
    },

    currentSection() {
      const sections = {
        users: UsersList,
        roles: RolesList,
        exercises: ExercisesList,
      }

      return sections[this.activeSection] || UsersList
    }
  }
}
</script>

<style scoped>
.admin-dashboard {
  display: flex;
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
  min-height: 100vh;
  width: 100%;
  position: relative;
  color: white;
}

.admin-dashboard::before {
  content: "";
  position: absolute;
  inset: 0;
  background: rgba(0,0,0,0.4);
  z-index: 0;
}

.admin-dashboard__main {
  margin-left: 230px;
  width: calc(100% - 230px);
  padding: 20px;
  position: relative;
  z-index: 1;
}

.admin-dashboard :deep(.sidebar) {
  position: fixed;
  top: 0;
  left: 0;
  width: 230px;
  height: 100vh;
  z-index: 2;
}
</style>
