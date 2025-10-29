<template>
  <section class="reset-password-page">
    <div class="login-wrapper">
      <div class="login-text">
        <h1>Reset Your Password 🔒</h1>
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
        alert("Please fill in both fields.");
        return;
      }
      if (this.password !== this.confirmPassword) {
        alert("Passwords do not match.");
        return;
      }

      // Marrim token nga query params (nga email link)
      const token = this.$route.query.token;
      if (!token) {
        alert("Invalid or missing reset token.");
        return;
      }

      this.loading = true;

      try {
        const response = await api.post("/auth/reset-password", {
          token,
          password: this.password,
          password_confirmation: this.confirmPassword,
        });

        alert(response.data.message || "Password reset successfully!");
        this.password = "";
        this.confirmPassword = "";
        this.$router.push("/login");
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
</style>
