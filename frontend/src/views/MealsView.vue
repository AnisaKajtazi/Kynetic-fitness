<template>
  <div class="meals-page">
    <div class="hero">
      <h1 class="hero-title heading-font page-hero-title">Discover Your Perfect Meal</h1>
      <p class="hero-subtitle page-hero-subtitle">Healthy • Delicious • Personalized for your goals</p>
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
  width: 100%;
  max-width: none;
  margin: 0 auto;
  padding: 2rem 1rem 2.5rem;
  color: var(--text-light);
}

.hero {
  text-align: center;
  margin-top: 0;
  margin-bottom: 2.5rem;
}

.category-section {
  margin-bottom: 2rem;
  width: 100%;
}

.category-title {
  font-size: var(--text-xl);
  margin-bottom: 1rem;
  font-weight: 700;
}

.slider-wrapper {
  position: relative;
  width: 100%;
}

.slider {
  display: flex;
  gap: 1rem;
  overflow-x: auto;
  padding: 0.5rem 0;
  scroll-behavior: smooth;
  width: 100%;
}

.slider::-webkit-scrollbar {
  display: none;
}

.meal-card {
  flex: 0 0 calc((100% - 72px) / 5);
  min-width: 370px;
  height: auto;
  border-radius: var(--radius);
  overflow: hidden;
  position: relative;
  flex-shrink: 0;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  background: var(--bg-card);
}

.meal-card:hover {
  transform: scale(1.07);
  box-shadow: 0 6px 18px rgba(0, 0, 0, 0.25);
}

.meal-card img {
  width: 100%;
  height: 220px;
  object-fit: cover;
  display: block;
  cursor: pointer;
}

.meal-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem 0.85rem;
}

.meal-price {
  font-weight: 700;
  font-size: var(--text-base);
  color: var(--accent-lavender);
}

.add-to-cart-btn {
  background: var(--accent-blue);
  padding: 0.70rem 0.85rem;
  border: 2px;
  color: var(--accent-purple);
  border-radius: 50px;
  cursor: pointer;
  font-size: var(--text-md);
  font-weight: 600;
}

.add-to-cart-btn:hover {
  background: #97dffc;
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
  left: 0;
}
.slide-btn.right {
  right: 0;
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
  background: var(--bg-card);
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
  padding: 1.5rem 1.75rem;
  font-size: var(--text-base);
}

.popup-content h2 {
  margin-bottom: 0.75rem;
  font-size: var(--text-xl);
}

.popup-content .desc {
  margin-top: 1rem;
  color: var(--text-muted);
  line-height: 1.7;
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

@media (max-width: 1200px) {
  .meal-card {
    flex-basis: calc((100% - 45px) / 4);
  }
}

@media (max-width: 900px) {
  .meal-card {
    flex-basis: calc((100% - 30px) / 3);
  }
}

@media (max-width: 640px) {
  .meals-page {
    padding: 1rem 0.75rem 1.5rem;
  }

  .meal-card {
    flex-basis: calc((100% - 15px) / 2);
  }
}
</style>
