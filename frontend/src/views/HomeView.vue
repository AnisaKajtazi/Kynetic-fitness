<template>
  <section class="home">
    <header class="hero" role="banner" aria-label="Fitness hero">
      <div class="hero__overlay">
        <h1 class="hero__title">Achieve Your Fitness Goals</h1>
        <p class="hero__subtitle">
          Track exercises, plan meals, and visualize your progress — all in one place.
        </p>

        <form class="search" role="search" @submit.prevent="handleSearch">
          <div class="search-field">
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Search exercises, meals, or trainers..."
              aria-label="Search"
              @keydown.enter="handleSearch"
            />
            <button type="submit" class="search-btn" aria-label="Search">
              <img :src="searchIcon" alt="Search" class="search-icon-img" />
            </button>
          </div>
        </form>

        <div class="hero__cta">
          <router-link class="btn btn--primary btn--lg" to="/exercises">Explore Exercises</router-link>
          <router-link class="btn btn--blue btn--lg" to="/login">Get Started</router-link>
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
          <div class="sidebar slider-box">
            <h3>Push Your Limits!</h3>
            <p>Discover dynamic training sessions designed to build strength, mobility, and confidence. Every workout is crafted to help you lift heavier, move smarter, and stay consistent while tracking real progress.</p>
            <p>From focused strength circuits to recovery-friendly mobility flows, our routines adapt to your fitness level and keep you moving forward. Expect more energy, better posture, and a stronger body with every session.</p>
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
          <div class="sidebar slider-box">
            <h3>Fuel Your Body!</h3>
            <p>Enjoy balanced meal plans that power your workouts, speed recovery, and keep energy high throughout the day. Smart nutrition, delicious recipes, and performance-focused fuel for every goal.</p>
            <p>Personalized macros, easy-to-follow menus, and nutrient-dense meals make healthy eating feel effortless. Whether you're training hard or recovering strong, we help you eat better without sacrificing flavor.</p>
          </div>
        </div>
      </article>
    </section>

   
    <section class="services full-width" aria-label="Our services">
      <div class="services-wrapper">
        <h2 class="section-title text-center">Our Services</h2>
        <div class="services-grid">
          <article v-for="(service, idx) in services" :key="idx" class="service-card">
            <img :src="service.icon" :alt="service.title" class="service-icon"/>
            <h3>{{ service.title }}</h3>
            <p>{{ service.description }}</p>
          </article>
        </div>
      </div>
    </section>

    
    <section id="about-us" ref="aboutSection" class="about full-width" aria-label="About us">
      <div class="about-wrapper">
        <h2 class="section-title text-center">About Us</h2>
        <div class="about-top">
          <div class="about-copy">
            <p class="about-text">
              At Kynetic, we blend science-backed training, expert nutrition, and smart tracking into one seamless fitness experience. Our platform is built for busy people who want real results without guesswork.
            </p>
            <p class="about-text">
              From guided workouts and meal plans to habit-building tools and progress insights, Kynetic helps you move with purpose every day. Your program adapts to your pace, goals, and lifestyle so you can stay motivated from week one.
            </p>
            <p class="about-text">
              Every feature is designed to simplify your journey: intelligent workout scheduling, recovery reminders, clear progress charts, and motivational milestones that keep you engaged. We believe fitness should fit into your life, not interrupt it.
            </p>
            <p class="about-text">
              This is the place where planning, performance, and recovery come together in a clean experience that evolves with you. Your routine becomes easier to follow, your energy stays consistent, and progress feels natural.
            </p>
            <p class="about-text">
              Kynetic keeps every part of your routine connected, so you can focus on getting stronger without extra complexity.
            </p>
          </div>

          <div class="about-highlights">
            <article :class="['about-card', 'about-card--enter-left', { 'about-card--active': aboutVisible }]">
              <span class="about-card-icon">⚡</span>
              <h4>Fast results without burnout</h4>
              <p>Programs that deliver strength, mobility and endurance gains through smart progression, not random workouts.</p>
            </article>
            <article :class="['about-card', 'about-card--enter-right', { 'about-card--active': aboutVisible }]">
              <span class="about-card-icon">🥗</span>
              <h4>Nutrition that feels effortless</h4>
              <p>Simple meal suggestions, quick prep options, and energy-first fuel designed for active lifestyles.</p>
            </article>
            <article :class="['about-card', 'about-card--enter-left', { 'about-card--active': aboutVisible }]">
              <span class="about-card-icon">📈</span>
              <h4>Progress you can see</h4>
              <p>Clear habit tracking, weekly performance summaries, and motivating milestones that keep you moving forward.</p>
            </article>
          </div>
        </div>
      </div>
    </section>

    
    <section class="trainers full-width" aria-label="Our trainers">
      <div class="trainers-wrapper">
        <h2 class="section-title text-center">Meet Our Trainers</h2>
        <div class="trainers-list">
          <article v-for="trainer in trainers" :key="trainer.UserID" class="trainer-card">
            <img :src="getTrainerPhoto(trainer.photo)" :alt="trainer.fullName" class="trainer-photo" />
            <h3>{{ trainer.fullName }}</h3>
            <h4>{{ trainer.focus_area ? trainer.focus_area : 'Personal Trainer' }}</h4>

            <p>
              {{ trainer.description ? trainer.description : 'This trainer has not added a description yet.' }}
            </p>
          </article>
        </div>
      </div>
    </section>

    
