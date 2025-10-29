<template>
  <section class="home">
    <header class="hero" role="banner" aria-label="Fitness hero">
      <div class="hero__overlay">
        <h1 class="hero__title">Achieve Your Fitness Goals</h1>
        <p class="hero__subtitle">
          Track exercises, plan meals, and visualize your progress — all in one place.
        </p>

        <form class="search" role="search" @submit.prevent>
          <input
            type="text"
            placeholder="Search exercises, meals, or trainers..."
            aria-label="Search"
          />
          <button class="btn btn--blue" type="submit">Search</button>
        </form>

        <div class="hero__cta">
          <router-link class="btn btn--primary" to="/exercises">Explore Exercises</router-link>
          <router-link class="btn btn--blue" to="/login">Get Started</router-link>
        </div>
      </div>
    </header>

    
    
    <section class="features" aria-label="Key features">
      <h2 class="section-title text-center">Explore Our Sections</h2>

     
      <article class="feature feature--exercises">
        <div class="feature-inner">
          <div class="slider-wrapper">
            <div class="slider manual-slider">
              <button class="slider-btn prev" @click="prevSlide('exercise')">&#10094;</button>
              <div class="slides-container">
                <div class="slides" :style="{ transform: `translateX(-${exerciseIndex * 100}%)` }">
                  <div v-for="(img, idx) in exerciseImages" :key="idx" class="slide">
                    <img :src="img" :alt="'Exercise ' + (idx+1)" />
                  </div>
                </div>
              </div>
              <button class="slider-btn next" @click="nextSlide('exercise')">&#10095;</button>
            </div>
          </div>
          <div class="sidebar">
            <h3>Push Your Limits!</h3>
            <p>Track your progress daily and discover exercises that challenge and grow your strength.</p>
          </div>
        </div>
      </article>

      
      <article class="feature feature--meals">
        <div class="feature-inner reverse">
          <div class="slider-wrapper">
            <div class="slider manual-slider">
              <button class="slider-btn prev" @click="prevSlide('meal')">&#10094;</button>
              <div class="slides-container">
                <div class="slides" :style="{ transform: `translateX(-${mealIndex * 100}%)` }">
                  <div v-for="(img, idx) in mealImages" :key="idx" class="slide">
                    <img :src="img" :alt="'Meal ' + (idx+1)" />
                  </div>
                </div>
              </div>
              <button class="slider-btn next" @click="nextSlide('meal')">&#10095;</button>
            </div>
          </div>
          <div class="sidebar">
            <h3>Fuel Your Body!</h3>
            <p>Healthy meal plans to complement your workouts and optimize recovery.</p>
          </div>
        </div>
      </article>
    </section>

  
    <section class="cta full-width" aria-label="Call to action">
      <div class="cta-wrapper">
        <div class="cta__content">
          <h2 class="cta__title">Ready to start your transformation?</h2>
          <p class="cta__text">Create your account and save your first plan in minutes.</p>
          <div class="cta__actions">
            <router-link class="btn btn--blue btn--lg" to="/login">Create Account</router-link>
            <router-link class="btn btn--primary" to="/about">Learn More</router-link>
          </div>
        </div>
      </div>
    </section>

   
    <footer class="footer full-width">
      <div class="footer-wrapper">
        <div class="footer__content">
          <div class="footer__col">
            <h4 class="footer__logo">FitnessApp</h4>
            <p class="footer__description">Track, eat, and grow stronger every day.</p>
          </div>
          <div class="footer__col">
            <h4 class="footer__title">Quick Links</h4>
            <ul class="footer__links">
              <li><router-link to="/">Home</router-link></li>
              <li><router-link to="/exercises">Exercises</router-link></li>
              <li><router-link to="/meals">Meals</router-link></li>
            </ul>
          </div>
          <div class="footer__col">
            <h4 class="footer__title">Follow Us</h4>
            <div class="footer__socials">
              <a href="#"><i class="fab fa-facebook"></i></a>
              <a href="#"><i class="fab fa-instagram"></i></a>
              <a href="#"><i class="fab fa-twitter"></i></a>
            </div>
          </div>
        </div>
        <div class="footer__bottom">
          <p>&copy; 2025 FitnessApp. All rights reserved.</p>
        </div>
      </div>
    </footer>
  </section>
