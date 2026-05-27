<template>
  <div class="order-error-page">
    <h2 class="text">Payment Failed</h2>
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
.text{
   font-size: 2.6rem;
  margin-bottom: 1rem;
  line-height: 1.2;
  font-weight: 800;
  background: linear-gradient(90deg, var(--accent-blue), var(--accent-blue));
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  font-family: Lucida Handwriting;
}
</style>