<section class="testimonials full-width" aria-label="Testimonials">
      <div class="testimonials-wrapper">
        <h2 class="section-title text-center">What Our Users Say</h2>
        <p class="testimonial-subtitle text-center">Real progress stories from members who built strength, stamina, and confidence with our training and meal plans.</p>
        <div class="testimonials-grid">
          <div class="testimonial-card" v-for="(testi, idx) in testimonials" :key="idx">
            <div class="testimonial-photo-wrapper">
              <img :src="testi.photo" :alt="testi.name" class="testimonial-photo" />
            </div>
            <p class="testimonial-text">"{{ testi.feedback }}"</p>
            <h4 class="testimonial-name">{{ testi.name }}</h4>
            <span class="testimonial-role">{{ testi.role }}</span>
          </div>
        </div>
      </div>
    </section>

  
<section class="cta full-width" aria-label="Call to action">
  <div class="cta-overlay"></div>
  <div class="cta-inner">
    <h2 class="cta-title">Transform Your Life Today</h2>
    <p class="cta-desc">
      Join thousands of fitness enthusiasts achieving their goals every day. 
      Discover personalized workouts, nutrition plans, and progress tracking — 
      all in one inspiring platform.
    </p>
    <div class="cta-buttons">
      <router-link class="btn btn--primary btn--lg" to="/login">Start Now</router-link>
      <router-link class="btn btn--outline btn--lg" to="/about">Learn More</router-link>
    </div>
  </div>
</section>


<footer class="footer full-width">
  <div class="footer-wrapper">
    <div class="footer-columns">
      
      
      <div class="footer-col brand">
        <h3 class="footer-logo">Kynetic</h3>
        <p>
          Your trusted fitness companion — combining <strong>training</strong>,
          <strong>nutrition</strong>, and <strong>motivation</strong> to help
          you reach your full potential.
        </p>
      </div>

      
      <div class="footer-col">
        <h4>Explore</h4>
        <ul>
          <li><router-link to="/">Home</router-link></li>
          <li><router-link to="/exercises">Exercises</router-link></li>
          <li><router-link to="/meals">Meals</router-link></li>
          <li><router-link to="/about">About</router-link></li>
        </ul>
      </div>

    
      <div class="footer-col">
        <h4>Support</h4>
        <ul>
          <li><router-link to="/contact">Contact</router-link></li>
          <li><router-link to="/faq">FAQ</router-link></li>
          <li><router-link to="/privacy">Privacy Policy</router-link></li>
        </ul>
      </div>

      
      <div class="footer-col socials">
        <h4>Connect with Us</h4>
        <ul class="social-links">
          <li>
            <a href="mailto:support@kyneticfit.com">
              <i class="fas fa-envelope"></i> support@kyneticfit.com
            </a>
          </li>
          <li>
            <a href="https://www.linkedin.com/" target="_blank">
              <i class="fab fa-linkedin"></i> LinkedIn
            </a>
          </li>
          <li>
            <a href="https://www.instagram.com/" target="_blank">
              <i class="fab fa-instagram"></i> Instagram
            </a>
          </li>
          <li>
            <a href="https://www.youtube.com/" target="_blank">
              <i class="fab fa-youtube"></i> YouTube
            </a>
          </li>
          <li>
            <a href="https://x.com/" target="_blank">
              <i class="fab fa-x-twitter"></i> X (Twitter)
            </a>
          </li>
          <li>
            <a href="https://facebook.com/" target="_blank">
              <i class="fab fa-facebook"></i> Facebook
            </a>
          </li>
        </ul>
      </div>
    </div>

    <div class="footer-bottom">
      <p>
        &copy; 2025 <strong>Kynetic</strong>. Designed with passion for your fitness journey.
      </p>
    </div>
  </div>
