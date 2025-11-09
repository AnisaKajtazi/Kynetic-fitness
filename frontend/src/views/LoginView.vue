<template>
  <section class="login-page">
    <div class="login-wrapper">
      <div class="login-text">
        <h1>Welcome Back, Champion 🏋️‍♀️</h1>
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

        
        setLoggedIn(true);

      
        this.$router.push("/dashboard");
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
  gap: 3rem;
  max-width: 1000px;
  padding: 3rem 2rem;
  width: 100%;
}
.login-text {
  flex: 1 1 380px;
  color: var(--text-light);
  text-align: left;
  padding: 1rem;
}
.login-text h1 {
  font-size: 2.6rem;
  margin-bottom: 1rem;
  line-height: 1.2;
  font-weight: 800;
  background: linear-gradient(90deg, #d4af37, #f0c75e);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  font-family: Lucida Handwriting;
}
.login-text p {
  font-size: 1.05rem;
  color: var(--text-light);
  opacity: 0.8;
  max-width: 420px;
}
.login-card {
  flex: 1 1 360px;
  padding: 3rem 2.5rem;
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
  font-size: 2rem;
  margin-bottom: 0.5rem;
  color: var(--accent-blue);
  font-weight: 700;
}
.login-subtitle {
  color: var(--text-dim);
  font-size: 0.95rem;
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
  font-size: 1rem;
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
  font-size: 0.9rem;
}
.btn.full-width {
  width: 100%;
  padding: 1rem 0;
  font-size: 1.05rem;
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
  .login-text h1 {
    font-size: 2rem;
  }
  .login-card {
    width: 100%;
    max-width: 420px;
  }
}
</style>
