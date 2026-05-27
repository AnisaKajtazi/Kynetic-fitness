<template>
  <div class="dashboard-meals">
    <h2>Your Personalized Menu</h2>

    <div v-if="loading" class="loading">Loading menu...</div>

    <div v-else>
      <div
        v-for="cat in meals"
        :key="cat.category"
        class="category-section"
      >
        <h3 class="category-title">{{ cat.category }}</h3>

        <div v-if="cat.bestItem" class="menu-row">
          <img
            class="meal-image"
            :src="`http://127.0.0.1:8000/uploads/${cat.category}/${cat.bestItem.image}`"
            alt="Meal Image"
          />

          <div class="meal-left">
            <p class="meal-name">{{ cat.bestItem.name }}</p>
            <p class="meal-desc">{{ cat.bestItem.description }}</p>
          </div>

          <div class="meal-right">
            <p class="meal-price">
              ${{ cat.bestItem.price.toFixed(2) }}
            </p>
          </div>
        </div>

        <p v-else class="no-item">No personalized meal found.</p>
      </div>

     
      <div class="total-price">
        <strong>Total: ${{ totalPrice.toFixed(2) }}</strong>
        <button
          v-if="isLoggedIn && totalPrice > 0"
          class="add-to-cart-btn total-cart-btn"
          @click="addAllToCart"
        >
          ➕ Add All to Cart
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import axios from "axios";
import { loggedIn } from "../stores/auth";

const meals = ref([]);
const loading = ref(true);
const isLoggedIn = computed(() => loggedIn.value);


const user = JSON.parse(localStorage.getItem("user")) || {};

const goal = user.fitness_goal || "";
const activity = user.activity_level || "";
const training = Number(user.training_days || 0);
const focus = user.focus_area || "";


const calculateScore = (meal) => {
  let score = 0;
  if (meal.fitness_goal === goal) score += 3;
  if (meal.activity_level === activity) score += 3;
  if (meal.focus_area === focus) score += 3;
  if (Math.abs(meal.training_days - training) <= 2) score += 2;
  if (goal === "gain muscle" && meal.calories >= 600) score += 1;
  if (goal === "lose weight" && meal.calories <= 400) score += 1;
  return score;
};


const fetchMenu = async () => {
  try {
    const res = await axios.get("http://127.0.0.1:8000/api/meals");
    const data = res.data;

    meals.value = Object.keys(data).map((category) => {
      const items = data[category] || [];

      
      const scored = items
        .map((meal) => ({ ...meal, score: calculateScore(meal) }))
        .sort((a, b) => b.score - a.score);

      const bestItem = scored.length ? scored[0] : null;

      return { category, items: scored, bestItem };
    });
  } catch (err) {
    console.error("Error fetching meals:", err);
  } finally {
    loading.value = false;
  }
};

onMounted(fetchMenu);


const totalPrice = computed(() => {
  return meals.value.reduce((sum, cat) => {
    if (cat.bestItem && typeof cat.bestItem.price === "number") {
      return sum + cat.bestItem.price;
    }
    return sum;
  }, 0);
});


const addAllToCart = async () => {
  if (!isLoggedIn.value) return;

  
  const mealIds = meals.value
    .filter((cat) => cat.bestItem)
    .map((cat) => cat.bestItem.MealID);

  try {
    await axios.post(
      "http://127.0.0.1:8000/api/add-to-cart",
      { meal_ids: mealIds }, // backend duhet të pranojë array
      {
        headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
      }
    );
    alert("All personalized meals added to cart!");
  } catch (err) {
    console.error("Error adding all to cart:", err);
    alert("Failed to add meals to cart");
  }
};
</script>

<style scoped>
.dashboard-meals {
  padding: 1.5rem;
}

.category-section {
  margin-bottom: 2rem;
}

.category-title {
  font-size: 1.4rem;
  font-weight: 700;
  margin-bottom: 0.8rem;
  border-bottom: 1px solid #ccc;
  padding-bottom: 4px;
}

.menu-row {
  display: flex;
  gap: 12px;
  align-items: center;
  padding: 0.5rem 0;
  border-bottom: 1px dashed #eee;
}

.meal-image {
  width: 80px;
  height: 80px;
  object-fit: cover;
  border-radius: 8px;
}

.meal-left {
  flex: 3;
}

.meal-name {
  font-weight: 600;
  font-size: 1rem;
}

.meal-desc {
  font-size: 0.9rem;
  color: #555;
}

.meal-right {
  flex: 1;
  text-align: right;
  font-weight: 500;
  font-size: 1rem;
}

.total-price {
  text-align: right;
  font-size: 1.2rem;
  font-weight: 700;
  margin-top: 1rem;
  border-top: 1px solid #ccc;
  padding-top: 8px;
  color: #2c3e50;
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  align-items: center;
}

.add-to-cart-btn.total-cart-btn {
  background: var(--accent-lavender);
  padding: 6px 16px;
  border: none;
  color: white;
  border-radius: 6px;
  cursor: pointer;
  font-size: 14px;
}

.add-to-cart-btn.total-cart-btn:hover {
  background: #e64a19;
}

.no-item {
  font-style: italic;
  color: #999;
}
</style>