</footer>



  </section>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';
import { useRouter } from 'vue-router';
import api from '@/services/axios';
import '@/assets/global.css';
import searchIcon from '@/icons/search.png';

import exercise1 from '@/img/exercise1.jpg';
import exercise2 from '@/img/exercise2.jpg';
import exercise3 from '@/img/exercise3.jpg';
import exercise4 from '@/img/exercise4.jpg';
import meal1 from '@/img/orangesmoothie.jpg';
import meal2 from '@/img/bananasmoothie.jpg';
import meal3 from '@/img/dragonfruitsmoothie.jpg';
import meal4 from '@/img/kiwismoothie.jpg';
import meal5 from '@/img/passionfruitsmoothie.jpg';
import meal6 from '@/img/strawberrysmoothie.jpg';

import user1 from '@/img/user1.jpg';
import user2 from '@/img/user2.jpg';
import user3 from '@/img/user3.jpg';

import service1 from '@/img/PersonalTraining.jpg';
import service2 from '@/img/passionfruitsmoothie.jpg';
import service3 from '@/img/ProgressTracking.jpg';
import service4 from '@/img/chat.jpg';



const exerciseImages = [exercise1, exercise2, exercise3,exercise4];
const mealImages = [meal1, meal2, meal3, meal4, meal5, meal6];

const exerciseIndex = ref(0);
const mealIndex = ref(0);
const searchQuery = ref('');
const router = useRouter();
const aboutSection = ref(null);
const aboutVisible = ref(false);
const trainers = ref([]);

const handleSearch = async () => {
  if (!searchQuery.value.trim()) return;

  try {
    const response = await api.get('/search', {
      params: { q: searchQuery.value }
    });

    const { type, item } = response.data;

    if (type === 'exercise' && item) {
      router.push(`/exercises?id=${item.ExerciseID || item.id}`).catch(() => {});
    } else if (type === 'meal' && item) {
      router.push(`/meals?id=${item.MealID || item.id}`).catch(() => {});
    } else if (type === 'trainer' && item) {
      router.push(`/trainers-browse?id=${item.UserID || item.id}`).catch(() => {});
    } else {
      alert('No results found. Try searching for exercises, meals, or trainers.');
    }

    searchQuery.value = '';
  } catch (error) {
    console.error('Search error:', error);
    alert('Search failed. Please try again.');
  }
};

const fetchTrainers = async () => {
  try {
    const response = await api.get('/trainers');
    trainers.value = response.data.map((trainer) => ({
      ...trainer,
      fullName: `${trainer.name} ${trainer.surname}`.trim(),
    }));
  } catch (error) {
    console.error('Unable to load trainers:', error);
  }
};

const getTrainerPhoto = (photo) => {
  if (!photo) {
    return 'https://via.placeholder.com/360x320?text=Trainer+Photo';
  }

  return `${api.defaults.baseURL.replace(/\/api\/?$/, '')}/uploads/trainers/${photo}`;
};


