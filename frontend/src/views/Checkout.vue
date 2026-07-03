<template>
  <div class="checkout-page">
    <h2>Checkout Summary</h2>

    <div v-if="loading" class="loading">Loading...</div>

    <div v-else>
      <p v-if="cartItems.length === 0">Your cart is empty.</p>

      <div v-else>
        <div
          v-for="item in cartItems"
          :key="item.cart_id"
          class="checkout-item"
        >
          <img
            class="meal-image"
            :src="item.meal
              ? `http://127.0.0.1:8000/uploads/${item.meal.category}/${item.meal.image}` 
              : ''"
            alt="Meal Image"
          />
          <div class="meal-info">
            <p class="meal-name">{{ item.item_name }}</p>
            <p class="meal-desc">{{ item.meal?.description ?? '' }}</p>
          </div>
          <div class="meal-quantity">
            x{{ item.quantity }}
          </div>
          <div class="meal-price">
            ${{ (item.price * item.quantity).toFixed(2) }}
          </div>
        </div>

        <div class="total-price">
          <strong>Total: ${{ totalPrice.toFixed(2) }}</strong>
        </div>

        <button class="confirm-btn" @click="confirmCheckout" :disabled="processing">
          {{ processing ? 'Redirecting...' : 'Confirm & Pay' }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';
import { loggedIn } from '@/stores/auth';
import { showError } from '@/stores/notifications';

const cartItems = ref([]);
const loading = ref(true);
const processing = ref(false);
const router = useRouter();
const isLoggedIn = computed(() => loggedIn.value);

if (!isLoggedIn.value) {
  router.push('/login');
}

const fetchCart = async () => {
  loading.value = true;
  try {
    const res = await axios.get('http://127.0.0.1:8000/api/my-cart', {
      headers: { Authorization: `Bearer ${localStorage.getItem('token')}` },
    });
    cartItems.value = res.data;
  } catch (err) {
    console.error('Error fetching cart:', err);
    showError('Failed to load cart.');
  } finally {
    loading.value = false;
  }
};

const totalPrice = computed(() =>
  cartItems.value.reduce((sum, item) => sum + (item.price * item.quantity || 0), 0)
);

const confirmCheckout = async () => {
  processing.value = true;
  try {
    const res = await axios.post(
      'http://127.0.0.1:8000/api/checkout/stripe',
      {},
      {
        headers: { Authorization: `Bearer ${localStorage.getItem('token')}` },
      }
    );

    if (res.data.url) {
      window.location.href = res.data.url;
    } else {
      showError('Stripe session failed. Try again.');
      processing.value = false;
    }

  } catch (err) {
    console.error('Checkout failed:', err);
    showError('Checkout failed. Try again.');
    processing.value = false;
  }
};

onMounted(fetchCart);
</script>

<style scoped>
.checkout-page {
  padding: var(--page-top-with-navbar) 1.5rem 1.5rem;
}

.checkout-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 0.5rem 0;
  border-bottom: 1px dashed #eee;
}

.meal-image {
  width: 80px;
  height: 80px;
  object-fit: cover;
  border-radius: 8px;
}

.meal-info {
  flex: 3;
}

.meal-name {
  font-weight: 600;
}

.meal-desc {
  font-size: 0.9rem;
  color: #555;
}

.meal-quantity {
  flex: 1;
  text-align: center;
}

.meal-price {
  flex: 1;
  text-align: right;
  font-weight: 500;
}

.total-price {
  margin-top: 1rem;
  text-align: right;
  font-size: 1.2rem;
  font-weight: 700;
}

.confirm-btn {
  margin-top: 1rem;
  padding: 10px 20px;
  background-color: #38c172;
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 600;
  transition: background 0.2s;
}

.confirm-btn:hover {
  background-color: #2d995b;
}
</style>
