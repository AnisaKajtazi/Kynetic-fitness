<template>
  <div class="meals-wrapper">
    <div class="p-4 shadow rounded">
      <h2 class="text-center mb-4">Meals List</h2>

      <button class="btn btn-primary mb-3" @click="openModal()">Add Meal</button>

      <MealForm
        v-if="showForm"
        :meal="selectedMeal"
        @close="closeForm"
        @saved="fetchMeals"
      />

      <div class="d-flex justify-content-end mb-2">
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
import axios from "axios";
import MealForm from "./MealForm.vue";

const BASE_URL = "http://127.0.0.1:8000/api";

export default {
  components: { MealForm },

  data() {
    return {
      meals: [],
      showForm: false,
      selectedMeal: null,
      searchQuery: "",
      perPage: 10,
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

      await axios.delete(`${BASE_URL}/meals/${id}`, {
        headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
      });

      this.fetchMeals();
    },

    async fetchMeals(page = 1) {
      try {
        const res = await axios.get(`${BASE_URL}/meals`, {
          headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
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
