<template>
  <div class="meals-page">
    <div class="hero">
      <h1 class="hero-title">Discover Your Perfect Meal</h1>
      <p class="hero-subtitle">Healthy • Delicious • Personalized for your goals</p>
    </div>

    <div v-if="loading" class="loading">Loading meals...</div>

    <div v-else>
      <div 
        v-for="(cat, index) in meals"
        :key="cat.category"
        class="category-section"
      >
        <h2 class="category-title">{{ cat.category }}</h2>

        <div class="slider-wrapper">

          <button class="slide-btn left" @click="scrollLeft(index)">‹</button>

          <div class="slider" :ref="(el) => sliderRefs[index] = el">
            <div 
              class="meal-card"
              v-for="meal in cat.items"
              :key="meal.MealID"
            >
              <img 
                :src="meal.image ? imageUrl(meal) : defaultImage"
                :alt="meal.name"
                loading="lazy"
                @click="openPopup(meal)"
              />

              <div class="meal-footer">
                <span class="meal-price">
                  {{ meal.price != null ? `$${Number(meal.price).toFixed(2)}` : '—' }}
                </span>

                <button
                  v-if="isLoggedIn"
                  class="add-to-cart-btn"
                  @click.stop="addToCart(meal)"
                >
                  ➕ Add to Cart
                </button>
              </div>

            </div>
          </div>

          <button class="slide-btn right" @click="scrollRight(index)">›</button>

        </div>
      </div>

      <div v-if="selectedMeal" class="popup-overlay" @click.self="selectedMeal = null">
        <div class="popup-card">
          <button class="close-btn" @click="selectedMeal = null">✕</button>

          <img 
            :src="selectedMeal.image ? imageUrl(selectedMeal) : defaultImage"
            :alt="selectedMeal.name"
          />

          <div class="popup-content">
            <h2>{{ selectedMeal.name }}</h2>
            <p><strong>Category:</strong> {{ selectedMeal.category }}</p>
            <p><strong>Calories:</strong> {{ selectedMeal.calories ?? '—' }}</p>
            <p><strong>Focus:</strong> {{ selectedMeal.focus_area ?? '—' }}</p>
            <p class="desc">{{ selectedMeal.description }}</p>
          </div>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed, watchEffect } from "vue";
import axios from "axios";
import { loggedIn } from "../stores/auth";

const meals = ref([]);
const loading = ref(false);
const selectedMeal = ref(null);
const sliderRefs = ref([]);
const defaultImage = "https://cdn-icons-png.flaticon.com/512/706/706195.png";

const isLoggedIn = computed(() => loggedIn.value);

onMounted(() => {
  fetchMeals();
});

const fetchMeals = async () => {
  loading.value = true;
  try {
    const res = await axios.get("http://127.0.0.1:8000/api/meals/all"); // endpoint i ri pa pagination
    console.log("Meals API response:", res.data);

    if (res.data && typeof res.data === "object") {
      meals.value = Object.entries(res.data).map(([category, items]) => ({
        category,
        items: Array.isArray(items)
          ? items.map((meal) => ({
              ...meal,
              meal_id: Number(meal.MealID),
              category,
            }))
          : [],
      }));
    } else {
      meals.value = [];
    }
  } catch (e) {
    console.error("Error loading meals:", e);
    meals.value = [];
  }
  loading.value = false;
};

const imageUrl = (meal) => {
  const cat = encodeURIComponent(meal.category || "");
  const img = encodeURIComponent(meal.image || "");
  return `http://127.0.0.1:8000/uploads/${cat}/${img}`;
};

const openPopup = (meal) => {
  selectedMeal.value = meal;
};

const scrollLeft = (index) => {
  if (sliderRefs.value[index]) {
    sliderRefs.value[index].scrollBy({ left: -300, behavior: "smooth" });
  }
};
const scrollRight = (index) => {
  if (sliderRefs.value[index]) {
    sliderRefs.value[index].scrollBy({ left: 300, behavior: "smooth" });
  }
};

