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
          <div class="sidebar slider-box">
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
          <div class="sidebar slider-box">
            <h3>Fuel Your Body!</h3>
            <p>Healthy meal plans to complement your workouts and optimize recovery.</p>
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

    
    <section id="about-us" class="about full-width" aria-label="About us">
      <div class="about-wrapper">
        <h2 class="section-title text-center">About Us</h2>
        <p class="about-text">
          At Kynetic, we believe that everyone deserves a healthy and active lifestyle. Our mission is to empower you with the tools, guidance, and community support to reach your fitness goals efficiently.
           From personalized workout routines and meal plans to progress tracking and motivation, we bring everything you need under one roof. Join our growing community and experience a holistic approach to fitness, wellness, and personal growth.
        </p>
        <p class="about-text">
          Whether you are a beginner or a seasoned athlete, Kynetic adapts to your journey and helps you challenge yourself in a safe and enjoyable way. Together, we make fitness a habit, not just a goal.
        </p>
      </div>
    </section>

    
    <section class="trainers full-width" aria-label="Our trainers">
      <div class="trainers-wrapper">
        <h2 class="section-title text-center">Meet Our Trainers</h2>
        <div class="trainers-grid">
          <article v-for="(trainer, idx) in trainers" :key="idx" class="trainer-card">
            <img :src="trainer.photo" :alt="trainer.name" class="trainer-photo"/>
            <h3>{{ trainer.name }}</h3>
            <h4>{{ trainer.specialty }}</h4>
            <p>{{ trainer.bio }}</p>
          </article>
        </div>
      </div>
    </section>

    
<section class="testimonials full-width" aria-label="Testimonials">
  <div class="testimonials-wrapper">
    <h2 class="section-title text-center">What Our Users Say</h2>
    <div class="testimonials-grid">
      <div class="testimonial-card" v-for="(testi, idx) in testimonials" :key="idx">
        <div class="testimonial-photo-wrapper">
          <img :src="testi.photo" :alt="testi.name" class="testimonial-photo" />
        </div>
        <p>"{{ testi.feedback }}"</p>
        <h4>- {{ testi.name }}</h4>
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
import { ref } from 'vue';
import '@/assets/global.css';

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

import trainer1 from '@/img/trainer2.jpg';
import trainer2 from '@/img/trainer1.webp';
import trainer3 from '@/img/trainer3.jpg';


import user1 from '@/img/user1.jpg';
import user2 from '@/img/user2.jpg';
import user3 from '@/img/user3.jpg';



const exerciseImages = [exercise1, exercise2, exercise3,exercise4];
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


const services = [
  { icon: meal4, title: "Personal Training", description: "Customized workouts for your goals." },
  { icon: meal5, title: "Nutrition Plans", description: "Healthy meal plans to complement exercises." },
  { icon: meal6, title: "Progress Tracking", description: "Visualize your growth and milestones." },
  { icon: exercise1, title: "Community Challenges", description: "Join challenges with friends." },
];


const trainers = [
  { name: "Sophia Turner", specialty: "Strength & Conditioning", bio: "Certified trainer helping clients achieve strength and endurance goals.", photo: trainer1 },
  { name: "Liam Johnson", specialty: "Nutrition & Wellness", bio: "Expert in creating personalized nutrition plans for maximum results.", photo: trainer2 },
  { name: "Emma Davis", specialty: "Yoga & Flexibility", bio: "Passionate about mindfulness and functional training for all levels.", photo: trainer3 },
];


const testimonials = [
  { name: "Alex", feedback: "Kynetic helped me stay consistent and achieve my goals! I feel stronger and healthier every day.", photo: user1 },
  { name: "Maria", feedback: "Love the meal plans and progress tracking! It really makes my fitness journey easy and fun.", photo: user2 },
  { name: "John", feedback: "Easy to use, motivating, and full of features that help me track my growth effectively.", photo: user3 },
];

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
  flex-direction: 
  row-reverse; 
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
  border: none; color: #fff; 
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
  background: #2f2f35ff;
  border-radius: 20px;
  padding: 2rem 2.5rem;
  box-shadow: 0 8px 20px rgba(246, 244, 244, 0.08);
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;
}



