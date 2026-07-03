<template>
  <div class="exercises-wrapper">
    <div class="admin-panel admin-table-panel p-4 shadow rounded">
      <div class="admin-table-header">
        <div class="admin-table-title-block">
          <h2>Exercises List</h2>
        </div>

        <button class="btn btn-primary admin-create-btn" @click="openModal">
          Create Exercise
        </button>
      </div>

      <ExerciseForm
        v-if="showForm"
        :exercise="selectedExercise"
        @close="closeForm"
        @saved="fetchExercises"
      />

      <div class="toolbar admin-table-toolbar admin-table-toolbar--meta d-flex justify-content-between align-items-center mb-3">
        <span class="table-count">{{ pagination ? pagination.total : exercises.length }} exercises</span>
        <input
          type="text"
          v-model="searchQuery"
          @input="fetchExercises"
          class="form-control admin-search-input"
          placeholder="Search by exercise name..."
        />
      </div>

      <div class="table-responsive admin-table-shell mt-2">
        <table class="table admin-table table-striped table-bordered align-middle text-center">
          <thead class="table-dark">
            <tr>
              <th class="name-col">Name</th>
              <th class="category-col">Category</th>
              <th class="level-col">Level</th>
              <th class="duration-col">Duration (sec)</th>
              <th class="description-col">Description</th>
              <th class="image-col">Image</th>
              <th class="actions-col">Actions</th>
            </tr>
          </thead>

          <tbody>
            <tr v-for="e in exercises" :key="e.id">
              <td>{{ e.name }}</td>
              <td>{{ e.category }}</td>
              <td>{{ e.level }}</td>
              <td>
                <input
                  type="number"
                  class="admin-inline-input"
                  :value="e.duration ?? 0"
                  min="0"
                  max="300"
                  step="1"
                  inputmode="numeric"
                  @keydown="blockIntegerKeys"
                  @input="handleDurationInput(e, $event)"
                  @change="saveDuration(e)"
                  @blur="saveDuration(e)"
                />
              </td>
              <td>{{ e.description }}</td>
              <td>
                <img
                  v-if="e.image"
                  :src="`http://127.0.0.1:8000/uploads/${e.image}`"
                  width="60"
                />
              </td>
              <td class="actions-col">
                <div class="admin-actions">
                  <AdminActionButton variant="edit" title="Edit exercise" @click="editExercise(e)" />
                  <AdminActionButton variant="delete" title="Delete exercise" @click="deleteExercise(e.id)" />
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
          @click="fetchExercises(pagination.current_page - 1)"
        >
          Previous
        </button>

        <span class="mx-2">{{ pagination.current_page }} of {{ pagination.last_page }}</span>

        <button 
          class="btn btn-secondary btn-sm ms-2" 
          :disabled="!pagination.next_page_url"
          @click="fetchExercises(pagination.current_page + 1)"
        >
          Next
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import api from "@/services/axios";
import ExerciseForm from "./ExerciseForm.vue";
import AdminActionButton from "@/components/AdminActionButton.vue";
import { requestConfirmation } from "@/stores/confirmation";
import { showSuccess, showError } from "@/stores/notifications";

export default {
  components: { ExerciseForm, AdminActionButton },

  data() {
    return {
      exercises: [],
      showForm: false,
      selectedExercise: null,
      searchQuery: "",
      perPage: 15,
      pagination: null,
      savingDurationIds: new Set()
    };
  },

  mounted() {
    this.fetchExercises();
  },

  methods: {
    openModal() {
      this.selectedExercise = null;
      this.showForm = true;
    },

    editExercise(exercise) {
      this.selectedExercise = { ...exercise };
      this.showForm = true;
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

    handleDurationInput(exercise, event) {
      const clamped = this.clampInteger(event.target.value, 0, 300);
      exercise.duration = clamped;
      event.target.value = clamped;
    },

    async saveDuration(exercise) {
      const value = this.clampInteger(exercise.duration, 0, 300);
      exercise.duration = value;

      if (this.savingDurationIds.has(exercise.id)) return;
      this.savingDurationIds.add(exercise.id);

      try {
        await api.put(`/exercises/${exercise.id}`, {
          name: exercise.name,
          description: exercise.description || "",
          duration: value,
          category: exercise.category || "",
          level: exercise.level || ""
        });
      } catch (error) {
        console.error("Error updating exercise duration:", error);
      } finally {
        this.savingDurationIds.delete(exercise.id);
      }
    },

    closeForm() {
      this.showForm = false;
    },

    async deleteExercise(id) {
      const confirmed = await requestConfirmation({
        title: "Delete Exercise",
        message: "Are you sure you want to delete this exercise?",
        detail: "This action cannot be undone.",
        confirmText: "Delete",
      });
      if (!confirmed) return;

      try {
        await api.delete(`/exercises/${id}`);
        showSuccess("Exercise deleted successfully.");
        this.fetchExercises();
      } catch (error) {
        console.error("Error deleting exercise:", error);
        showError("Exercise could not be deleted.");
      }
    },

    async fetchExercises(page = 1) {
      try {
        const res = await api.get("/exercises", {
          params: {
            search: this.searchQuery,
            per_page: this.perPage,
            page
          }
        });

        this.exercises = res.data.data;
        this.pagination = res.data;
      } catch (e) {
        console.error("Error loading exercises:", e);
      }
    }
  }
};
</script>

<style scoped>
.exercises-wrapper {
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

.name-col { width: 140px; }
.category-col { width: 130px; }
.level-col { width: 110px; }
.duration-col { width: 120px; }
.description-col { width: 260px; }
.image-col { width: 90px; }
.actions-col { width: 100px; }

@media (max-width: 768px) {
  .toolbar {
    align-items: stretch !important;
    flex-direction: column;
  }
}
</style>