const nextSlide = (type) => {
  if (type === 'exercise') exerciseIndex.value = (exerciseIndex.value + 1) % exerciseImages.length;
  if (type === 'meal') mealIndex.value = (mealIndex.value + 1) % mealImages.length;
};

const prevSlide = (type) => {
  if (type === 'exercise') exerciseIndex.value = (exerciseIndex.value - 1 + exerciseImages.length) % exerciseImages.length;
  if (type === 'meal') mealIndex.value = (mealIndex.value - 1 + mealImages.length) % mealImages.length;
};

let observer;

onMounted(() => {
  observer = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (entry.target === aboutSection.value) {
        aboutVisible.value = entry.isIntersecting && entry.intersectionRatio >= 0.5;
      }
    });
  }, {
    threshold: [0.5],
  });

  if (aboutSection.value) {
    observer.observe(aboutSection.value);
  }

  fetchTrainers();
});

onBeforeUnmount(() => {
  if (observer && aboutSection.value) {
    observer.unobserve(aboutSection.value);
  }
});

const services = [
  { icon: service1, title: "Personal Training", description: "Customized workouts for your goals." },
  { icon: service2, title: "Nutrition Plans", description: "Healthy meal plans to complement exercises." },
  { icon: service3, title: "Progress Tracking", description: "Visualize your growth and milestones." },
  { icon: service4, title: "Communication With Our Trainers", description: "Chat with our trainers for any help you may need." },
];

const testimonials = [
  { name: "Alex", role: "Strength Athlete", feedback: "With Kynetic, I finally kept a workout routine and gained real muscle in 10 weeks. The planner made it easy to train smarter, not harder.", photo: user1 },
  { name: "Maria", role: "Busy Professional", feedback: "The meal plans are a game-changer — quick, nutritious, and tailored to fatigue-free training days. I feel fitter and more energized.", photo: user2 },
  { name: "John", role: "Weekend Warrior", feedback: "I used to skip gym days. Now I stay consistent, track progress clearly, and actually enjoy the results.", photo: user3 },
];

</script>

<style scoped>
.home {
  width: 100%;
  margin: 0;
  padding: 0;
}

.hero {
  position: relative;
  width: 100%;
  min-height: 100vh;
  background: #000 url('@/img/home.jpg') center/cover no-repeat;
  overflow: hidden;
}
.hero__overlay { 
  padding: 4rem 2rem;
  display: grid;
  place-items: center;
  text-align: center;
  gap: 1.75rem;
  min-height: inherit; 
}
.hero__title {
  font-size: clamp(2.2rem, 4vw, 3.8rem);
  line-height: 1.02;
  letter-spacing: -0.04em;
  max-width: 900px;
  margin: 0 auto;
  font-family: Lucida Handwriting;
  font-weight: 800;
  color: #97dffc;
}
.hero__subtitle {
  font-size: 1.32rem;
  max-width: 760px;
  margin: 0 auto;
  color: #97dffc;
  line-height: 1.85;
}
.search {
  width: 100%;
  max-width: 760px;
  margin: 0 auto;
}
.search-field {
  position: relative;
  width: 100%;
}
.search-field input {
  width: 100%;
  min-height: 60px;
  padding: 1.3rem 4.5rem 1.3rem 1.5rem;
  border-radius: 999px;
  border: none;
  background: rgba(255, 255, 255, 0.96);
  color: var(--bg-card);
  font-size: 1.3rem;
  box-shadow: 0 20px 50px rgba(0, 0, 0, 0.18);
}
.search-field input::placeholder {
  color: #6b7280;
}
.search-btn {
  position: absolute;
  right: 0.75rem;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  cursor: pointer;
  padding: 0.5rem 1rem;
  transition: transform 0.2s ease;
  display: flex;
  align-items: center;
  justify-content: center;
}
.search-icon-img {
  width: 22px;
  height: 22px;
  opacity: 0.7;
  transition: opacity 0.2s ease;
}
.search-btn:hover .search-icon-img {
  opacity: 1;
}
.search-btn:hover {
  transform: translateY(-50%) scale(1.1);
}
.search-btn:active {
  transform: translateY(-50%) scale(0.95);
}
.search-field input:focus {
  outline: none;
  box-shadow: 0 24px 60px rgba(0, 0, 0, 0.25);
}
.hero__cta {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  gap: 1rem;
  margin-top: 1.5rem;
}
.hero__cta .btn {
  min-width: 190px;
  padding: 1rem 1.8rem;
  font-size: 1rem;
}


