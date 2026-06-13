<template>
  <div class="admin-modal-overlay">
    <div class="admin-modal-content">
      <h3 class="mb-3">
        {{ exercise ? "Edit Exercise" : "Add Exercise" }}
      </h3>

      <form @submit.prevent="handleSubmit">
        <div class="admin-form-row">
          <div class="admin-form-group">
            <label>Name</label>
            <input
              class="admin-form-control"
              v-model="formData.name"
              placeholder="Exercise name"
              required
            />
          </div>

          <div class="admin-form-group">
            <label>Duration (seconds)</label>
            <input
              type="number"
              class="admin-form-control"
              v-model="formData.duration"
              min="0"
              max="300"
              step="1"
            />
          </div>
        </div>

        <div class="admin-form-row">
          <div class="admin-form-group">
            <label>Category</label>
            <select class="admin-form-control" v-model="formData.category">
              <option value="">Select category</option>
              <option>All Categories</option>
              <option>Full Body</option>
              <option>Upper Body</option>
              <option>Core</option>
              <option>Lower Body</option>
              <option>Cardio</option>
            </select>
          </div>

          <div class="admin-form-group">
            <label>Level</label>
            <select class="admin-form-control" v-model="formData.level">
              <option value="">Select difficulty</option>
              <option>Beginner</option>
              <option>Intermediate</option>
              <option>Advanced</option>
            </select>
          </div>
        </div>

        <div class="admin-form-group">
          <label>Description</label>
          <textarea
            class="admin-form-control"
            rows="3"
            v-model="formData.description"
          ></textarea>
        </div>

        <div class="admin-form-group">
          <label>Exercise Image / GIF</label>
          <input
            type="file"
            class="admin-form-control"
            accept="image/*"
            @change="handleFileChange"
          />

          <div v-if="exercise && exercise.image" class="mt-2">
            <small>Current image:</small>
            <img
              :src="`http://127.0.0.1:8000/uploads/${exercise.image}`"
              style="max-width:150px; display:block; margin-top:5px;"
            />
          </div>
        </div>

        <div class="admin-form-actions d-flex justify-content-end mt-4">
          <button type="button" class="btn btn-secondary me-2" @click="$emit('close')">
            Cancel
          </button>
          <button type="submit" class="btn btn-primary">
            {{ exercise ? "Update" : "Create" }}
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
  props: {
    exercise: {
      type: Object,
      default: null,
    },
  },

  data() {
    return {
      formData: {
        name: "",
        description: "",
        duration: "",
        category: "",
        level: "",
        image: null,
      },
    };
  },

  mounted() {
    if (this.exercise) {
      this.formData = {
        name: this.exercise.name,
        description: this.exercise.description,
        duration: this.exercise.duration,
        category: this.exercise.category,
        level: this.exercise.level,
        image: null,
      };
    }
  },

  methods: {
    handleFileChange(e) {
      this.formData.image = e.target.files[0];
    },

    async handleSubmit() {
  const formPayload = new FormData();

  formPayload.append("name", this.formData.name);
  formPayload.append("description", this.formData.description || "");
  formPayload.append("duration", this.formData.duration || "");
  formPayload.append("category", this.formData.category || "");
  formPayload.append("level", this.formData.level || "");

  if (this.formData.image) {
    formPayload.append("image", this.formData.image);
  }

  try {
    if (this.exercise && this.exercise.ExerciseID) {
      formPayload.append("_method", "PUT");

      await axios.post(
        `${BASE_URL}/exercises/${this.exercise.ExerciseID}`,
        formPayload,
        {
          headers: {
            Authorization: `Bearer ${localStorage.getItem("token")}`,
          },
        }
      );
    } else {
      await axios.post(`${BASE_URL}/exercises`, formPayload, {
        headers: {
          Authorization: `Bearer ${localStorage.getItem("token")}`,
        },
      });
    }

    this.$emit("saved");
    this.$emit("close");
  } catch (error) {
    console.error(error.response || error);
    alert(error.response?.data?.message || "Error saving exercise");
  }
}
,
  },
};
</script>

<style scoped>
</style>
