<template>
  <div class="my-cart-page">
    <h2>My Cart</h2>
    <div v-if="loading" class="loading">Loading cart...</div>
    <div v-else>
      <p v-if="cartItems.length === 0">Your cart is empty.</p>
      <div v-else>
        <div
          v-for="item in cartItems"
          :key="item.cart_id"
          class="cart-item"
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
            <button @click="updateQuantity(item, item.quantity - 1)" :disabled="item.quantity <= 1">-</button>
            <span>{{ item.quantity }}</span>
            <button @click="updateQuantity(item, item.quantity + 1)">+</button>
          </div>
          <div class="meal-price">
            ${{ (item.price * item.quantity).toFixed(2) }}
          </div>
          <button class="remove-btn" @click="removeFromCart(item.meal_id)">
            ❌
          </button>
        </div>

        <div class="total-price">
          <strong>Total: ${{ totalPrice.toFixed(2) }}</strong>
        </div>
        <div class="checkout-button-wrapper" v-if="cartItems.length > 0">
 <button @click="$router.push('/checkout')" class="checkout-btn">Checkout</button>
</div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';
import { loggedIn } from '@/stores/auth';

const cartItems = ref([]);
const loading = ref(true);
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
    alert('Failed to load cart');
  } finally {
    loading.value = false;
  }
};

const updateQuantity = async (item, newQty) => {
  if (newQty < 1) {
    removeFromCart(item.meal_id);
    return;
  }
  try {
    await axios.patch(`http://127.0.0.1:8000/api/my-cart/${item.meal_id}/quantity`, {
      quantity: newQty,
    }, {
      headers: { Authorization: `Bearer ${localStorage.getItem('token')}` },
    });

    item.quantity = newQty;
  } catch (err) {
    console.error('Error updating quantity:', err);
    alert('Failed to update quantity');
  }
};

const removeFromCart = async (meal_id) => {
  try {
    await axios.delete(`http://127.0.0.1:8000/api/my-cart/${meal_id}`, {
      headers: { Authorization: `Bearer ${localStorage.getItem('token')}` },
    });
    cartItems.value = cartItems.value.filter(item => item.meal_id !== meal_id);
  } catch (err) {
    console.error('Error removing item:', err);
    alert('Failed to remove item');
  }
};

const totalPrice = computed(() => {
  return cartItems.value.reduce((sum, item) => sum + (item.price * item.quantity || 0), 0);
});

onMounted(fetchCart);

const checkout = async () => {
  if (!confirm('Are you sure you want to proceed to checkout?')) return;

  try {
    const res = await axios.post(
      'http://127.0.0.1:8000/api/checkout',
      {},
      { headers: { Authorization: `Bearer ${localStorage.getItem('token')}` } }
    );

    alert(`Checkout successful! Your order ID: ${res.data.order_id}`);

    cartItems.value = [];
  } catch (err) {
    console.error('Checkout error:', err);
    alert(err.response?.data?.message || 'Checkout failed');
  }
};

</script>

<style scoped>
.my-cart-page {
  padding: 1.5rem;
}

.cart-item {
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
  display: flex;
  align-items: center;
  gap: 6px;
}

.meal-quantity button {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  border: none;
  background-color: var(--accent-blue);
  color: white;
  font-weight: bold;
  cursor: pointer;
  transition: background 0.2s;
}

.meal-quantity button:hover {
  background-color: var(--accent-blue);
}

.meal-price {
  flex: 1;
  text-align: right;
  font-weight: 500;
}

.remove-btn {
  background: var(--accent-plum);
  color: white;
  border: none;
  border-radius: 6px;
  padding: 6px 10px;
  cursor: pointer;
  transition: background 0.2s;
}

.remove-btn:hover {
  background: #cc1f1a;
}

.total-price {
  margin-top: 1rem;
  text-align: right;
  font-size: 1.2rem;
  font-weight: 700;
}
.checkout-button-wrapper {
  margin-top: 1.5rem;
  text-align: right;
}

.checkout-btn {
  background-color: var(--accent-blue);
  color: white;
  border: none;
  border-radius: 8px;
  padding: 10px 20px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.2s;
}

.checkout-btn:hover{
  background-color: #293442ff;
}

</style>