.features { 
  background: var(--bg-dark); 
  padding: 3rem 8rem; 
  width: 100%;
  margin: 0;
}

.feature {
  width: 100%;
  margin: 0;
}

.feature-inner { 
  display: flex; 
  align-items: center; 
  justify-content: space-between; 
  gap: 2.5rem; 
  margin: 2rem 0; 
  padding: 0 2rem;
}
.feature-inner.reverse { 
  flex-direction: 
  row-reverse; 
}

.section-title {
  padding: 0 2rem;
  margin: 2rem 0;
  color: #97dffc;
}
.slider-wrapper { 
  flex: 0 0 320px; 
}
.manual-slider { 
  position: relative; 
  overflow: hidden; 
  border-radius: var(--radius-lg); 
  height: 480px; 
  width: 500px; 
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
  border: none; color: var(--text-strong); 
  font-size: 2rem; 
  padding: 0.5rem 1rem; 
  cursor: pointer; 
  border-radius: 50%; 
  z-index: 10; 
}
.slider-btn.prev { 
  left: 10px; 
}
.slider-btn.next { 
  right: 10px; 
}


.sidebar.slider-box {
  background: rgba(25, 28, 34, 0.96);
  border-radius: 24px;
  padding: 2.2rem 2.5rem;
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-left: 4px solid #97dffc;
  box-shadow: 0 18px 40px rgba(0, 0, 0, 0.28);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  position: relative;
  overflow: hidden;
}

.sidebar.slider-box:hover {
  transform: translateY(-4px);
  box-shadow: 0 24px 45px rgba(0, 0, 0, 0.32);
}

.sidebar.slider-box h3 {
  font-size: 1.8rem;
  font-weight: 700;
  color: #97dffc;
  margin-bottom: 1rem;
  position: relative;
  z-index: 1;
}

.sidebar.slider-box p {
  font-size: 1.05rem;
  color: var(--text-strong)fffff;
  line-height: 2.6;
  position: relative;
  z-index: 1;
}



.services-wrapper  { 
  max-width: 100%; 
  margin: 0; 
  padding: 3rem 2rem; 
}
.services-grid { 
  display: grid; 
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); 
  gap: 3rem; 
}
.service-card { 
  background: var(--bg-card); 
  padding: 1.5rem; 
  border-radius: var(--radius-lg); 
  text-align: center; 
  box-shadow: var(--shadow-sm); 
}
.service-icon { 
  width: 150px; 
  height: 150px; 
  object-fit: cover; 
  margin-bottom: 1rem; 
  border-radius: var(--radius-lg); 
}


