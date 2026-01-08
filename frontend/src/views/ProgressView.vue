<template>
  <div class="progress-page">
    <div class="progress-card">
      <h2>My Progress</h2>

      <div class="cart-section">
        <h3>My Meals (This Week)</h3>
        <table>
          <thead>
            <tr>
              <th>
                <input type="checkbox" v-model="selectAll" @change="toggleSelectAll" />
              </th>
              <th>Meal</th>
              <th>Category</th>
              <th>Calories</th>
              <th>Quantity</th>
              <th>Consumed</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in cartItems" :key="item.cart_id">
              <td>
                <input type="checkbox" 
                       v-model="selectedItems" 
                       :value="item.cart_id"
                       :disabled="item.consumed === 1" />
              </td>
              <td>{{ item.item_name }}</td>
              <td>{{ item.category }}</td>
              <td>{{ item.calories }}</td>
              <td>{{ item.quantity }}</td>
              <td>
                <span v-if="item.consumed">✅</span>
                <span v-else>❌</span>
              </td>
            </tr>
          </tbody>
        </table>
        <button class="save-btn" @click="markConsumed">Mark as Consumed</button>
      </div>

      <div class="stats-section">
        <div class="chart-wrapper">
          <h3>Calories Consumed by Category</h3>
          <canvas ref="categoryChart"></canvas>
        </div>
        <div class="chart-wrapper">
          <h3>Calories Per Day</h3>
          <canvas ref="caloriesChart"></canvas>
        </div>
      </div>


      <div class="trainer-section">
        <h3>Select Trainer</h3>
        <select v-model="selectedTrainer">
          <option v-for="trainer in trainers" :key="trainer.id" :value="trainer.id">
            {{ trainer.name }}
          </option>
        </select>

        <h3>Trainer Feedback</h3>
        <textarea v-model="trainerFeedback" placeholder="Feedback from trainer..."></textarea>
        <button class="save-btn" @click="saveFeedback">Save Feedback</button>
      </div>


      <div class="journal-section">
        <h3>My Journal</h3>
        <textarea v-model="journal" placeholder="Write your notes..."></textarea>
        <button class="save-btn" @click="saveJournal">Save Journal</button>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, watch } from 'vue';
import axios from 'axios';
import {
  Chart,
  PieController,
  BarController,
  ArcElement,
  BarElement,
  CategoryScale,
  LinearScale,
  Tooltip,
  Legend
} from 'chart.js';

Chart.register(PieController, BarController, ArcElement, BarElement, CategoryScale, LinearScale, Tooltip, Legend);

