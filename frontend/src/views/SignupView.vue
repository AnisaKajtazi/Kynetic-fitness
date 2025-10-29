<template>
  <section class="signup-page">
    <div class="signup-wrapper">
      <div class="signup-text">
        <h1>Join the Fitness Journey 💪</h1>
        <p>Create your personal account and start transforming your body today!</p>
      </div>

      <transition name="fade-slide" mode="out-in">
        <div :key="step" class="card signup-card">
          <h2 class="signup-title">Sign Up - Step {{ step }}</h2>
          <p class="signup-subtitle">
            {{
              step === 1
                ? 'Account Information'
                : step === 2
                ? 'Personal Details'
                : 'Fitness Goals'
            }}
          </p>

          <form class="signup-form" @submit.prevent="nextStep">
            
            
            <div v-if="step === 1">
              <div class="form-group">
                <label for="username">Username</label>
                <input
                  type="text"
                  id="username"
                  v-model="form.username"
                  placeholder="Enter your username"
                  required
                />
              </div>

              <div class="form-group">
                <label for="email">Email</label>
                <input
                  type="email"
                  id="email"
                  v-model="form.email"
                  placeholder="Enter your email"
                  required
                />
              </div>

              <div class="form-group">
                <label for="password">Password</label>
                <input
                  type="password"
                  id="password"
                  v-model="form.password"
                  placeholder="Enter your password"
                  required
                />
              </div>

              <div class="form-group">
                <label for="confirmPassword">Confirm Password</label>
                <input
                  type="password"
                  id="confirmPassword"
                  v-model="form.confirmPassword"
                  placeholder="Confirm your password"
                  required
                />
              </div>
            </div>

            
            
            <div v-if="step === 2">
              <div class="form-group">
                <label for="name">First Name</label>
                <input
                  type="text"
                  id="name"
                  v-model="form.name"
                  placeholder="Enter your first name"
                  required
                />
              </div>

              <div class="form-group">
                <label for="surname">Last Name</label>
                <input
                  type="text"
                  id="surname"
                  v-model="form.surname"
                  placeholder="Enter your last name"
                  required
                />
              </div>

              <div class="form-group">
                <label>Gender</label>
                <div class="radio-group">
                  <label><input type="radio" value="male" v-model="form.gender" /> Male</label>
                  <label><input type="radio" value="female" v-model="form.gender" /> Female</label>
                  <label><input type="radio" value="other" v-model="form.gender" /> Other</label>
                </div>
              </div>

              <div class="form-group">
                <label for="dob">Date of Birth</label>
                <input type="date" id="dob" v-model="form.dob" required />
              </div>

              <div class="form-group">
                <label for="phone">Phone</label>
                <input
                  type="text"
                  id="phone"
                  v-model="form.phone"
                  placeholder="Enter your phone"
                  required
                />
              </div>

              <div class="form-group">
                <label for="address">Address</label>
                <input
                  type="text"
                  id="address"
                  v-model="form.address"
                  placeholder="Enter your address"
                  required
                />
              </div>
            </div>

            
            
            <div v-if="step === 3">
              <div class="form-group">
                <label for="goal">Fitness Goal</label>
                <select id="goal" v-model="form.goal" required>
                  <option value="">Select your goal</option>
                  <option value="lose_weight">Lose Weight</option>
                  <option value="gain_muscle">Gain Muscle</option>
                  <option value="stay_fit">Stay Fit</option>
                </select>
              </div>

              <div class="form-group">
                <label for="activity">Activity Level</label>
                <select id="activity" v-model="form.activity" required>
                  <option value="">Select your activity level</option>
                  <option value="low">Low</option>
                  <option value="medium">Medium</option>
                  <option value="high">High</option>
                </select>
              </div>

              <div class="form-group">
                <label for="days">Training Days per Week</label>
                <input
                  type="number"
                  id="days"
                  v-model="form.trainingDays"
                  min="1"
                  max="7"
                  required
                />
              </div>

              <div class="form-group">
                <label for="focus">Focus Area</label>
                <select id="focus" v-model="form.focusArea" required>
                  <option value="">Choose focus area</option>
                  <option value="upper_body">Upper Body</option>
                  <option value="lower_body">Lower Body</option>
                  <option value="cardio">Cardio</option>
                </select>
              </div>
            </div>

            
            
            <div class="buttons">
              <button
                v-if="step > 1"
                type="button"
                class="btn btn--gray"
                @click="prevStep"
              >
                Back
              </button>

              <button
                v-if="step < 3"
                type="button"
                class="btn btn--blue"
                @click="nextStep"
              >
                Next
              </button>

              <button
                v-if="step === 3"
                type="submit"
                class="btn btn--gold full-width"
              >
                Create Account
              </button>
            </div>
          </form>

          <p class="login-link">
            Already have an account?
            <router-link to="/login" class="link">Sign in</router-link>
          </p>
        </div>
      </transition>
    </div>
  </section>
</template>

<script>
import api from "@/services/axios";

