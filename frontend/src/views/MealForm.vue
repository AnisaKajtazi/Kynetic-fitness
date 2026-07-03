<template>
  <div class="admin-modal-overlay">
    <div class="admin-modal-content">
      <h3 class="mb-3">{{ meal ? "Edit Meal" : "Add Meal" }}</h3>

      <form @submit.prevent="handleSubmit" enctype="multipart/form-data">
        <div class="admin-form-row">

          <div class="admin-form-group">
            <label>Name</label>
            <input
              type="text"
              class="admin-form-control"
              v-model="formData.name"
              required
            />
          </div>

          <div class="admin-form-group">
            <label>Description</label>
            <textarea
              class="admin-form-control"
              v-model="formData.description"
              rows="3"
            ></textarea>
          </div>

          <div class="admin-form-group">
            <label>Category</label>
            <select class="admin-form-control" v-model="formData.category" required>
              <option value="" disabled>Select category</option>
              <option value="Healthy Desserts">Healthy Desserts</option>
              <option value="High Protein Meals">High Protein Meals</option>
              <option value="Low Calorie Meals">Low Calorie Meals</option>
              <option value="Salads">Salads</option>
              <option value="Smoothies & Drinks">Smoothies & Drinks</option>
              <option value="Snacks">Snacks</option>
            </select>
          </div>

          <div class="admin-form-group">
            <label>Image</label>
            <input
              type="file"
              class="admin-form-control"
              accept="image/*"
              @change="handleFileChange"
            />
          </div>

          <div class="admin-form-group">
            <label>Price</label>
            <input
              type="number"
              step="0.01"
              class="admin-form-control"
              v-model="formData.price"
              min="0"
            />
          </div>

          <div class="admin-form-group">
            <label>Calories</label>
            <input
              type="number"
              class="admin-form-control"
              v-model="formData.calories"
              min="1"
              max="5000"
            />
          </div>

          <div class="admin-form-group">
            <label>Fitness Goal</label>
            <select class="admin-form-control" v-model="formData.fitness_goal" required>
              <option value="" disabled>Select goal</option>
              <option value="lose weight">Lose Weight</option>
              <option value="gain muscle">Gain Muscle</option>
              <option value="stay fit">Stay Fit</option>
            </select>
          </div>

          <div class="admin-form-group">
            <label>Activity Level</label>
            <select class="admin-form-control" v-model="formData.activity_level" required>
              <option value="" disabled>Select activity level</option>
              <option value="low">Low</option>
              <option value="medium">Medium</option>
              <option value="high">High</option>
            </select>
          </div>

          <div class="admin-form-group">
            <label>Focus Area</label>
            <select class="admin-form-control" v-model="formData.focus_area" required>
              <option value="" disabled>Select focus area</option>
              <option value="upper body">Upper Body</option>
              <option value="lower body">Lower Body</option>
              <option value="cardio">Cardio</option>
            </select>
          </div>

          <div class="admin-form-group">
            <label>Training Days</label>
            <input
              type="number"
              class="admin-form-control"
              v-model="formData.training_days"
              min="0"
              max="7"
            />
          </div>
        </div>

        <div class="admin-form-actions d-flex justify-content-end mt-4">
          <button type="button" class="btn btn-secondary me-2" @click="$emit('close')">
            Cancel
          </button>
          <button type="submit" class="btn btn-primary">
            {{ meal ? "Update" : "Create" }}
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
  props: {
    meal: Object,
  },

  data() {
    return {
      formData: {
        name: "",
        description: "",
        category: "",
        image: null,
        price: null,
        calories: null,
        fitness_goal: "",
        activity_level: "",
        focus_area: "",
        training_days: null,
      },
    };
  },

  mounted() {
    if (this.meal) {
      this.formData = {
        name: this.meal.name,
        description: this.meal.description,
        category: this.meal.category,
        image: null,
        price: this.meal.price,
        calories: this.meal.calories,
        fitness_goal: this.meal.fitness_goal,
        activity_level: this.meal.activity_level,
        focus_area: this.meal.focus_area,
        training_days: this.meal.training_days,
      };
    }
  },

  methods: {
    handleFileChange(e) {
      this.formData.image = e.target.files[0];
    },

    async handleSubmit() {
      try {
        const data = new FormData();

        Object.keys(this.formData).forEach((key) => {
          const value = this.formData[key];
          if (value !== null && value !== "") {
            data.append(key, value);
          }
        });

        const headers = {
          Authorization: `Bearer ${localStorage.getItem("token")}`,
        };

        if (this.meal && this.meal.MealID) {
          await axios.post(
            `${BASE_URL}/meals/${this.meal.MealID}?_method=PUT`,
            data,
            { headers }
          );
        } else {
          await axios.post(`${BASE_URL}/meals`, data, { headers });
        }

        this.$emit("saved");
        this.$emit("close");
      } catch (err) {
        console.error("Meal save error:", err.response?.data || err);
        showError("Error saving meal.");
      }
    },
  },
};
</script>

<style scoped>
</style>
