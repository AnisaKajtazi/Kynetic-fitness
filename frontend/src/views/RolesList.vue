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

      <div class="table-responsive mt-3">
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
    };
  },

  mounted() {
    this.fetchRoles();
  },

  methods: {
    async fetchRoles() {
      try {
        const res = await axios.get(`${BASE_URL}/roles`, {
          headers: {
            Authorization: `Bearer ${localStorage.getItem("token")}`,
          },
        });
        this.roles = res.data;
      } catch (e) {
        console.error("Error loading roles:", e);
      }
    },

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
        headers: {
          Authorization: `Bearer ${localStorage.getItem("token")}`,
        },
      });

      this.fetchRoles();
    },
  },
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
</style>
