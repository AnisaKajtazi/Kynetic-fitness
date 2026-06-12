<template>
  <div class="order-success-page">
    <h2 class="text heading-font auth-hero-title">Payment Successful!</h2>
    <p>Your order has been placed successfully.</p>
    <router-link :to="`/dashboard?activeSection=mycart&order_id=${orderId}`" class="link">
      Go to My Cart
    </router-link>
  </div>
</template>

<script setup>
import { useRoute } from 'vue-router';

const route = useRoute();
const orderId = route.query.order_id || '';
</script>

<style scoped>
.order-success-page {
  padding: 6rem 2rem 2rem;
  text-align: center;
}
</style>
