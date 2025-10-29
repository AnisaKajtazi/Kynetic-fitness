<template>
  <section class="forgot-password-page">
    <div class="login-wrapper">
      <div class="login-text">
        <h1>Reset Your Password 🔒</h1>
        <p>Enter your email below and we'll send you a link to reset your password.</p>
      </div>

      <div class="card login-card">
        <h2 class="login-title">Forgot Password</h2>
        <p class="login-subtitle">Provide your registered email</p>

        <form class="login-form" @submit.prevent="handleForgotPassword">
          <div class="form-group">
            <label for="email">Email</label>
            <input
              type="email"
              id="email"
              v-model="email"
              placeholder="Enter your email"
              required
            />
          </div>

          <button 
            type="submit" 
            class="btn btn--blue full-width"
            :disabled="loading"
          >
            {{ loading ? "Sending..." : "Send Reset Link" }}
          </button>
        </form>

        <p class="signup-text">
          Remembered your password?
          <router-link to="/login" class="link">Sign In</router-link>
        </p>
      </div>
    </div>
  </section>
</template>

<script>
import api from "@/services/axios";

export default {
  name: "ForgotPasswordView",
  data() {
    return {
      email: "",
      loading: false,
    };
  },
  methods: {
    async handleForgotPassword() {
      if (!this.email) {
        alert("Please enter your email.");
        return;
      }

      this.loading = true;

      try {
        const response = await api.post("/auth/forgot-password", {
          email: this.email,
        });

        alert(response.data.message || "Reset link sent! Check your email.");
        this.email = "";
      } catch (error) {
        console.error(error);
        alert(
          error.response?.data?.message ||
            "An error occurred. Please try again later."
        );
      } finally {
        this.loading = false;
      }
    },
  },
};
</script>

<style scoped>
.forgot-password-page {
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

.btn[disabled] {
  opacity: 0.6;
  cursor: not-allowed;
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