export default {
  name: 'ProgressView',
  setup() {
    const categoryChart = ref(null);
    const caloriesChart = ref(null);
    const categoryChartInstance = ref(null);
    const caloriesChartInstance = ref(null);

    const cartItems = ref([]);
    const selectedItems = ref([]);
    const selectAll = ref(false);

    const trainers = ref([]);
    const selectedTrainer = ref(null);
    const trainerFeedback = ref('');
    const journal = ref('');

    const api = axios.create({
      baseURL: 'http://127.0.0.1:8000/api',
      headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
    });


    const fetchCartStats = async () => {
      try {
        const { data } = await api.get('/progress/stats');

        cartItems.value = data.cartItems || [];


        const categories = data.byCategory || {};
        const categoryLabels = Object.keys(categories);
        const categoryData = Object.values(categories);

        if (categoryChart.value) {
          if (categoryChartInstance.value) categoryChartInstance.value.destroy();
          categoryChartInstance.value = new Chart(categoryChart.value, {
            type: 'pie',
            data: {
              labels: categoryLabels,
              datasets: [{
                data: categoryData,
                backgroundColor: ['#2563eb', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#f97316', '#14b8a6']
              }]
            },
            options: {
              responsive: true,
              plugins: {
                legend: { position: 'bottom' },
                tooltip: {
                  callbacks: {
                    label: function(ctx) {
                      const total = ctx.dataset.data.reduce((sum, val) => sum + val, 0);
                      const value = ctx.raw;
                      const percent = ((value / total) * 100).toFixed(1);
                      return `${ctx.label}: ${value} kcal (${percent}%)`;
                    }
                  }
                }
              }
            }
          });
        }


        const days = data.byDay || [];
        const barLabels = days.map(d => d.day);
        const barData = days.map(d => d.calories);

        if (caloriesChart.value) {
          if (caloriesChartInstance.value) caloriesChartInstance.value.destroy();
          caloriesChartInstance.value = new Chart(caloriesChart.value, {
            type: 'bar',
            data: {
              labels: barLabels,
              datasets: [{
                label: 'Calories',
                data: barData,
                backgroundColor: '#2563eb'
              }]
            },
            options: {
              responsive: true,
              plugins: { legend: { display: false } },
              scales: { y: { beginAtZero: true } }
            }
          });
        }

      } catch (err) {
        console.error('Error fetching progress stats:', err);
      }
    };


    const fetchTrainers = async () => {
      try {
        const { data } = await api.get('/users?role=trainer');
        trainers.value = data.data || data;
      } catch (err) {
        console.error('Error fetching trainers:', err);
      }
    };


    const markConsumed = async () => {
      if (selectedItems.value.length === 0) {
        alert('No meals selected!');
        return;
      }

      try {
        cartItems.value.forEach(item => {
          if (selectedItems.value.includes(item.cart_id)) {
            item.consumed = 1;
          }
        });

        selectedItems.value = [];
        selectAll.value = false;
      } catch (err) {
        console.error('Error marking consumed:', err);
        alert('Error marking meals as consumed.');
      }
    };


    const toggleSelectAll = () => {
      if (selectAll.value) {
        selectedItems.value = cartItems.value
          .filter(i => i.consumed !== 1)
          .map(i => i.cart_id);
      } else {
        selectedItems.value = [];
      }
    };


    const saveFeedback = () => alert(`Feedback saved: ${trainerFeedback.value}`);
    const saveJournal = () => alert(`Journal saved: ${journal.value}`);

    onMounted(() => {
      fetchCartStats();
      fetchTrainers();
    });

    watch(cartItems, () => {
      const allSelectable = cartItems.value.filter(i => i.consumed !== 1).length;
      selectAll.value = allSelectable > 0 && allSelectable === selectedItems.value.length;
    });

    return {
      categoryChart,
      caloriesChart,
      cartItems,
      selectedItems,
      selectAll,
      toggleSelectAll,
      markConsumed,
      trainers,
      selectedTrainer,
      trainerFeedback,
      journal,
      saveFeedback,
      saveJournal
    };
  }
};
</script>

<style scoped>
.progress-page {
  display: flex;
  justify-content: center;
  padding: 3rem;
  background: linear-gradient(135deg, #0f1115, #1c1f26);
  min-height: 100vh;
  color: #f9fafb;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.progress-card {
  width: 100%;
  max-width: 1000px;
  background: linear-gradient(145deg, #1f2937, #111827);
  border-radius: 16px;
  padding: 2.5rem;
  box-shadow: 0 10px 30px rgba(0,0,0,0.7);
}

.cart-section {
  margin-bottom: 2rem;
  overflow-x: auto;
}

.cart-section table {
  width: 100%;
  border-collapse: collapse;
}

.cart-section th, .cart-section td {
  padding: 0.6rem 1rem;
  text-align: left;
  border-bottom: 1px solid #374151;
}

.stats-section {
  display: flex;
  flex-wrap: wrap;
  gap: 2rem;
  margin-bottom: 2rem;
}

.chart-wrapper {
  flex: 1 1 300px;
}

.chart-wrapper canvas {
  width: 100% !important;
  height: 300px !important;
}

.trainer-section, .journal-section {
  margin-top: 2rem;
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

textarea {
  width: 100%;
  min-height: 80px;
  background-color: #1f2937;
  border: none;
  border-radius: 10px;
  padding: 1rem;
  color: #f9fafb;
  resize: vertical;
}

select {
  padding: 0.6rem 1rem;
  border-radius: 8px;
  border: none;
  background-color: #1f2937;
  color: #f9fafb;
}

.save-btn {
  margin-top: 0.5rem;
  background-color: #2563eb;
  color: #f9fafb;
  font-weight: 600;
  padding: 0.7rem 1.4rem;
  border-radius: 10px;
  cursor: pointer;
  transition: background 0.25s ease;
}

.save-btn:hover {
  background-color: #1d4ed8;
}
</style>
