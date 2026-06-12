<template>
  <div class="order-error-page">
    <h2 class="text heading-font auth-hero-title">Payment Failed</h2>
    <p>Sorry, there was an error processing your payment. Please try again.</p>
    <router-link :to="`/dashboard?activeSection=mycart`" class="link">
      Go back to Cart
    </router-link>
  </div>
</template>

<script setup>
</script>

<style scoped>
.order-error-page {
  padding: 6rem 2rem 2rem;
  text-align: center;
}
</style>