export default {
  name: "SignUpView",
  data() {
    return {
      step: 1,
      form: {
        username: "",
        email: "",
        password: "",
        confirmPassword: "",
        name: "",
        surname: "",
        gender: "",
        dob: "",
        phone: "",
        address: "",
        goal: "",
        activity: "",
        trainingDays: "",
        focusArea: "",
      },
    };
  },
  methods: {
    nextStep() {
      if (this.step === 1) {
        if (!this.form.username || !this.form.email || !this.form.password || !this.form.confirmPassword) {
          alert("Please fill in all fields before continuing.");
          return;
        }
        if (this.form.password !== this.form.confirmPassword) {
          alert("Passwords do not match.");
          return;
        }
      }

      if (this.step === 2) {
        if (!this.form.name || !this.form.surname || !this.form.gender || !this.form.dob || !this.form.phone || !this.form.address) {
          alert("Please complete all personal details before continuing.");
          return;
        }
      }

      if (this.step < 3) this.step++;
      else this.submitForm();
    },

    prevStep() {
      if (this.step > 1) this.step--;
    },

    async submitForm() {
      if (!this.form.goal || !this.form.activity || !this.form.trainingDays || !this.form.focusArea) {
        alert("Please complete all fitness details before submitting.");
        return;
      }

      try {
        const response = await api.post("/auth/register", {
          username: this.form.username,
          name: this.form.name,
          surname: this.form.surname,
          email: this.form.email,
          password: this.form.password,
          password_confirmation: this.form.confirmPassword,
          RoleID: 2, // 2 = client
          phone: this.form.phone,
          address: this.form.address,
          dob: this.form.dob,
          gender: this.form.gender,
        });

        localStorage.setItem("token", response.data.access_token);
        localStorage.setItem("user", JSON.stringify(response.data.user));

        alert("Account created successfully!");
        this.$router.push("/login");
      } catch (error) {
        console.error(error);
        alert(error.response?.data?.message || "Sign up failed.");
      }
    },
  },
};
</script>


<style scoped>

.signup-page {
  min-height: 100vh;
  width: 100%;
  display: flex;
  justify-content: center;
  align-items: center;
  background: radial-gradient(circle at top right, rgba(26, 115, 232, 0.15), transparent 60%),
              radial-gradient(circle at bottom left, rgba(212, 175, 55, 0.12), transparent 60%),
              var(--bg-dark);
  background-blend-mode: screen;
  overflow: hidden;
  position: relative;
}

.signup-wrapper {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 3rem;
  flex-wrap: wrap;
  max-width: 1000px;
  padding: 3rem 2rem;
  width: 100%;
}

.signup-text {
  flex: 1 1 380px;
  color: var(--text-light);
  text-align: left;
  padding: 1rem;
}

.signup-text h1 {
  font-size: 2.6rem;
  margin-bottom: 1rem;
  line-height: 1.2;
  font-weight: 800;
  background: linear-gradient(90deg, #d4af37, #f0c75e);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  font-family: Lucida Handwriting;
}

.signup-text p {
  font-size: 1.05rem;
  color: var(--text-light);
  opacity: 0.8;
  max-width: 420px;
}

.signup-card {
  flex: 1 1 360px;
  padding: 3rem 2.5rem;
  border-radius: var(--radius-lg);
  background: linear-gradient(145deg, var(--bg-card), var(--bg-contrast));
  box-shadow: 0 10px 35px rgba(0, 0, 0, 0.6);
  text-align: center;
  transition: transform 0.4s ease, box-shadow 0.4s ease;
}

.signup-title {
  font-size: 2rem;
  margin-bottom: 0.5rem;
  color: var(--accent-blue);
  font-weight: 700;
}

.signup-subtitle {
  color: var(--text-dim);
  font-size: 0.95rem;
  margin-bottom: 2rem;
}

.form-group {
  text-align: left;
  margin-bottom: 1.3rem;
}

label {
  color: var(--text-muted);
  font-weight: 600;
  margin-bottom: 0.4rem;
  display: block;
}

input,
select {
  width: 100%;
  padding: 1rem 1.2rem;
  border-radius: 999px;
  font-size: 1rem;
  border: 1px solid #444;
  background: #1f2023;
  color: var(--text-light);
}

.radio-group {
  display: flex;
  gap: 1rem;
  color: var(--text-light);
}

.buttons {
  margin-top: 1.8rem;
  display: flex;
  justify-content: space-between;
  gap: 1rem;
}

.btn {
  padding: 1rem 2rem;
  border-radius: 50px;
  font-weight: 600;
  transition: all 0.3s ease;
}

.btn--blue {
  background: #1a73e8;
  color: #fff;
}

.btn--gray {
  background: #333;
  color: #ddd;
}

.btn--gold {
  background: linear-gradient(90deg, #d4af37, #f0c75e);
  color: #000;
}

.btn:hover {
  transform: translateY(-3px);
  box-shadow: 0 6px 18px rgba(0, 0, 0, 0.4);
}

.fade-slide-enter-active,
.fade-slide-leave-active {
  transition: all 0.5s ease;
}

.fade-slide-enter-from {
  opacity: 0;
  transform: translateY(30px);
}

.fade-slide-leave-to {
  opacity: 0;
  transform: translateY(-30px);
}

@media (max-width: 768px) {
  .signup-wrapper {
    flex-direction: column;
    text-align: center;
    padding: 2rem 1rem;
  }

  .signup-text {
    text-align: center;
  }

  .signup-text h1 {
    font-size: 2rem;
  }

  .signup-card {
    width: 100%;
    max-width: 420px;
  }
}
</style>
