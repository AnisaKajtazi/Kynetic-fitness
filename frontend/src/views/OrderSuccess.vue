<template>
  <div class="order-success-page">
    <h2 class="text">Payment Successful!</h2>
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
