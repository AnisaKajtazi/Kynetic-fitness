<template>
  <div class="meals-wrapper">
    <div class="admin-panel admin-table-panel p-4 shadow rounded">
      <div class="admin-table-header">
        <div class="admin-table-title-block">
          <h2>Meals List</h2>
        </div>

        <button class="btn btn-primary admin-create-btn" @click="openModal()">Create Meal</button>
      </div>

      <MealForm
        v-if="showForm"
        :meal="selectedMeal"
        @close="closeForm"
        @saved="fetchMeals"
      />

      <div class="toolbar admin-table-toolbar admin-table-toolbar--meta d-flex justify-content-between align-items-center mb-3">
        <span class="table-count">{{ pagination ? pagination.total : meals.length }} meals</span>
        <input
          type="text"
          v-model="searchQuery"
          @input="fetchMeals"
          class="form-control admin-search-input"
          placeholder="Search by meal name..."
        />
      </div>

      <div class="table-responsive admin-table-shell mt-2">
        <table class="table admin-table table-striped table-bordered align-middle text-center">
          <thead class="table-dark">
            <tr>
              <th class="id-col">ID</th>
              <th class="name-col">Name</th>
              <th class="description-col">Description</th>
              <th class="category-col">Category</th>
              <th class="image-col">Image</th>
              <th class="price-col">Price</th>
              <th class="calories-col">Calories</th>
              <th class="goal-col">Fitness Goal</th>
              <th class="activity-col">Activity Level</th>
              <th class="focus-col">Focus Area</th>
              <th class="training-col">Training Days</th>
              <th class="actions-col">Actions</th>
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
              <td>
                <div class="admin-money-field">
                  <span class="currency-prefix">€</span>
                  <input
                    type="number"
                    class="admin-inline-input admin-inline-input--wide"
                    :value="formatMoneyInput(m.price)"
                    min="0"
                    max="9999.99"
                    step="0.01"
                    inputmode="decimal"
                    @input="handlePriceInput(m, $event)"
                    @change="saveMealField(m, 'price')"
                    @blur="saveMealField(m, 'price')"
                  />
                </div>
              </td>
              <td>
                <input
                  type="number"
                  class="admin-inline-input"
                  :value="m.calories ?? 0"
                  min="1"
                  max="5000"
                  step="1"
                  inputmode="numeric"
                  @keydown="blockIntegerKeys"
                  @input="handleCaloriesInput(m, $event)"
                  @change="saveMealField(m, 'calories')"
                  @blur="saveMealField(m, 'calories')"
                />
              </td>
              <td>{{ m.fitness_goal }}</td>
              <td>
                <span
                  v-if="m.activity_level"
                  :class="['admin-badge', activityBadgeClass(m.activity_level)]"
                >
                  {{ activityInitial(m.activity_level) }}
                </span>
                <span v-else class="text-muted">—</span>
              </td>
              <td>{{ m.focus_area }}</td>
              <td>
                <input
                  type="number"
                  class="admin-inline-input"
                  :value="m.training_days ?? 0"
                  min="0"
                  max="7"
                  step="1"
                  inputmode="numeric"
                  @keydown="blockIntegerKeys"
                  @input="handleTrainingDaysInput(m, $event)"
                  @change="saveMealField(m, 'training_days')"
                  @blur="saveMealField(m, 'training_days')"
                />
              </td>
              <td class="actions-col">
                <div class="admin-actions">
                  <AdminActionButton variant="edit" title="Edit meal" @click="editMeal(m)" />
                  <AdminActionButton variant="delete" title="Delete meal" @click="deleteMeal(m.MealID)" />
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
import AdminActionButton from "@/components/AdminActionButton.vue";

export default {
  components: { MealForm, AdminActionButton },

  data() {
    return {
      meals: [],
      showForm: false,
      selectedMeal: null,
      searchQuery: "",
      perPage: 15,
      pagination: null,
      savingMealFields: new Set()
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

    clampInteger(value, min, max) {
      const parsed = Number.parseInt(value, 10);
      if (Number.isNaN(parsed)) return min;
      return Math.min(max, Math.max(min, parsed));
    },

    clampDecimal(value, min, max) {
      const parsed = Number.parseFloat(value);
      if (Number.isNaN(parsed)) return min;
      return Math.min(max, Math.max(min, parsed));
    },

    blockIntegerKeys(event) {
      const blocked = ["e", "E", "+", "-"];
      if (blocked.includes(event.key)) {
        event.preventDefault();
      }
    },

    activityInitial(value) {
      const map = { low: "L", medium: "M", high: "H" };
      return map[value] || value;
    },

    activityBadgeClass(value) {
      const map = { low: "admin-badge--low", medium: "admin-badge--medium", high: "admin-badge--high" };
      return map[value] || "admin-badge--muted";
    },

    formatMoneyInput(value) {
      return value === null || value === undefined || value === "" ? "" : Number(value).toFixed(2);
    },

    handlePriceInput(meal, event) {
      const clamped = this.clampDecimal(event.target.value, 0, 9999.99);
      meal.price = clamped;
      event.target.value = Number(clamped).toFixed(2);
    },

    handleCaloriesInput(meal, event) {
      const clamped = this.clampInteger(event.target.value, 1, 5000);
      meal.calories = clamped;
      event.target.value = clamped;
    },

    handleTrainingDaysInput(meal, event) {
      const clamped = this.clampInteger(event.target.value, 0, 7);
      meal.training_days = clamped;
      event.target.value = clamped;
    },

    async saveMealField(meal, field) {
      const updateKey = `${meal.MealID}:${field}`;
      if (this.savingMealFields.has(updateKey)) return;

      const payload = {
        name: meal.name,
        description: meal.description || "",
        category: meal.category || "",
        price: this.clampDecimal(meal.price, 0, 9999.99),
        calories: this.clampInteger(meal.calories, 1, 5000),
        fitness_goal: meal.fitness_goal || "",
        activity_level: meal.activity_level || "",
        focus_area: meal.focus_area || "",
        training_days: this.clampInteger(meal.training_days, 0, 7)
      };

      meal.price = payload.price;
      meal.calories = payload.calories;
      meal.training_days = payload.training_days;

      this.savingMealFields.add(updateKey);

      try {
        await api.put(`/meals/${meal.MealID}`, payload);
      } catch (error) {
        console.error("Error updating meal field:", error);
      } finally {
        this.savingMealFields.delete(updateKey);
      }
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

.toolbar {
  gap: 1rem;
}

.table-count {
  color: var(--text-muted);
  font-weight: 700;
}

.id-col { width: 75px; }
.name-col { width: 145px; }
.description-col { width: 220px; }
.category-col { width: 130px; }
.image-col { width: 90px; }
.price-col { width: 110px; }
.calories-col { width: 110px; }
.goal-col { width: 130px; }
.activity-col { width: 95px; }
.focus-col { width: 125px; }
.training-col { width: 120px; }
.actions-col { width: 100px; }

.admin-money-field {
  display: inline-flex;
  align-items: center;
  gap: 0.35rem;
  justify-content: center;
}

.currency-prefix {
  color: var(--text-muted);
  font-weight: 800;
}

@media (max-width: 768px) {
  .toolbar {
    align-items: stretch !important;
    flex-direction: column;
  }
}
</style>