.sidebar.slider-box:hover {
  transform: translateY(-5px);
  box-shadow: 0 12px 25px rgba(0, 0, 0, 0.12);
}

.sidebar.slider-box h3 {
  font-size: 1.8rem;
  font-weight: 700;
  color: #0d6efd;
  margin-bottom: 1rem;
  position: relative;
  z-index: 1;
}

.sidebar.slider-box p {
  font-size: 1.05rem;
  color: #ffffffff;
  line-height: 2.6;
  position: relative;
  z-index: 1;
}



.services-wrapper  { 
  max-width: 1200px; 
  margin: 0 auto; 
  padding: 3rem 2rem; 
}
.services-grid { 
  display: grid; 
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); 
  gap: 2rem; 
}
.service-card { 
  background: var(--bg-card); 
  padding: 1.5rem; 
  border-radius: var(--radius-lg); 
  text-align: center; 
  box-shadow: var(--shadow-sm); 
}
.service-icon { 
  width: 80px; 
  height: 80px; 
  object-fit: cover; 
  margin-bottom: 1rem; 
}


.about-wrapper { 
  max-width: 900px; 
  margin: 0 auto; 
  padding: 3rem 2rem; 
  text-align: center;
}
.about-text { 
  font-size: 1.1rem; 
  line-height: 1.6; 
  margin-bottom: 1rem; 
}


.trainers-wrapper { 
  max-width: 1200px; 
  margin: 0 auto;
  padding: 3rem 2rem; 
  text-align: center; 
}
.trainers-grid { 
  display: grid; 
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); 
  gap: 2rem; 
}
.trainer-card { 
  background: var(--bg-card); 
  padding: 1.5rem; 
  border-radius: var(--radius-lg); 
  box-shadow: var(--shadow-sm);
}
.trainer-photo { 
  width: 100%; 
  height: 250px; 
  object-fit: cover; 
  border-radius: var(--radius-lg); 
  margin-bottom: 1rem;
}


.testimonials-wrapper { 
  max-width: 900px; 
  margin: 0 auto; 
  padding: 3rem 2rem; 
  text-align: center; 
}
.testimonials-grid { 
  display: grid; 
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); 
  gap: 2rem; 
}
.testimonial-card { 
  background: var(--bg-card); 
  padding: 1.5rem; 
  border-radius: var(--radius-lg);
  box-shadow: var(--shadow-sm);
  font-style: italic; 
}
.testimonial-photo-wrapper { 
  display: flex; 
  justify-content: center; 
  margin-bottom: 1rem; 
}
.testimonial-photo { 
  width: 80px; 
  height: 80px; 
  border-radius: 50%; 
  object-fit: cover; 
  border: 3px solid #fff; 
  box-shadow: 0 2px 6px rgba(0,0,0,0.2); 
  transition: transform 0.3s; 
}
.testimonial-photo:hover { 
  transform: scale(1.1); 
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
  color: #fff;
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
  background: linear-gradient(135deg, #1e90ff, #2563eb);
  color: #fff;
  border: none;
  padding: 0.8rem 1.8rem;
  font-size: 1rem;
  border-radius: 50px;
  transition: all 0.3s ease;
}

.btn--primary:hover {
  background: linear-gradient(135deg, #2563eb, #1e90ff);
  transform: scale(1.05);
}

.btn--outline {
  background: transparent;
  color: #fff;
  border: 2px solid #fff;
  padding: 0.8rem 1.8rem;
  border-radius: 50px;
  font-size: 1rem;
  transition: all 0.3s ease;
}

.btn--outline:hover {
  background: #fff;
  color: #1e90ff;
  transform: scale(1.05);
}


.footer {
  background: #0d1117;
  color: #cbd5e1;
  padding: 3rem 1.5rem 1rem;
}

.footer-wrapper {
  max-width: 1200px;
  margin: 0 auto;
}

.footer-columns {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
  gap: 2rem;
  margin-bottom: 2rem;
}

.footer-col h4 {
  font-size: 1.1rem;
  color: #fff;
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
  color: #1e90ff;
  margin-bottom: 1rem;
}


.footer-bottom {
  border-top: 1px solid #1f2937;
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
