<template>
  <div class="admin-modal-overlay">
    <div class="admin-modal-content">
      <h3 class="mb-3">{{ role ? "Edit Role" : "Add Role" }}</h3>

      <form @submit.prevent="handleSubmit">

        <div class="admin-form-row">
          <div class="admin-form-group">
            <label>Name</label>
            <input
              type="text"
              class="admin-form-control"
              v-model="formData.name"
              placeholder="Role name"
              required
            />
          </div>

          <div class="admin-form-group">
            <label>Description</label>
            <textarea
              class="admin-form-control"
              v-model="formData.description"
              placeholder="Role description"
              rows="3"
            ></textarea>
          </div>
        </div>

        <div class="admin-form-actions d-flex justify-content-end mt-4">
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
import { showError } from "@/stores/notifications";
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
        showError("Error saving role.");
      }
    },
  },
};
</script>

<style scoped>
</style>
