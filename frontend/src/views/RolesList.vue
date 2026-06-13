<template>
  <div class="roles-wrapper">
    <div class="admin-panel admin-table-panel p-4 shadow rounded">
      <div class="admin-table-header">
        <div class="admin-table-title-block">
          <h2>Roles List</h2>
        </div>

        <button class="btn btn-primary admin-create-btn" @click="openModal()">Create Role</button>
      </div>

      <RoleForm
        v-if="showForm"
        :role="selectedRole"
        @close="closeForm"
        @saved="fetchRoles"
      />

      <div class="toolbar admin-table-toolbar admin-table-toolbar--meta d-flex justify-content-between align-items-center mb-3">
        <span class="table-count">{{ pagination ? pagination.total : roles.length }} roles</span>
        <input
          type="text"
          v-model="searchQuery"
          @input="fetchRoles"
          class="form-control admin-search-input"
          placeholder="Search by role name..."
        />
      </div>

      <div class="table-responsive admin-table-shell mt-2">
        <table class="table admin-table table-striped table-bordered align-middle text-center">
          <thead class="table-dark">
            <tr>
              <th class="name-col">Name</th>
              <th class="description-col">Description</th>
              <th class="actions-col">Actions</th>
            </tr>
          </thead>

          <tbody>
            <tr v-for="r in roles" :key="r.RoleID">
              <td>{{ r.name }}</td>
              <td>{{ r.description }}</td>
              <td class="actions-col">
                <div class="admin-actions">
                  <AdminActionButton variant="edit" title="Edit role" @click="editRole(r)" />
                  <AdminActionButton variant="delete" title="Delete role" @click="deleteRole(r.RoleID)" />
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
          @click="fetchRoles(pagination.current_page - 1)"
        >
          Previous
        </button>

        <span class="mx-2">{{ pagination.current_page }} of {{ pagination.last_page }}</span>

        <button 
          class="btn btn-secondary btn-sm ms-2" 
          :disabled="!pagination.next_page_url"
          @click="fetchRoles(pagination.current_page + 1)"
        >
          Next
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import api from "@/services/axios";
import RoleForm from "./RoleForm.vue";
import AdminActionButton from "@/components/AdminActionButton.vue";

export default {
  components: { RoleForm, AdminActionButton },

  data() {
    return {
      roles: [],
      showForm: false,
      selectedRole: null,
      searchQuery: "",
      perPage: 15,
      pagination: null
    };
  },

  mounted() {
    this.fetchRoles();
  },

  methods: {
    openModal() {
      this.selectedRole = null;
      this.showForm = true;
    },

    editRole(role) {
      this.selectedRole = { ...role };
      this.showForm = true;
    },

    closeForm() {
      this.showForm = false;
    },

    async deleteRole(id) {
      if (!confirm("Delete this role?")) return;

      await api.delete(`/roles/${id}`);

      this.fetchRoles();
    },

    async fetchRoles(page = 1) {
      try {
        const res = await api.get("/roles", {
          params: {
            search: this.searchQuery,
            per_page: this.perPage,
            page
          }
        });

        this.roles = res.data.data;
        this.pagination = res.data;
      } catch (e) {
        console.error("Error loading roles:", e);
      }
    }
  }
};
</script>

<style scoped>
.roles-wrapper {
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

.name-col { width: 220px; }
.description-col { width: auto; }
.actions-col { width: 100px; }

@media (max-width: 768px) {
  .toolbar {
    align-items: stretch !important;
    flex-direction: column;
  }
}
</style>
