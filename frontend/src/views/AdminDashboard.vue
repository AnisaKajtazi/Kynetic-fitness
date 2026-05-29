<template>
  <div class="admin-dashboard">
    <Sidebar
      :activeSection="activeSection"
      @changeSection="activeSection = $event"
    />

    <main class="admin-dashboard__main">
      <component :is="currentSection" />
    </main>
  </div>
</template>

<script>
import Sidebar from '@/components/Sidebar.vue'
import UsersList from './UsersList.vue'
import RolesList from './RolesList.vue'
import ExercisesList from './ExercisesList.vue'
import MealsList from './MealsList.vue'
import AdminSchedule from './AdminSchedule.vue'

export default {
  name: 'AdminDashboard',
  components: {
    Sidebar,
    UsersList,
    RolesList,
    ExercisesList,
    MealsList, 
    AdminSchedule
  },
  data() {
    return {
      activeSection: 'users',
    }
  },
  mounted() {
    const section = this.$route.query.activeSection
    if (section && this.sections[section]) {
      this.activeSection = section
    }
  },
  watch: {
    '$route.query.activeSection'(section) {
      if (section && this.sections[section]) {
        this.activeSection = section
      }
    }
  },
  computed: {
    sections() {
      return {
        users: UsersList,
        roles: RolesList,
        exercises: ExercisesList,
        meals: MealsList,
        schedule: AdminSchedule,
      }
    },

    currentSection() {
      return this.sections[this.activeSection] || UsersList
    }
  }
}
</script>

<style scoped>
.admin-dashboard {
  display: flex;
  width: 100vw;
  height: 100vh;
  overflow: hidden;
  background: var(--bg-dark);
  color: var(--text-light);
}

.admin-dashboard__main {
  margin-left: 230px;
  width: calc(100vw - 230px);
  height: 100vh;
  padding: 2rem;
  overflow-y: auto;
  box-sizing: border-box;
  background: var(--bg-card);
  border-left: 1px solid var(--border-dark);
}

.admin-dashboard :deep(.sidebar) {
  position: fixed;
  top: 0;
  left: 0;
  width: 230px;
  height: 100vh;
  z-index: 1000;
}
</style>