.about-wrapper { 
  max-width: 85%; 
  margin: 0 auto; 
  padding: 4rem 1.5rem; 
  text-align: center;
  background: rgba(12, 14, 18, 0.96);
  border-radius: 32px;
  box-shadow: 0 18px 50px rgba(0, 0, 0, 0.28);
  border: 1px solid rgba(255, 255, 255, 0.08);
}
.about-top {
  display: grid;
  grid-template-columns: minmax(0, 1.55fr) minmax(300px, 1fr);
  gap: 2rem;
  align-items: start;
  margin-top: 2rem;
}
.about-copy {
  text-align: left;
}
.about-text { 
  font-size: 1.05rem; 
  line-height: 2.8; 
  margin-bottom: 2.35rem; 
  color: #d7d9df;
}
.about-highlights {
  display: grid;
  gap: 1rem;
}
.about-card {
  width: 100%;
  min-height: 180px;
  background: rgba(255, 255, 255, 0.04);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-left: 4px solid #97dffc;
  padding: 1.8rem;
  border-radius: 20px;
  box-shadow: 0 14px 30px rgba(0, 0, 0, 0.15);
}
.about-card-icon {
  display: inline-flex;
  width: 44px;
  height: 44px;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  background: rgba(79, 141, 255, 0.15);
  color: #4f8dff;
  font-size: 1.2rem;
  margin-bottom: 1rem;
}
.about-card h4 {
  font-size: 1.1rem;
  margin-bottom: 0.75rem;
  color: var(--text-strong)fff;
}
.about-card p {
  color: #c7cad0;
  line-height: 1.7;
}
.about-card {
  width: 100%;
  min-height: 130px;
  background: rgba(255, 255, 255, 0.04);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-left: 4px solid #97dffc;
  padding: 1.4rem;
  border-radius: 20px;
  box-shadow: 0 14px 30px rgba(0, 0, 0, 0.15);
  opacity: 0;
  transform: translateX(0);
}
.about-card--enter-left.about-card--active {
  animation: slideInLeft 1.2s ease-out both;
}
.about-card--enter-right.about-card--active {
  animation: slideInRight 1.2s ease-out both;
}
.about-card:nth-child(1) {
  animation-delay: 0.08s;
}
.about-card:nth-child(2) {
  animation-delay: 0.16s;
}
.about-card:nth-child(3) {
  animation-delay: 0.24s;
}
@keyframes slideInLeft {
  from {
    opacity: 0;
    transform: translateX(-80px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}
@keyframes slideInRight {
  from {
    opacity: 0;
    transform: translateX(80px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}


.trainers-wrapper { 
  max-width: 100%; 
  margin: 0;
  padding: 3rem 2rem; 
  text-align: center; 
}
.trainers-list {
  display: flex;
  gap: 1.75rem;
  overflow-x: auto;
  padding-bottom: 1rem;
  scroll-snap-type: x mandatory;
  margin-top: 2rem;
}
.trainers-list::-webkit-scrollbar {
  height: 10px;
}
.trainers-list::-webkit-scrollbar-thumb {
  background: rgba(255, 255, 255, 0.18);
  border-radius: 999px;
}
.trainers-list::-webkit-scrollbar-track {
  background: transparent;
}
.trainer-card {
  flex: 0 0 320px;
  min-width: 320px;
  background: var(--bg-card); 
  padding: 1.75rem; 
  border-radius: var(--radius-lg); 
  box-shadow: var(--shadow-sm);
  scroll-snap-align: start;
  min-height: 420px;
}
.trainer-photo {
  width: 100%; 
  height: 260px; 
  object-fit: cover; 
  border-radius: var(--radius-lg); 
  margin-bottom: 1rem;
}
.trainer-photo {
  width: 100%; 
  height: 260px; 
  object-fit: cover; 
  border-radius: var(--radius-lg); 
  margin-bottom: 1rem;
}
@media (max-width: 900px) {
  .trainers-list {
    flex-wrap: nowrap;
  }
  .trainer-card {
    min-width: 280px;
  }
}


.testimonials-wrapper { 
  max-width: 100%; 
  margin: 0; 
  padding: 3rem 2rem; 
  text-align: center; 
}
.testimonials-grid { 
  display: grid; 
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); 
  gap: 2rem; 
}
.testimonial-card { 
  background: linear-gradient(180deg, rgba(15, 23, 42, 0.94), rgba(9, 12, 20, 0.98)); 
  border: 1px solid rgba(79, 141, 255, 0.18);
  padding: 2rem; 
  border-radius: 28px;
  box-shadow: 0 18px 45px rgba(0,0,0,0.25);
  font-style: normal; 
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1.25rem;
}
.testimonial-photo-wrapper { 
  display: flex; 
  justify-content: center; 
  margin-bottom: 0.5rem; 
}
.testimonial-photo { 
  width: 86px; 
  height: 86px; 
  border-radius: 50%; 
  object-fit: cover; 
  border: 3px solid var(--accent-blue); 
  box-shadow: 0 10px 30px rgba(0,0,0,0.25); 
  transition: transform 0.3s ease; 
}
.testimonial-photo:hover { 
  transform: scale(1.05); 
}
.testimonial-text {
  font-size: 1rem;
  color: #e2e8f0;
  line-height: 1.8;
  text-align: center;
}
.testimonial-name {
  color: var(--text-strong)fff;
  font-weight: 700;
  margin: 0;
}
.testimonial-role {
  color: #93c5fd;
  font-size: 0.95rem;
}
.testimonial-subtitle {
  max-width: 760px;
  margin: 0 auto 1.75rem;
  color: #a5b4fc;
  font-size: 1.05rem;
  line-height: 1.8;
}




.cta.full-width {
  background: #000 url('@/img/backup.jpg') center/cover no-repeat;
  position: relative;
  width: 100%;
  min-height: 80vh;
  overflow: hidden;
  display: flex;
  align-items: center;
  justify-content: center;
}

.cta-overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  backdrop-filter: blur(2px);
}

.cta-inner {
  position: relative;
  z-index: 2;
  max-width: 800px;
  text-align: center;
  color: var(--text-strong);
  padding: 2rem;
}

.cta-title {
  font-size: 2.8rem;
  font-weight: 800;
  margin-bottom: 1rem;
  letter-spacing: 1px;
  text-shadow: 0 2px 15px rgba(167, 165, 165, 0.6);
}

.cta-desc {
  font-size: 1.15rem;
  line-height: 1.7;
  margin-bottom: 2rem;
  color: #e5e7eb;
}

.cta-buttons {
  display: flex;
  justify-content: center;
  gap: 1.5rem;
}

.btn--primary {
  background: linear-gradient(135deg, var(--accent-blue), var(--accent-blue));
  color: var(--text-strong);
  border: none;
  padding: 0.8rem 1.8rem;
  font-size: 1rem;
  border-radius: 50px;
  transition: all 0.3s ease;
}

.btn--primary:hover {
  background: linear-gradient(135deg, var(--accent-blue), var(--accent-blue));
  transform: scale(1.05);
}

.btn--outline {
  background: transparent;
  color: var(--text-strong);
  border: 2px solid rgba(255,255,255,0.55);
  padding: 0.8rem 1.8rem;
  border-radius: 50px;
  font-size: 1rem;
  transition: all 0.3s ease;
}

.btn--outline:hover {
  background: var(--bg-card);
  color: var(--accent-blue);
  transform: scale(1.05);
}


.footer {
  background: #0d1117;
  color: #cbd5e1;
  padding: 3rem 1.5rem 1rem;
  width: 100%;
  margin: 0;
}

.footer-wrapper {
  max-width: 100%;
  margin: 0;
  padding: 0 2rem;
}

.footer-columns {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
  gap: 2rem;
  margin-bottom: 2rem;
}

.footer-col h4 {
  font-size: 1.1rem;
  color: var(--text-strong);
  margin-bottom: 1rem;
}

.footer-col ul {
  list-style: none;
  padding: 0;
  margin: 0;
}

.footer-col ul li {
  margin-bottom: 0.6rem;
}

.footer-col ul li a {
  color: #cbd5e1;
  text-decoration: none;
  transition: color 0.3s ease;
}


.footer-logo {
  font-size: 1.8rem;
  font-weight: 700;
  color: var(--accent-blue);
  margin-bottom: 1rem;
}


.footer-bottom {
  border-top: 1px solid var(--bg-card);
  padding-top: 1rem;
  text-align: center;
  font-size: 0.9rem;
  color: #94a3b8;
}


@media (max-width: 768px) {
  .cta-title {
    font-size: 2rem;
  }

  .cta-desc {
    font-size: 1rem;
  }

  .cta-buttons {
    flex-direction: column;
  }

  .btn--primary,
  .btn--outline {
    width: 100%;
  }
}

@media (max-width: 768px) {
  .cta-content {
    flex-direction: column;
    text-align: center;
  }

  .cta-image img {
    max-width: 300px;
  }

  .footer-columns {
    flex-direction: column;
    text-align: center;
  }

  .social-icons {
    justify-content: center;
  }
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
  .services-grid, .trainers-grid, .testimonials-grid { 
    grid-template-columns: 1fr; 
  }
}
</style>
