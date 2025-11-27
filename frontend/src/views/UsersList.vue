<template>
  <div class="users-wrapper">
    <div class="p-4 shadow rounded">
      <h2 class="text-center mb-4">Users List</h2>

      <button class="btn btn-primary mb-3" @click="openModal()">Add User</button>

      <UserForm 
        v-if="showForm"
        :user="selectedUser"
        @close="closeForm"
        @saved="fetchUsers"
      />

      <div class="table-responsive mt-3">
        <table class="table table-striped table-bordered align-middle">
          <thead class="table-dark text-center">
            <tr>
              <th>Username</th>
              <th>Name</th>
              <th>Email</th>
              <th>Role</th>
              <th>Gender</th>
              <th>Goal</th>
              <th>Activity</th>
              <th>Focus</th>
              <th>Actions</th>
            </tr>
          </thead>

          <tbody>
            <tr v-for="u in users" :key="u.UserID">
              <td>{{ u.username }}</td>
              <td>{{ u.name }} {{ u.surname }}</td>
              <td>{{ u.email }}</td>
              <td>{{ u.RoleID }}</td>
              <td>{{ u.gender }}</td>
              <td>{{ u.fitness_goal }}</td>
              <td>{{ u.activity_level }}</td>
              <td>{{ u.focus_area }}</td>
              <td class="text-center">
                <button @click="editUser(u)" class="btn btn-warning btn-sm me-2">Edit</button>
                <button @click="deleteUser(u.UserID)" class="btn btn-danger btn-sm">Delete</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import UserForm from "./UserForm.vue";

const BASE_URL = "http://127.0.0.1:8000/api";

export default {
  components: { UserForm },
  data() {
    return {
      users: [],
      showForm: false,
      selectedUser: null
    };
  },

  mounted() {
    this.fetchUsers();
  },

  methods: {
    async fetchUsers() {
      try {
        const res = await axios.get(`${BASE_URL}/users`, {
          headers: { 
            Authorization: `Bearer ${localStorage.getItem("token")}` 
          }
        });
        this.users = res.data;
      } catch (e) {
        console.error("Error loading users:", e);
      }
    },

    openModal() {
      this.selectedUser = null;
      this.showForm = true;
    },

    editUser(user) {
      this.selectedUser = { ...user };
      this.showForm = true;
    },

    closeForm() {
      this.showForm = false;
    },

    async deleteUser(id) {
      if (!confirm("Delete this user?")) return;

      await axios.delete(`${BASE_URL}/users/${id}`, {
        headers: { Authorization: `Bearer ${localStorage.getItem("token")}` }
      });

      this.fetchUsers();
    }
  }
};
</script>

<style scoped>
.users-wrapper {
  display: flex;
  justify-content: center;
  align-items: center;
  padding-top: 40px;
}

table {
  background-color: #02143aff;;
  border-radius:10px;
  width:100%;
  height:100%;
  padding:20px;
  margin:25px;
}

.table-dark th {
  color: white; 
}

td button {
  margin-bottom: 4px;
}
</style>
