<template>
  <div class="exercises-wrapper">
    <div class="p-4 shadow rounded">
      <h2 class="text-center mb-4">Exercises List</h2>

      <button class="btn btn-primary mb-3" @click="openModal">
        Add Exercise
      </button>

      <ExerciseForm
        v-if="showForm"
        :exercise="selectedExercise"
        @close="closeForm"
        @saved="fetchExercises"
      />

      <div class="table-responsive mt-3">
        <table class="table table-striped table-bordered align-middle text-center">
          <thead class="table-dark">
            <tr>
              <th>Name</th>
              <th>Category</th>
              <th>Level</th>
              <th>Duration (sec)</th>
              <th>Description</th>
              <th>Image</th>
              <th>Actions</th>
            </tr>
          </thead>

          <tbody>
            <tr v-for="e in exercises" :key="e.ExerciseID">
              <td>{{ e.name }}</td>
              <td>{{ e.category }}</td>
              <td>{{ e.level }}</td>
              <td>{{ e.duration }}</td>
              <td>{{ e.description }}</td>
              <td>
                <img
                  v-if="e.image"
                  :src="`http://127.0.0.1:8000/uploads/${e.image}`"
                  width="60"
                />
              </td>
              <td>
                <button
                  class="btn btn-warning btn-sm me-2"
                  @click="editExercise(e)"
                >
                  Edit
                </button>
                <button
                  class="btn btn-danger btn-sm"
                  @click="deleteExercise(e.ExerciseID)"
                >
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
import ExerciseForm from "./ExerciseForm.vue";

const BASE_URL = "http://127.0.0.1:8000/api";

export default {
  components: { ExerciseForm },
  data() {
    return {
      exercises: [],
      showForm: false,
      selectedExercise: null,
    };
  },
  mounted() {
    this.fetchExercises();
  },
  methods: {
    async fetchExercises() {
      const res = await axios.get(`${BASE_URL}/exercises`, {
        headers: {
          Authorization: `Bearer ${localStorage.getItem("token")}`,
        },
      });
      this.exercises = res.data;
    },

    openModal() {
      this.selectedExercise = null;
      this.showForm = true;
    },

    editExercise(exercise) {
      this.selectedExercise = { ...exercise };
      this.showForm = true;
    },

    closeForm() {
      this.showForm = false;
    },

    async deleteExercise(id) {
      if (!confirm("Delete this exercise?")) return;

      await axios.delete(`${BASE_URL}/exercises/${id}`, {
        headers: {
          Authorization: `Bearer ${localStorage.getItem("token")}`,
        },
      });

      this.fetchExercises();
    },
  },
};
</script>

<style scoped>
.exercises-wrapper {
  display: flex;
  justify-content: center;
  padding-top: 40px;
}

table {
  background-color: #02143aff;
  border-radius: 10px;
}
</style>
