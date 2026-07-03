<template>
  <section class="reset-password-page">
    <div class="login-wrapper">
      <div class="login-text">
        <h1 class="heading-font auth-hero-title">Reset Your Password</h1>
        <p>Enter your new password below to regain access to your account.</p>
      </div>

      <div class="card login-card">
        <h2 class="login-title">Reset Password</h2>
        <p class="login-subtitle">Provide a new password</p>

        <form class="login-form" @submit.prevent="handleResetPassword">
          <div class="form-group">
            <label for="password">New Password</label>
            <input
              type="password"
              id="password"
              v-model="password"
              placeholder="Enter your new password"
              required
            />
          </div>

          <div class="form-group">
            <label for="confirmPassword">Confirm Password</label>
            <input
              type="password"
              id="confirmPassword"
              v-model="confirmPassword"
              placeholder="Confirm your new password"
              required
            />
          </div>

          <button type="submit" class="btn btn--blue full-width" :disabled="loading">
            {{ loading ? "Resetting..." : "Reset Password" }}
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
import { showSuccess, showError, showWarning } from "@/stores/notifications";

export default {
  name: "ResetPasswordView",
  data() {
    return {
      password: "",
      confirmPassword: "",
      loading: false,
    };
  },
  methods: {
    async handleResetPassword() {
      if (!this.password || !this.confirmPassword) {
        showWarning("Please fill in both fields.");
        return;
      }
      if (this.password !== this.confirmPassword) {
        showWarning("Passwords do not match.");
        return;
      }

      const token = this.$route.query.token;
      if (!token) {
        showError("Invalid or missing reset token.");
        return;
      }

      this.loading = true;

      try {
        const response = await api.post("/auth/reset-password", {
          token,
          password: this.password,
          password_confirmation: this.confirmPassword,
        });

        showSuccess(response.data.message || "Password reset successfully.");
        this.password = "";
        this.confirmPassword = "";
        this.$router.push("/login");
      } catch (error) {
        console.error(error);
        showError(
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
.reset-password-page {
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
