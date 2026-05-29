<template>
  <div class="meals-wrapper">
    <div class="admin-panel p-4 shadow rounded">
      <h2 class="text-center mb-4">Meals List</h2>

      <button class="btn btn-primary mb-3" @click="openModal()">Add Meal</button>

      <MealForm
        v-if="showForm"
        :meal="selectedMeal"
        @close="closeForm"
        @saved="fetchMeals"
      />

      <div class="toolbar d-flex justify-content-between align-items-center mb-3">
        <span class="table-count">{{ pagination ? pagination.total : meals.length }} meals</span>
        <input
          type="text"
          v-model="searchQuery"
          @input="fetchMeals"
          class="form-control w-50"
          placeholder="Search by meal name..."
        />
      </div>

      <div class="table-responsive mt-2">
        <table class="table table-striped table-bordered align-middle text-center">
          <thead class="table-dark">
            <tr>
              <th>ID</th>
              <th>Name</th>
              <th>Description</th>
              <th>Category</th>
              <th>Image</th>
              <th>Price</th>
              <th>Calories</th>
              <th>Fitness Goal</th>
              <th>Activity Level</th>
              <th>Focus Area</th>
              <th>Training Days</th>
              <th>Actions</th>
            </tr>
          </thead>

          <tbody>
            <tr v-for="m in meals" :key="m.MealID">
              <td>{{ m.MealID }}</td>
              <td>{{ m.name }}</td>
              <td>{{ m.description }}</td>
              <td>{{ m.category }}</td>
              <td>
               <img 
                    v-if="m.image && m.category"
                    :src="`http://127.0.0.1:8000/uploads/${m.category}/${m.image}`" 
                    width="60" 
                    />
              </td>
              <td>{{ m.price }}</td>
              <td>{{ m.calories }}</td>
              <td>{{ m.fitness_goal }}</td>
              <td>{{ m.activity_level }}</td>
              <td>{{ m.focus_area }}</td>
              <td>{{ m.training_days }}</td>
              <td>
                <button @click="editMeal(m)" class="btn btn-warning btn-sm me-2">
                  Edit
                </button>
                <button @click="deleteMeal(m.MealID)" class="btn btn-danger btn-sm">
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
          @click="fetchMeals(pagination.current_page - 1)"
        >
          Previous
        </button>

        <span class="mx-2">{{ pagination.current_page }} of {{ pagination.last_page }}</span>

        <button 
          class="btn btn-secondary btn-sm ms-2" 
          :disabled="!pagination.next_page_url"
          @click="fetchMeals(pagination.current_page + 1)"
        >
          Next
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import api from "@/services/axios";
import MealForm from "./MealForm.vue";

export default {
  components: { MealForm },

  data() {
    return {
      meals: [],
      showForm: false,
      selectedMeal: null,
      searchQuery: "",
      perPage: 15,
      pagination: null
    };
  },

  mounted() {
    this.fetchMeals();
  },

  methods: {
    openModal() {
      this.selectedMeal = null;
      this.showForm = true;
    },

    editMeal(meal) {
      this.selectedMeal = { ...meal };
      this.showForm = true;
    },

    closeForm() {
      this.showForm = false;
    },

    async deleteMeal(id) {
      if (!confirm("Delete this meal?")) return;

      await api.delete(`/meals/${id}`);

      this.fetchMeals();
    },

    async fetchMeals(page = 1) {
      try {
        const res = await api.get("/meals", {
          params: {
            search: this.searchQuery,
            per_page: this.perPage,
            page
          }
        });

        this.meals = res.data.data;
        this.pagination = res.data;
      } catch (e) {
        console.error("Error loading meals:", e);
      }
    }
  }
};
</script>

<style scoped>
.meals-wrapper {
  width: 100%;
  padding: 1rem 0;
  color: var(--text-light);
}

.admin-panel {
  width: 100%;
  max-width: none;
  background: var(--bg-card);
  border: 1px solid var(--border-dark);
  border-radius: var(--radius);
  box-shadow: var(--shadow-md);
}

.admin-panel h2 {
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
  min-width: 1500px;
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
