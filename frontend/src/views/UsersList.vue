<template>
  <div class="users-wrapper">
    <div class="users-panel p-4 shadow rounded">
      <h2 class="text-center mb-4">Users List</h2>

      <button class="btn btn-primary mb-3" @click="openModal()">Add User</button>

      <UserForm 
        v-if="showForm"
        :user="selectedUser"
        @close="closeForm"
        @saved="fetchUsers"
      />

      <div class="toolbar d-flex justify-content-between align-items-center mb-3">
        <span class="table-count">{{ pagination ? pagination.total : users.length }} users</span>
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
                  :src="imageUrl(u.photo)" 
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
import api from "@/services/axios";
import UserForm from "./UserForm.vue";

const BASE_URL = api.defaults.baseURL;
const IMG_BASE = BASE_URL.replace(/\/api\/?$/, "");

export default {
  components: { UserForm },
  data() {
    return {
      users: [],
      showForm: false,
      selectedUser: null,
      roles: { 1: "Admin", 2: "User", 3: "Staff" },
      searchQuery: "",
      perPage: 15,
      pagination: null
    };
  },

  mounted() {
    this.fetchUsers();
  },

  methods: {
    imageUrl(photo) {
      return `${IMG_BASE}/uploads/profilephotos/${photo}`;
    },
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

      await api.delete(`/users/${id}`);

      this.fetchUsers();
    },

    async fetchUsers(page = 1) {
      try {
        const res = await api.get("/users", {
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
  width: 100%;
  padding: 1rem 0;
  color: var(--text-light);
}

.users-panel {
  width: 100%;
  max-width: none;
  background: var(--bg-card);
  border: 1px solid var(--border-dark);
  border-radius: var(--radius);
  box-shadow: var(--shadow-md);
}

.users-panel h2 {
  color: var(--theme-ice);
  font-size: 2.2rem;
  text-align: left !important;
}

.toolbar {
  gap: 1rem;
}

.table-count {
  color: var(--text-muted);
  font-weight: 700;
}

.table-responsive {
  width: 100%;
  overflow-x: auto;
}

table {
  width: 100%;
  min-width: 1280px;
  margin: 0;
  overflow: hidden;
  border-radius: var(--radius);
  border: 1px solid var(--border-dark);
  background: var(--bg-contrast);
}

.table > :not(caption) > * > * {
  padding: 0.9rem 1rem;
  background-color: transparent;
  border-color: var(--border-dark);
  color: var(--text-light);
}

.table-dark th,
.table thead th {
  background: var(--theme-plum);
  color: var(--theme-ice);
  border-color: var(--border-dark);
  font-weight: 800;
  white-space: nowrap;
}

.table-striped > tbody > tr:nth-of-type(odd) > * {
  background-color: rgba(var(--theme-night-rgb), 0.28);
}

.table-striped > tbody > tr:nth-of-type(even) > * {
  background-color: rgba(var(--theme-lavender-rgb), 0.08);
}

.table-bordered > :not(caption) > * {
  border-color: var(--border-dark);
}

.btn-primary {
  background: var(--accent-blue);
  border-color: var(--accent-blue);
  color: var(--theme-night);
  font-weight: 700;
}

.btn-warning {
  background: var(--theme-lavender);
  border-color: var(--theme-lavender);
  color: var(--text-strong);
  font-weight: 700;
}

.btn-danger {
  background: var(--theme-plum);
  border-color: var(--theme-plum);
  color: var(--text-strong);
  font-weight: 700;
}

.btn-secondary {
  background: var(--bg-contrast);
  border-color: var(--border-dark);
  color: var(--text-light);
}

td button {
  margin-bottom: 4px;
}

.form-control {
  max-width: 420px;
  background: var(--bg-contrast);
  border: 1px solid var(--border-dark);
  color: var(--text-light);
}

.form-control::placeholder {
  color: var(--text-dim);
}

.form-control:focus {
  background: var(--bg-contrast);
  border-color: var(--theme-ice);
  color: var(--text-light);
  box-shadow: 0 0 0 0.2rem rgba(var(--theme-ice-rgb), 0.18);
}

@media (max-width: 768px) {
  .toolbar {
    align-items: stretch !important;
    flex-direction: column;
  }

  .form-control {
    max-width: none;
    width: 100% !important;
  }
}
</style>
