<template>
  <div class="page-header">
    <h1>Progress Page</h1>
    <h2>Your Purchased Meals – Mark What You’ve Eaten</h2>
    <p class="subtitle">Select the meals you’ve consumed this week to keep your progress updated.</p>
  </div>

  <div class="meals-list">
    <table>
      <thead>
        <tr>
          <th>
            <button class="select-all-btn" @click="toggleSelectAllBtn">
              {{ allSelected ? 'Deselect All' : 'Select All' }}
            </button>
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
            <input type="checkbox" :value="item.cart_id" v-model="selectedItems" :disabled="item.consumed === 1" />
          </td>
          <td>{{ item.item_name }}</td>
          <td>{{ item.category }}</td>
          <td>{{ item.calories * item.quantity }}</td>
          <td>{{ item.quantity }}</td>
          <td>
            <span v-if="item.consumed">✅</span>
            <span v-else>❌</span>
          </td>
        </tr>
      </tbody>
    </table>

    <button class="save-btn" :disabled="selectedItems.length === 0" @click="markConsumed">
      Mark as Consumed
    </button>
  </div>

  <div class="charts">
    <div class="chart-box pie-chart">
      <h3>Consumed Meals by Category (%)</h3>
      <canvas ref="categoryChart"></canvas>
    </div>

    <div class="chart-box bar-chart">
      <h3>Calories Consumed per Day</h3>
      <canvas ref="caloriesChart"></canvas>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, watch } from "vue";
import axios from "axios";
import {
  Chart, PieController, BarController, ArcElement, BarElement,
  CategoryScale, LinearScale, Tooltip, Legend
} from "chart.js";

Chart.register(PieController, BarController, ArcElement, BarElement, CategoryScale, LinearScale, Tooltip, Legend);

export default {
  name: "ProgressView",
  setup() {
    const cartItems = ref([]);
    const selectedItems = ref([]);
    const allSelected = ref(false);

    const categoryChart = ref(null);
    const caloriesChart = ref(null);
    let categoryInstance = null;
    let caloriesInstance = null;

    const api = axios.create({
      baseURL: "http://127.0.0.1:8000/api",
      headers: { Authorization: `Bearer ${localStorage.getItem("token")}` }
    });

    const fetchStats = async () => {
      try {
        const { data } = await api.get("/progress/stats");
        cartItems.value = data.cartItems || [];
        renderCategoryChart(data.byCategory || {});
        renderCaloriesChart(data.byDay || []);
      } catch (err) {
        console.error("Error fetching progress:", err);
      }
    };

    const markConsumed = async () => {
      try {
        await api.post("/progress/consumed", { items: selectedItems.value });
        selectedItems.value = [];
        allSelected.value = false;
        await fetchStats();
      } catch (err) {
        console.error("Error marking consumed:", err);
      }
    };

    const toggleSelectAllBtn = () => {
      if (!allSelected.value) {
        selectedItems.value = cartItems.value.filter(i => i.consumed !== 1).map(i => i.cart_id);
      } else {
        selectedItems.value = [];
      }
      allSelected.value = !allSelected.value;
    };

    watch(selectedItems, () => {
      const selectable = cartItems.value.filter(i => i.consumed !== 1).length;
      allSelected.value = selectable > 0 && selectedItems.value.length === selectable;
    });

    const renderCategoryChart = (data) => {
      if (categoryInstance) categoryInstance.destroy();
      if (!Object.keys(data).length) return;

      const sortedEntries = Object.entries(data).sort((a, b) => b[1] - a[1]);
      const labels = sortedEntries.map(e => e[0]);
      const values = sortedEntries.map(e => e[1]);

      const bgColors = ["#2563eb","#10b981","#f59e0b","#ef4444","#8b5cf6","#14b8a6"];
      const backgroundColor = bgColors.slice(0, labels.length);

      categoryInstance = new Chart(categoryChart.value, {
        type: "pie",
        data: { labels, datasets: [{ data: values, backgroundColor, borderWidth: 1 }] },
        options: {
          responsive: true,
          aspectRatio: 1.2,
          plugins: {
            legend: { position: 'bottom' },
            tooltip: {
              callbacks: {
                label: function(context) {
                  const val = context.parsed;
                  const sum = context.chart._metasets[0].total;
                  const percent = ((val / sum) * 100).toFixed(1);
                  return `${context.label}: ${val} (${percent}%)`;
                }
              }
            }
          },
          layout: { padding: 10 },
        }
      });
    };

    const renderCaloriesChart = (days) => {
      if (caloriesInstance) caloriesInstance.destroy();
      caloriesInstance = new Chart(caloriesChart.value, {
        type: "bar",
        data: {
          labels: days.map(d => d.day),
          datasets: [{
            label: "Calories",
            data: days.map(d => d.calories),
            backgroundColor: "#2563eb",
            borderRadius: 6
          }]
        },
        options: {
          responsive: true,
          aspectRatio: 1.2,
          scales: { y: { beginAtZero: true } },
          plugins: { legend: { display: false } }
        }
      });
    };

    onMounted(fetchStats);

    return { cartItems, selectedItems, allSelected, toggleSelectAllBtn, markConsumed, categoryChart, caloriesChart };
  }
};
</script>

<style scoped>
.page-header {
  margin-top: 3rem;
  text-align: center;
}

.page-header h1 {
  font-size: 2.4rem;
  margin-bottom: 1rem;
  line-height: 1.2;
  font-weight: 800;
  background: linear-gradient(90deg, #d4af37, #f0c75e);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  font-family: Lucida Handwriting;
}

.page-header h2 {
  font-size: 1.4rem;
  font-weight: 600;
  margin-bottom: 0.5rem;
  color: #f9fafb;
}

.page-header .subtitle {
  font-size: 1rem;
  color: #d1d5db;
  opacity: 0.8;
  max-width: 480px;
  margin: 0 auto 2rem auto;
}

.meals-list {
  width: 100%;
  max-width: 1000px;
  margin: 0 auto 2rem auto;
}

.meals-list table { 
  width:100%; 
  border-collapse:collapse; 
}

.meals-list th,.meals-list td { 
  padding:0.6rem; 
  border-bottom:1px solid #374151; 
}

.select-all-btn {
  background: #10b981;
  color: white;
  font-weight: 600;
  padding: 0.4rem 0.8rem;
  border-radius: 6px;
  cursor: pointer;
  border: none;
}

.save-btn { 
  background:#2563eb; 
  color:white; 
  padding:0.6rem 1.2rem; 
  border-radius:8px; 
  font-weight:600; 
  margin-top:0.5rem;
}

.charts { 
  display:flex; 
  flex-wrap:wrap; 
  gap:2rem; 
  justify-content:center;
  margin-bottom: 3rem;
}

.chart-box { 
  flex:1 1 300px; 
  max-width:400px;
}

.chart-box.bar-chart {
  max-width:400px;
}
</style>