</template>

<script setup>
import { ref } from 'vue';
import '@/assets/global.css';

import exercise1 from '@/img/orangesmoothie.jpg';
import exercise2 from '@/img/orangesmoothie.jpg';
import exercise3 from '@/img/orangesmoothie.jpg';
import meal1 from '@/img/orangesmoothie.jpg';
import meal2 from '@/img/bananasmoothie.jpg';
import meal3 from '@/img/dragonfruitsmoothie.jpg';
import meal4 from '@/img/kiwismoothie.jpg';
import meal5 from '@/img/passionfruitsmoothie.jpg';
import meal6 from '@/img/strawberrysmoothie.jpg';

const exerciseImages = [exercise1, exercise2, exercise3];
const mealImages = [meal1, meal2, meal3, meal4, meal5, meal6];

const exerciseIndex = ref(0);
const mealIndex = ref(0);

const nextSlide = (type) => {
  if (type === 'exercise') exerciseIndex.value = (exerciseIndex.value + 1) % exerciseImages.length;
  if (type === 'meal') mealIndex.value = (mealIndex.value + 1) % mealImages.length;
};

const prevSlide = (type) => {
  if (type === 'exercise') exerciseIndex.value = (exerciseIndex.value - 1 + exerciseImages.length) % exerciseImages.length;
  if (type === 'meal') mealIndex.value = (mealIndex.value - 1 + mealImages.length) % mealImages.length;
};
</script>

<style scoped>

.hero {
  position: relative;
  width: 100%;
  min-height: 100vh;
  background: #000 url('@/img/home.jpg') center/cover no-repeat;
  overflow: hidden;
}

.hero__overlay {
  padding: 3rem;
  display: grid;
  place-items: center;
  text-align: center;
  gap: 1.25rem;
  min-height: inherit;
}


.features {
  background: var(--bg-dark);
  padding: 3rem 2rem;
}

.feature-inner {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 2rem;
  margin: 2rem 0;
}

.feature-inner.reverse {
  flex-direction: row-reverse;
}


.slider-wrapper {
  flex: 0 0 320px;
}

.manual-slider {
  position: relative;
  overflow: hidden;
  border-radius: var(--radius-lg);
  height: 480px;
  width: 100%;
  box-shadow: var(--shadow-sm);
}

.slides-container {
  overflow: hidden;
  width: 100%;
}

.slides {
  display: flex;
  transition: transform 0.5s ease-in-out;
}

.slide {
  flex: 0 0 100%;
}

.slides img {
  width: 100%;
  height: 480px;
  object-fit: cover;
  border-radius: var(--radius-lg);
  transition: transform 0.3s ease;
}

.slides img:hover {
  transform: scale(1.05);
}

.slider-btn {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  background: rgba(0,0,0,0.4);
  border: none;
  color: #fff;
  font-size: 2rem;
  padding: 0.5rem 1rem;
  cursor: pointer;
  border-radius: 50%;
  z-index: 10;
}

.slider-btn.prev {
  left: -10px;
}

.slider-btn.next {
  right: -10px;
}


.sidebar {
  flex: 1;
  padding: 2rem;
  background: #f5f5f5;
  border-radius: var(--radius-lg);
  box-shadow: var(--shadow-sm);
}


.cta.full-width {
  width: 100%;
  background: var(--bg-contrast);
  padding: 3rem 0;
  text-align: center;
}

.cta-wrapper {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 2rem;
}

.cta__actions .btn {
  margin: 0.5rem;
}

/* FOOTER */
.footer.full-width {
  width: 100%;
  background: var(--bg-contrast);
  padding: 3rem 0;
  text-align: center;
}

.footer-wrapper {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 2rem;
}

.footer__content {
  display: flex;
  justify-content: space-around;
  flex-wrap: wrap;
  gap: 2rem;
}

.footer__col {
  flex: 1 1 250px;
}


@media (max-width: 768px) {
  .feature-inner {
    flex-direction: column;
    align-items: center;
  }

  .slider-wrapper {
    width: 100%;
  }

  .manual-slider {
    height: 400px;
  }

  .slides img {
    height: 400px;
  }
}
</style>
