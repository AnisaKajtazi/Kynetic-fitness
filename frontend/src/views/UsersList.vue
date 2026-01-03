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

      <div class="d-flex justify-content-end mb-2">
        <input 
          type="text" 
          v-model="searchQuery" 
          @input="fetchUsers" 
          class="form-control w-50"
          placeholder="Search by username or name..."
        />
      </div>

      <div class="table-responsive mt-2">
        <table class="table table-striped table-bordered align-middle text-center">
          <thead class="table-dark">
            <tr>
              <th>Username</th>
              <th>Name</th>
              <th>Email</th>
              <th>Role</th>
              <th>Staff Type</th>
              <th>Gender</th>
              <th>Goal</th>
              <th>Activity</th>
              <th>Focus</th>
              <th>Training Days</th>
              <th>Photo</th> <!-- new photo column -->
              <th>Actions</th>
            </tr>
          </thead>

          <tbody>
            <tr v-for="u in users" :key="u.UserID">
              <td>{{ u.username }}</td>
              <td>{{ u.name }} {{ u.surname }}</td>
              <td>{{ u.email }}</td>
              <td>{{ getRoleName(u.RoleID) }}</td>
              <td>{{ u.RoleID === 3 ? u.staff_type : "" }}</td>
              <td>{{ u.gender }}</td>
              <td>{{ u.fitness_goal }}</td>
              <td>{{ u.activity_level }}</td>
              <td>{{ u.focus_area }}</td>
              <td>{{ u.training_days }}</td>
              <td>
                <img 
                  v-if="u.photo" 
                  :src="`http://127.0.0.1:8000/uploads/profilephotos/${u.photo}`" 
                  alt="User Photo" 
                  style="max-width:60px; border-radius:6px;"
                />
              </td>
              <td>
                <button @click="editUser(u)" class="btn btn-warning btn-sm me-2">Edit</button>
                <button @click="deleteUser(u.UserID)" class="btn btn-danger btn-sm">Delete</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div v-if="pagination" class="d-flex justify-content-center align-items-center mt-3">
        <button 
          class="btn btn-secondary btn-sm me-2" 
          :disabled="!pagination.prev_page_url"
          @click="fetchUsers(pagination.current_page - 1)"
        >
          Previous
        </button>

        <span class="mx-2">{{ pagination.current_page }} of {{ pagination.last_page }}</span>

        <button 
          class="btn btn-secondary btn-sm ms-2" 
          :disabled="!pagination.next_page_url"
          @click="fetchUsers(pagination.current_page + 1)"
        >
          Next
        </button>
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
      selectedUser: null,
      roles: { 1: "Admin", 2: "User", 3: "Staff" },
      searchQuery: "",
      perPage: 10,
      pagination: null
    };
  },

  mounted() {
    this.fetchUsers();
  },

  methods: {
    getRoleName(roleId) {
      return this.roles[roleId] || "Unknown";
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
    },

    async fetchUsers(page = 1) {
      try {
        const res = await axios.get(`${BASE_URL}/users`, {
          headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
          params: {
            search: this.searchQuery,
            per_page: this.perPage,
            page
          }
        });

        this.users = res.data.data.map(u => ({
          ...u,
          staff_type: u.staff_type || "",
          photo: u.photo || null
        }));

        this.pagination = res.data;
      } catch (e) {
        console.error("Error loading users:", e);
      }
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
  background-color: #02143aff;
  border-radius:10px;
  width:100%;
  padding:20px;
  margin:25px 0;
}

.table-dark th {
  color: white; 
}

td button {
  margin-bottom: 4px;
}

.form-control {
  max-width: 300px;
}
</style>
