<template>
  <section class="login-page">
    <div class="login-wrapper">
      <div class="login-text">
        <h1 class="heading-font auth-hero-title">Welcome Back, Champion</h1>
        <p>Stay consistent. Stay strong. Let’s crush your fitness goals today!</p>
      </div>

      <div class="card login-card">
        <h2 class="login-title">Sign In</h2>
        <p class="login-subtitle">Enter your credentials below</p>

        <form class="login-form" @submit.prevent="handleLogin">
          <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" v-model="username" placeholder="Enter your username" required />
          </div>

          <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" v-model="password" placeholder="Enter your password" required />
          </div>

          <div class="forgot-password">
            <router-link to="/forgot-password" class="link">Forgot your password?</router-link>
          </div>

          <button type="submit" class="btn btn--blue full-width" :disabled="loading">
            <span v-if="loading">Logging in...</span>
            <span v-else>Login</span>
          </button>
        </form>

        <p class="signup-text">
          Don’t have an account?
          <router-link to="/signup" class="link">Create one</router-link>
        </p>
      </div>
    </div>
  </section>
</template>

<script>
import axios from "axios";
import { loggedIn, setLoggedIn } from "@/stores/auth";

const BASE_URL = "http://127.0.0.1:8000/api"; // Backend URL

export default {
  name: "LoginView",
  data() {
    return {
      username: "",
      password: "",
      loading: false,
    };
  },
  methods: {
    async handleLogin() {
      if (!this.username || !this.password) {
        alert("Please enter both username and password.");
        return;
      }

      this.loading = true;

      try {
        const response = await axios.post(`${BASE_URL}/auth/login`, {
          username: this.username,
          password: this.password,
        });

        localStorage.setItem("token", response.data.access_token);
        localStorage.setItem("user", JSON.stringify(response.data.user));
        localStorage.setItem("role", response.data.user.RoleID);

        setLoggedIn(true, response.data.user.RoleID);

        const role = response.data.user.RoleID;

      if (role === 1) {
            this.$router.push("/admin-dashboard");
          } else if (role === 2) {
            this.$router.push("/dashboard");
          } else if (role === 3) {
            this.$router.push("/staff-dashboard");
          }

      } catch (error) {
        console.error("Login error:", error);

        if (error.response) {
          const status = error.response.status;
          const message = error.response.data?.message || "Login failed.";

          if (status === 401) {
            alert("Invalid username or password.");
          } else if (status === 404) {
            alert("Backend endpoint not found. Check your URL.");
          } else {
            alert(message);
          }
        } else if (error.request) {
          alert("No response from server. Is backend running?");
        } else {
          alert("Login error: " + error.message);
        }
      } finally {
        this.loading = false;
      }
    },
  },
};
</script>


<style scoped>
.login-page {
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
.login-wrapper {
  display: flex;
  align-items: center;
  justify-content: center;
  flex-wrap: wrap;
  gap: 2.5rem;
  max-width: 1180px;
  padding: 2.5rem 2rem;
  width: 100%;
}
.login-text {
  flex: 1 1 420px;
  color: var(--text-light);
  text-align: left;
  padding: 0.5rem 1rem;
}
.login-text p {
  font-size: var(--text-lg);
  color: var(--text-light);
  opacity: 0.9;
  max-width: 480px;
  line-height: 1.75;
}
.login-card {
  flex: 1 1 480px;
  min-width: min(100%, 440px);
  max-width: 520px;
  padding: 2.75rem 3rem;
  border-radius: var(--radius-lg);
  background: linear-gradient(145deg, var(--bg-card), var(--bg-contrast));
  box-shadow: 0 10px 35px rgba(0, 0, 0, 0.6);
  text-align: center;
  transition: transform 0.4s ease, box-shadow 0.4s ease;
}
.login-card:hover {
  transform: translateY(-6px);
  box-shadow: var(--shadow-lg);
}
.login-title {
  font-size: clamp(1.75rem, 3vw, 2.25rem);
  margin-bottom: 0.6rem;
  color: var(--accent-blue);
  font-weight: 700;
}
.login-subtitle {
  color: var(--text-dim);
  font-size: var(--text-base);
  margin-bottom: 2rem;
}
.form-group {
  text-align: left;
  margin-bottom: 1.5rem;
}
label {
  display: block;
  font-weight: 600;
  margin-bottom: 0.4rem;
  color: var(--text-muted);
}
input {
  width: 100%;
  padding: 1rem 1.2rem;
  border-radius: 999px;
  font-size: var(--text-base);
  border: 1px solid #444;
  background: #1f2023;
  color: var(--text-light);
}
input::placeholder {
  color: #777;
}
.forgot-password {
  text-align: right;
  margin-bottom: 1.8rem;
}
.signup-text {
  margin-top: 1.8rem;
  color: var(--text-dim);
  font-size: var(--text-base);
}
.btn.full-width {
  width: 100%;
  padding: 1rem 0;
  font-size: var(--text-md);
  font-weight: 600;
  border-radius: 50px;
  transition: all 0.3s ease;
}
.btn.full-width:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 18px rgba(26, 115, 232, 0.35);
}
@media (max-width: 768px) {
  .login-wrapper {
    flex-direction: column;
    text-align: center;
    padding: 2rem 1rem;
  }
  .login-text {
    text-align: center;
  }
  .login-text h1,
  .auth-hero-title {
    font-size: var(--text-2xl);
  }
  .login-card {
    width: 100%;
    max-width: 520px;
    min-width: 0;
  }
}
</style>
