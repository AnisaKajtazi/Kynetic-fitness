<template>
  <div class="meals-page">

    <!-- HERO SECTION -->
    <div class="hero">
      <h1 class="hero-title">Discover Your Perfect Meal</h1>
      <p class="hero-subtitle">Healthy • Delicious • Personalized for your goals</p>
    </div>

    <div v-if="loading" class="loading">Loading meals...</div>

    <!-- CATEGORY ROWS -->
    <div 
      v-for="(cat, index) in meals"
      :key="cat.category"
      class="category-section"
    >
      <h2 class="category-title">{{ cat.category }}</h2>

      <div class="slider-wrapper">

        <!-- Left arrow -->
        <button 
          class="slide-btn left" 
          @click="scrollLeft(index)"
        >
          ‹
        </button>

        <!-- Scrollable slider -->
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
            <div class="meal-info">
              <h3>{{ meal.name }}</h3>

              <!-- Add to Cart button, only if logged in -->
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

        <!-- Right arrow -->
        <button 
          class="slide-btn right" 
          @click="scrollRight(index)"
        >
          ›
        </button>

      </div>
    </div>

    <!-- POPUP DETAILS -->
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
</template>

<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";

const meals = ref([]);
const loading = ref(false);
const selectedMeal = ref(null);
const sliderRefs = ref([]);
const defaultImage = "https://cdn-icons-png.flaticon.com/512/706/706195.png";

// Kontrollo nëse useri është i kyçur
const isLoggedIn = ref(false);
onMounted(() => {
  isLoggedIn.value = !!localStorage.getItem("token");
  fetchMeals();
});

const fetchMeals = async () => {
  loading.value = true;
  try {
    const res = await axios.get("http://127.0.0.1:8000/api/meals");

    meals.value = Object.keys(res.data).map((category) => ({
      category,
      items: res.data[category].map((meal) => ({
        ...meal,
        category,
      })),
    }));
  } catch (e) {
    console.error("Error loading meals:", e);
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
  sliderRefs.value[index].scrollBy({ left: -300, behavior: "smooth" });
};

const scrollRight = (index) => {
  sliderRefs.value[index].scrollBy({ left: 300, behavior: "smooth" });
};

// ADD TO CART function
const addToCart = async (meal) => {
  if (!isLoggedIn.value) return;
  try {
    await axios.post(
      "http://127.0.0.1:8000/api/add-to-cart",
      { meal_id: meal.MealID },
      {
        headers: {
          Authorization: `Bearer ${localStorage.getItem("token")}`,
        },
      }
    );
    alert(`${meal.name} added to cart!`);
  } catch (err) {
    console.error("Error adding to cart:", err);
    alert("Failed to add to cart");
  }
};
</script>

<style scoped>
/* ----- GENERAL PAGE ----- */
.meals-page {
  padding: 60px 20px;  /* Moved down for navbar */
  max-width: 1300px;
  margin: auto;
  color: #222;
}

/* ----- HERO SECTION ----- */
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

/* ----- CATEGORY SECTION ----- */
.category-section {
  margin-bottom: 50px;
}

.category-title {
  font-size: 22px;
  margin-bottom: 12px;
  font-weight: 700;
}

/* ----- SLIDER STYLING ----- */
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

/* ----- MEAL CARD ----- */
.meal-card {
  min-width: 230px;
  height: 150px;
  border-radius: 12px;
  overflow: hidden;
  position: relative;
  cursor: pointer;
  flex-shrink: 0;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.meal-card:hover {
  transform: scale(1.07);
  box-shadow: 0 6px 18px rgba(0, 0, 0, 0.25);
}

.meal-card img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.meal-info {
  position: absolute;
  bottom: 0;
  padding: 10px;
  background: linear-gradient(to top, rgba(0,0,0,0.6), transparent);
  color: white;
  width: 100%;
}

.add-to-cart-btn {
  margin-top: 6px;
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

.login-msg {
  margin-top: 6px;
  font-size: 13px;
  color: #ddd;
}

/* ----- SLIDER BUTTONS ----- */
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

/* ----- POPUP ----- */
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
