<template>
  <div 
    class="admin-dashboard"
    :style="{ backgroundImage: backgroundUrl }"
  >
    <h3 class="text-center text-white mb-4">Admin Dashboard</h3>
    <UsersList />
  </div>
</template>

<script>
import UsersList from './UsersList.vue';

export default {
  name: 'AdminDashboard',
  components: { UsersList },
  data() {
    return {
      backgroundImage: 'admindashboardbackground.avif',
    };
  },
  computed: {
    backgroundUrl() {
      return this.backgroundImage 
        ? `url(http://127.0.0.1:8000/uploads/${this.backgroundImage})` 
        : 'none';
    },
  },
};
</script>

<style scoped>
.admin-dashboard {
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
  min-height: 100vh;
  width: 100%;
  position: relative;
  padding: 20px;
  color: white;
}

.admin-dashboard::before {
  content: "";
  position: absolute;
  top: 0; left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0,0,0,0.4);
  z-index: 0;
}

.admin-dashboard > * {
  position: relative;
  z-index: 1;
}
</style>
