<template>
  <div class="users-wrapper">
    <div class="users-panel admin-table-panel p-4 shadow rounded">
      <div class="admin-table-header">
        <div class="admin-table-title-block">
          <h2>Users List</h2>
        </div>

        <button class="btn btn-primary admin-create-btn" @click="openModal()">Create User</button>
      </div>

      <UserForm 
        v-if="showForm"
        :user="selectedUser"
        @close="closeForm"
        @saved="fetchUsers"
      />

      <div class="toolbar admin-table-toolbar admin-table-toolbar--meta d-flex justify-content-between align-items-center mb-3">
        <span class="table-count">{{ pagination ? pagination.total : users.length }} users</span>
        <input 
          type="text" 
          v-model="searchQuery" 
          @input="fetchUsers" 
          class="form-control admin-search-input"
          placeholder="Search by username or name..."
        />
      </div>

      <div class="table-responsive admin-table-shell mt-2">
        <table class="table admin-table table-striped table-bordered align-middle text-center">
          <thead class="table-dark">
            <tr>
              <th class="username-col">Username</th>
              <th class="name-col">Full Name</th>
              <th class="email-col">Email</th>
              <th class="role-col">Role</th>
              <th class="staff-type-col">Staff Type</th>
              <th class="gender-col">Gender</th>
              <th class="goal-col">Goal</th>
              <th class="activity-col">Activity</th>
              <th class="focus-col">Focus</th>
              <th class="training-col">Training Days</th>
              <th class="photo-col">Photo</th>
              <th class="actions-col">Actions</th>
            </tr>
          </thead>

          <tbody>
            <tr v-for="u in users" :key="u.UserID">
              <td>{{ u.username }}</td>
              <td>{{ u.name }} {{ u.surname }}</td>
              <td>{{ u.email }}</td>
              <td>{{ getRoleName(u.RoleID) }}</td>
              <td>
                <span
                  v-if="u.RoleID === 3 && u.staff_type"
                  class="admin-badge admin-badge--staff"
                >
                  {{ staffTypeLabel(u.staff_type) }}
                </span>
                <span v-else class="text-muted">—</span>
              </td>
              <td>
                <span
                  v-if="u.gender"
                  :class="['admin-badge', genderBadgeClass(u.gender)]"
                >
                  {{ genderInitial(u.gender) }}
                </span>
                <span v-else class="text-muted">—</span>
              </td>
              <td>{{ u.fitness_goal }}</td>
              <td>
                <span
                  v-if="u.activity_level"
                  :class="['admin-badge', activityBadgeClass(u.activity_level)]"
                >
                  {{ activityInitial(u.activity_level) }}
                </span>
                <span v-else class="text-muted">—</span>
              </td>
              <td>{{ u.focus_area }}</td>
              <td>
                <input
                  type="number"
                  class="admin-inline-input"
                  :value="u.training_days ?? 0"
                  min="0"
                  max="7"
                  step="1"
                  inputmode="numeric"
                  @keydown="blockIntegerKeys"
                  @input="handleTrainingDaysInput(u, $event)"
                  @change="saveTrainingDays(u)"
                  @blur="saveTrainingDays(u)"
                />
              </td>
              <td>
                <img 
                  v-if="u.photo" 
                  :src="imageUrl(u.photo)" 
                  alt="User Photo" 
                  style="max-width:60px; border-radius:6px;"
                />
              </td>
              <td class="actions-col">
                <div class="admin-actions">
                  <AdminActionButton variant="edit" title="Edit user" @click="editUser(u)" />
                  <AdminActionButton variant="delete" title="Delete user" @click="deleteUser(u.UserID)" />
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div v-if="pagination" class="admin-pagination d-flex align-items-center mt-3">
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
import AdminActionButton from "@/components/AdminActionButton.vue";
import { requestConfirmation } from "@/stores/confirmation";
import { showSuccess, showError } from "@/stores/notifications";

const BASE_URL = api.defaults.baseURL;
const IMG_BASE = BASE_URL.replace(/\/api\/?$/, "");

export default {
  components: { UserForm, AdminActionButton },
  data() {
    return {
      users: [],
      showForm: false,
      selectedUser: null,
      roles: { 1: "Admin", 2: "User", 3: "Staff" },
      searchQuery: "",
      perPage: 15,
      pagination: null,
      savingTrainingDays: new Set()
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

    genderInitial(value) {
      const map = { male: "M", female: "F", other: "O" };
      return map[value] || value;
    },

    genderBadgeClass(value) {
      const map = { male: "admin-badge--male", female: "admin-badge--female", other: "admin-badge--other" };
      return map[value] || "admin-badge--muted";
    },

    activityInitial(value) {
      const map = { low: "L", medium: "M", high: "H" };
      return map[value] || value;
    },

    activityBadgeClass(value) {
      const map = { low: "admin-badge--low", medium: "admin-badge--medium", high: "admin-badge--high" };
      return map[value] || "admin-badge--muted";
    },

    staffTypeLabel(value) {
      const labels = {
        trainer: "Trainer",
        maintenance: "Maintenance",
        service_staff: "Service Staff"
      };

      return labels[value] || value;
    },

    clampInteger(value, min, max) {
      const parsed = Number.parseInt(value, 10);
      if (Number.isNaN(parsed)) return min;
      return Math.min(max, Math.max(min, parsed));
    },

    blockIntegerKeys(event) {
      const blocked = ["e", "E", "+", "-"];
      if (blocked.includes(event.key)) {
        event.preventDefault();
      }
    },

    handleTrainingDaysInput(user, event) {
      const clamped = this.clampInteger(event.target.value, 0, 7);
      user.training_days = clamped;
      event.target.value = clamped;
    },

    async saveTrainingDays(user) {
      const value = this.clampInteger(user.training_days, 0, 7);
      user.training_days = value;

      if (this.savingTrainingDays.has(user.UserID)) return;

      this.savingTrainingDays.add(user.UserID);

      try {
        await api.put(`/users/${user.UserID}`, { training_days: value });
      } catch (error) {
        console.error("Error updating training days:", error);
      } finally {
        this.savingTrainingDays.delete(user.UserID);
      }
    },

    closeForm() {
      this.showForm = false;
    },

    async deleteUser(id) {
      const confirmed = await requestConfirmation({
        title: "Delete User",
        message: "Are you sure you want to delete this user?",
        detail: "This action cannot be undone.",
        confirmText: "Delete",
      });
      if (!confirmed) return;

      try {
        await api.delete(`/users/${id}`);
        showSuccess("User deleted successfully.");
        this.fetchUsers();
      } catch (error) {
        console.error("Error deleting user:", error);
        showError("User could not be deleted.");
      }
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

.toolbar {
  gap: 1rem;
}

.table-count {
  color: var(--text-muted);
  font-weight: 700;
}

.users-panel .username-col { width: 110px; }
.users-panel .name-col { width: 170px; }
.users-panel .email-col { width: 210px; }
.users-panel .role-col { width: 90px; }
.users-panel .staff-type-col { width: 120px; }
.users-panel .gender-col { width: 90px; }
.users-panel .goal-col { width: 120px; }
.users-panel .activity-col { width: 90px; }
.users-panel .focus-col { width: 120px; }
.users-panel .training-col { width: 120px; }
.users-panel .photo-col { width: 84px; }
.users-panel .actions-col { width: 100px; }

.text-muted {
  color: var(--text-dim) !important;
}

@media (max-width: 768px) {
  .toolbar {
    align-items: stretch !important;
    flex-direction: column;
  }
}
</style>
