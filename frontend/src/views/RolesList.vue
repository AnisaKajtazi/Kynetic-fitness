<template>
  <div class="roles-wrapper">
    <div class="p-4 shadow rounded">
      <h2 class="text-center mb-4">Roles List</h2>

      <button class="btn btn-primary mb-3" @click="openModal()">Add Role</button>

      <RoleForm
        v-if="showForm"
        :role="selectedRole"
        @close="closeForm"
        @saved="fetchRoles"
      />

      <div class="d-flex justify-content-end mb-2">
        <input
          type="text"
          v-model="searchQuery"
          @input="fetchRoles"
          class="form-control w-50"
          placeholder="Search by role name..."
        />
      </div>

      <div class="table-responsive mt-2">
        <table class="table table-striped table-bordered align-middle text-center">
          <thead class="table-dark">
            <tr>
              <th>Name</th>
              <th>Description</th>
              <th>Actions</th>
            </tr>
          </thead>

          <tbody>
            <tr v-for="r in roles" :key="r.RoleID">
              <td>{{ r.name }}</td>
              <td>{{ r.description }}</td>
              <td>
                <button @click="editRole(r)" class="btn btn-warning btn-sm me-2">
                  Edit
                </button>
                <button @click="deleteRole(r.RoleID)" class="btn btn-danger btn-sm">
                  Delete
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div v-if="pagination" class="d-flex justify-content-center align-items-center mt-3">
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
import axios from "axios";
import RoleForm from "./RoleForm.vue";

const BASE_URL = "http://127.0.0.1:8000/api";

export default {
  components: { RoleForm },

  data() {
    return {
      roles: [],
      showForm: false,
      selectedRole: null,
      searchQuery: "",
      perPage: 10,
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

      await axios.delete(`${BASE_URL}/roles/${id}`, {
        headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
      });

      this.fetchRoles();
    },

    async fetchRoles(page = 1) {
      try {
        const res = await axios.get(`${BASE_URL}/roles`, {
          headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
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
  display: flex;
  justify-content: center;
  align-items: center;
  padding-top: 40px;
}

table {
  background-color: #02143aff;
  border-radius: 10px;
  width: 100%;
  padding: 20px;
  margin: 25px 0;
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
