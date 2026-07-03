<template>
  <section class="progress-section">

    <div class="page-header">
      <h2>Meals Progress</h2>

      <p class="subtitle">
        Select the meals you've consumed this week to keep your nutrition progress updated.
      </p>
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
              <label class="task-check">
                <input
                  type="checkbox"
                  :value="item.cart_id"
                  v-model="selectedItems"
                  :disabled="item.consumed === 1"
                />
                <span></span>
              </label>
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

      <button
        class="save-btn"
        :disabled="selectedItems.length === 0"
        @click="markConsumed"
      >
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

  </section>
</template>

<script>
import { ref, onMounted, watch } from "vue";
import axios from "axios";

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
} from "chart.js";

Chart.register(
  PieController,
  BarController,
  ArcElement,
  BarElement,
  CategoryScale,
  LinearScale,
  Tooltip,
  Legend
);

export default {
  name: "MealsProgress",

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

      headers: {
        Authorization: `Bearer ${localStorage.getItem("token")}`
      }
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

        await api.post("/progress/consumed", {
          items: selectedItems.value
        });

        selectedItems.value = [];
        allSelected.value = false;

        await fetchStats();

      } catch (err) {

        console.error("Error marking consumed:", err);

      }
    };

    const toggleSelectAllBtn = () => {

      if (!allSelected.value) {

        selectedItems.value = cartItems.value
          .filter(i => i.consumed !== 1)
          .map(i => i.cart_id);

      } else {

        selectedItems.value = [];

      }

      allSelected.value = !allSelected.value;
    };

    watch(selectedItems, () => {

      const selectable = cartItems.value
        .filter(i => i.consumed !== 1).length;

      allSelected.value =
        selectable > 0 &&
        selectedItems.value.length === selectable;

    });

    const themeColor = (name, fallback) => {
      return getComputedStyle(document.documentElement)
        .getPropertyValue(name)
        .trim() || fallback;
    };

    const chartPalette = () => [
      themeColor("--theme-ice", "#97dffc"),
      themeColor("--theme-lavender", "#858ae3"),
      themeColor("--theme-violet", "#613dc1"),
      themeColor("--theme-plum", "#4e148c"),
      themeColor("--theme-night", "#2c0735")
    ];

    const renderCategoryChart = (data) => {

      if (categoryInstance) categoryInstance.destroy();

      if (!Object.keys(data).length) return;

      const sortedEntries = Object.entries(data)
        .sort((a, b) => b[1] - a[1]);

      const labels = sortedEntries.map(e => e[0]);

      const values = sortedEntries.map(e => e[1]);

      const bgColors = chartPalette();

      const backgroundColor = bgColors.slice(0, labels.length);

      categoryInstance = new Chart(categoryChart.value, {

        type: "pie",

        data: {
          labels,
          datasets: [
            {
              data: values,
              backgroundColor,
              borderColor: themeColor("--theme-night", "#2c0735"),
              borderWidth: 2
            }
          ]
        },

        options: {

          responsive: true,

          aspectRatio: 1.2,

          plugins: {

            legend: {
              position: 'bottom'
            },

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

          layout: {
            padding: 10
          }
        }
      });
    };

    const renderCaloriesChart = (days) => {

      if (caloriesInstance) caloriesInstance.destroy();

      caloriesInstance = new Chart(caloriesChart.value, {

        type: "bar",

        data: {

          labels: days.map(d => d.day),

          datasets: [
            {
              label: "Calories",
              data: days.map(d => d.calories),
              backgroundColor: themeColor("--theme-lavender", "#858ae3"),
              borderColor: themeColor("--theme-ice", "#97dffc"),
              borderWidth: 1,
              borderRadius: 6
            }
          ]
        },

        options: {

          responsive: true,

          aspectRatio: 1.2,

          scales: {
            y: {
              beginAtZero: true
            }
          },

          plugins: {
            legend: {
              display: false
            }
          }
        }
      });
    };

    onMounted(fetchStats);

    return {
      cartItems,
      selectedItems,
      allSelected,
      toggleSelectAllBtn,
      markConsumed,
      categoryChart,
      caloriesChart
    };
  }
};
</script>

<style scoped>
.progress-section {
  margin-bottom: 4rem;
}

.page-header {
  text-align: center;
  margin-bottom: 2rem;
}

.page-header h2 {
  font-size: 2rem;
  margin-bottom: 0.7rem;
  color: var(--theme-ice);
}

.subtitle {
  font-size: 1rem;
  color: var(--text-muted);
  opacity: 0.85;
}

.meals-list {
  width: 100%;
  max-width: 1000px;
  margin: 0 auto 2rem auto;
}

.meals-list table {
  width: 100%;
  border-collapse: collapse;
}

.meals-list th,
.meals-list td {
  padding: 0.7rem;
  border-bottom: 1px solid #97dffc;
  text-align: center;
}

.select-all-btn {
  background: var(--accent-lavender);
  color: white;
  font-weight: 700;
  padding: 0.75rem 1.25rem;
  border-radius: 12px;
  cursor: pointer;
  border: none;
  font-size: 1rem;
}

.save-btn {
  background: var(--accent-blue);
  color: var(--accent-purple);
  padding: 0.95rem 1.8rem;
  border-radius: 12px;
  font-weight: 700;
  margin-top: 1.25rem;
  border: none;
  cursor: pointer;
  font-size: 1rem;
}

.task-check {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 42px;
  height: 42px;
  cursor: pointer;
  position: relative;
}

.task-check input {
  position: absolute;
  opacity: 0;
  width: 100%;
  height: 100%;
  margin: 0;
  cursor: pointer;
}

.task-check span {
  display: inline-flex;
  width: 36px;
  height: 36px;
  border: 2px solid var(--theme-lavender);
  border-radius: 10px;
  background: rgba(var(--theme-night-rgb), 0.35);
  transition: background 0.2s ease, border-color 0.2s ease, transform 0.2s ease;
}

.task-check span::after {
  content: "";
  width: 10px;
  height: 17px;
  border: solid var(--theme-night);
  border-width: 0 3px 3px 0;
  margin: 5px auto 0;
  transform: rotate(45deg) scale(0);
  transition: transform 0.18s ease;
}

.task-check input:checked + span {
  background: var(--theme-ice);
  border-color: var(--theme-ice);
  transform: scale(1.03);
}

.task-check input:checked + span::after {
  transform: rotate(45deg) scale(1);
}

.charts {
  display: flex;
  flex-wrap: wrap;
  gap: 2rem;
  justify-content: center;
}

.chart-box {
  flex: 1 1 300px;
  max-width: 420px;
}

.chart-box h3 {
  text-align: center;
  margin-bottom: 1rem;
  color: var(--text-light);
}
</style>