const addToCart = async (meal) => {
  if (!isLoggedIn.value) return;

  const mealId = Number(meal.MealID ?? meal.meal_id);
  if (!mealId) {
    console.error("Meal ID missing", meal);
    alert("Meal ID not found!");
    return;
  }

  const payload = {
    meal_id: mealId,
    item_name: meal.name,
    price: Number(meal.price ?? 0),
    quantity: 1
  };

  try {
    const res = await axios.post(
      "http://127.0.0.1:8000/api/my-cart",
      payload,
      {
        headers: {
          Authorization: `Bearer ${localStorage.getItem("token")}`,
        },
      }
    );
    console.log("Add to cart response:", res.data);
    alert(`${meal.name} added to cart!`);
  } catch (err) {
    console.error("Error adding to cart:", err.response?.data || err);
    alert("Failed to add to cart");
  }
};

watchEffect(() => {
  loggedIn.value = !!localStorage.getItem("token");
});
</script>

<style scoped>
.meals-page {
  padding: 60px 20px;
  max-width: 1300px;
  margin: auto;
  color: #222;
}

.hero {
  text-align: center;
  margin-top: 40px;
  margin-bottom: 50px;
}

.hero-title {
  font-size: 38px;
  font-weight: 800;
  color: #fff;
  font-family: Lucida Handwriting;
}

.hero-subtitle {
  font-size: 18px;
  margin-top: 10px;
  color: #ccc;
}

.category-section {
  margin-bottom: 50px;
}

.category-title {
  font-size: 22px;
  margin-bottom: 12px;
  font-weight: 700;
}

.slider-wrapper {
  position: relative;
}

.slider {
  display: flex;
  gap: 15px;
  overflow-x: auto;
  padding: 10px 0;
  scroll-behavior: smooth;
}

.slider::-webkit-scrollbar {
  display: none;
}

.meal-card {
  min-width: 230px;
  height: auto;
  border-radius: 12px;
  overflow: hidden;
  position: relative;
  flex-shrink: 0;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.meal-card:hover {
  transform: scale(1.07);
  box-shadow: 0 6px 18px rgba(0, 0, 0, 0.25);
}

.meal-card img {
  width: 100%;
  height: 150px;
  object-fit: cover;
  display: block;
}

.meal-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 6px 8px;
}

.meal-price {
  font-weight: 700;
  color: #ff5722;
}

.add-to-cart-btn {
  background: #ff5722;
  padding: 6px 12px;
  border: none;
  color: white;
  border-radius: 6px;
  cursor: pointer;
  font-size: 14px;
}

.add-to-cart-btn:hover {
  background: #e64a19;
}

.slide-btn {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  background: rgba(30,30,30,0.6);
  border: none;
  color: white;
  font-size: 32px;
  width: 40px;
  height: 80px;
  cursor: pointer;
  z-index: 2;
  border-radius: 6px;
  transition: 0.2s;
}

.slide-btn:hover {
  background: rgba(30,30,30,0.8);
}

.slide-btn.left {
  left: -10px;
}
.slide-btn.right {
  right: -10px;
}

.popup-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.65);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 30;
}

.popup-card {
  background: #fff;
  width: 90%;
  max-width: 620px;
  border-radius: 12px;
  overflow: hidden;
  position: relative;
  box-shadow: 0 8px 25px rgba(0,0,0,0.3);
}

.popup-card img {
  width: 100%;
  height: 260px;
  object-fit: cover;
}

.popup-content {
  padding: 20px;
}

.popup-content h2 {
  margin-bottom: 10px;
}

.popup-content .desc {
  margin-top: 15px;
  color: #444;
}

.close-btn {
  position: absolute;
  top: 12px;
  right: 12px;
  background: black;
  color: white;
  border: none;
  font-size: 22px;
  width: 34px;
  height: 34px;
  border-radius: 50%;
  cursor: pointer;
}
</style>
