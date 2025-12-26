<template>
  <div class="modal-overlay">
    <div class="modal-content">
      <h3 class="mb-3">{{ role ? "Edit Role" : "Add Role" }}</h3>

      <form @submit.prevent="handleSubmit">

        <div class="form-row">
          <div class="form-group">
            <label>Name</label>
            <input
              type="text"
              class="form-control"
              v-model="formData.name"
              placeholder="Role name"
              required
            />
          </div>

          <div class="form-group">
            <label>Description</label>
            <textarea
              class="form-control"
              v-model="formData.description"
              placeholder="Role description"
              rows="3"
            />
          </div>
        </div>

        <div class="d-flex justify-content-end mt-4">
          <button type="button" class="btn btn-secondary me-2" @click="$emit('close')">
            Cancel
          </button>
          <button type="submit" class="btn btn-primary">
            {{ role ? "Update" : "Create" }}
          </button>
        </div>

      </form>
    </div>
  </div>
</template>

<script>
import axios from "axios";
const BASE_URL = "http://127.0.0.1:8000/api";

export default {
  props: ["role"],

  data() {
    return {
      formData: {
        name: "",
        description: "",
      },
    };
  },

  mounted() {
    if (this.role) {
      this.formData = {
        name: this.role.name,
        description: this.role.description,
      };
    }
  },

  methods: {
    async handleSubmit() {
      try {
        const headers = {
          Authorization: `Bearer ${localStorage.getItem("token")}`,
        };

        if (this.role && this.role.RoleID) {
          await axios.put(
            `${BASE_URL}/roles/${this.role.RoleID}`,
            this.formData,
            { headers }
          );
        } else {
          await axios.post(`${BASE_URL}/roles`, this.formData, { headers });
        }

        this.$emit("saved");
        this.$emit("close");
      } catch (error) {
        console.error("Error saving role:", error);
        alert("Error saving role");
      }
    },
  },
};
</script>

<style scoped>
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1050;
}

.modal-content {
  background: white;
  padding: 25px;
  width: 600px;
  max-width: 90%;
  border-radius: 12px;
}

.form-row {
  display: flex;
  gap: 20px;
}

.form-group {
  flex: 1;
  display: flex;
  flex-direction: column;
}
</style>
