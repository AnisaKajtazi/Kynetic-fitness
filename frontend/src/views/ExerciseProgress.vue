<template>
  <section class="progress-section">
    <div class="page-header">
      <h2>Exercises Progress</h2>
      <p class="subtitle">
        Track your workouts and improve your fitness.
      </p>
    </div>

    <div class="recommended">
      <h3>Recommended Exercises</h3>

      <div class="cards">
        <div
          v-for="ex in recommended"
          :key="ex.ExerciseID"
          class="exercise-card"
        >
          <img :src="getImage(ex.image)" />
          <h4>{{ ex.name }}</h4>
          <p>{{ ex.category }} • {{ ex.level }}</p>

          <button @click="addExercise(ex)" class="save-btn">
            + Add
          </button>
        </div>
      </div>

      <p v-if="!recommended.length" class="empty">
        No recommendations yet
      </p>
    </div>

    <div class="meals-list">
      <table>
        <thead>
          <tr>
            <th>Exercise</th>
            <th>Category</th>
            <th>Reps</th>
            <th>Day</th>
          </tr>
        </thead>

        <tbody>
          <tr v-for="item in history" :key="item.id">
            <td>{{ item.exercise?.name }}</td>
            <td>{{ item.exercise?.category }}</td>
            <td>{{ item.reps }}</td>
            <td>{{ formatDay(item.day_of_week) }}</td>
          </tr>
        </tbody>
      </table>

      <p v-if="!history.length" class="empty">
        No activity yet
      </p>
    </div>

    <div class="charts">
      <div class="chart-box">
        <h3>Exercises by Category (%)</h3>
        <canvas ref="categoryChart"></canvas>
      </div>

      <div class="chart-box">
        <h3>Reps per Day</h3>
        <canvas ref="durationChart"></canvas>
      </div>
    </div>
  </section>
</template>

<script>
import { ref, onMounted } from "vue";
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
  name: "ExerciseProgress",

  setup() {
    const recommended = ref([]);
    const history = ref([]);

    const categoryChart = ref(null);
    const durationChart = ref(null);

    let categoryInstance = null;
    let durationInstance = null;

    const api = axios.create({
      baseURL: "http://127.0.0.1:8000/api",
      headers: {
        Authorization: `Bearer ${localStorage.getItem("token")}`
      }
    });

    const fetchStats = async () => {
      try {
        const { data } = await api.get("/exercise-progress/stats");

        recommended.value = data.recommended || [];
        history.value = data.history || [];

        renderCategoryChart(data.categoryStats || {});
        renderDurationChart(data.byDay || []);
      } catch (err) {
        console.error(err);
      }
    };

    const addExercise = async (ex) => {
      await api.post("/exercise-week/add", {
        exercise_id: ex.ExerciseID,
        day_of_week: new Date().toLocaleDateString("en-US", {
          weekday: "long"
        }),
        reps: 3
      });

      fetchStats();
    };

    const getImage = (img) => {
      return img
        ? `http://127.0.0.1:8000/uploads/${img}`
        : "https://via.placeholder.com/200";
    };

    const formatDay = (day) => {
      return day ? day.slice(0, 3) : "";
    };

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

      const sorted = Object.entries(data).sort((a, b) => b[1] - a[1]);

      categoryInstance = new Chart(categoryChart.value, {
        type: "pie",
        data: {
          labels: sorted.map(e => e[0]),
          datasets: [{
            data: sorted.map(e => e[1]),
            backgroundColor: chartPalette(),
            borderColor: themeColor("--theme-night", "#2c0735"),
            borderWidth: 2
          }]
        },
        options: {
          plugins: {
            legend: { position: "bottom" }
          }
        }
      });
    };

    const renderDurationChart = (days) => {
      if (durationInstance) durationInstance.destroy();

      durationInstance = new Chart(durationChart.value, {
        type: "bar",
        data: {
          labels: days.map(d => d.day),
          datasets: [{
            label: "Reps",
            data: days.map(d => d.duration),
            backgroundColor: themeColor("--theme-lavender", "#858ae3"),
            borderColor: themeColor("--theme-ice", "#97dffc"),
            borderWidth: 1,
            borderRadius: 6
          }]
        },
        options: {
          responsive: true,
          scales: {
            y: { beginAtZero: true }
          },
          plugins: {
            legend: { display: false }
          }
        }
      });
    };

    onMounted(fetchStats);

    return {
      recommended,
      history,
      addExercise,
      getImage,
      formatDay,
      categoryChart,
      durationChart
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
  color: var(--theme-ice);
}

.subtitle {
  color: var(--text-muted);
}

.recommended {
  max-width: 1100px;
  margin: 0 auto 2rem auto;
  text-align: center;
}

.cards {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  gap: 1rem;
}

.exercise-card {
  background: var(--bg-card);
  padding: 1rem;
  border-radius: 10px;
  width: 200px;
  text-align: center;
}

.exercise-card img {
  width: 100%;
  height: 160px;
  object-fit: cover;
  border-radius: 8px;
}

.meals-list {
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
  text-align: center;
  border-bottom: 1px solid #97dffc;
}

.save-btn {
  background: var(--accent-blue);
  color: white;
  padding: 0.4rem 0.8rem;
  border-radius: 6px;
  margin-top: 0.5rem;
}

.empty {
  text-align: center;
  color: var(--text-dim);
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
.chart-box canvas {
  width: 100% !important;
  min-height: 420px;
}
.chart-box h3 {
  text-align: center;
  margin-bottom: 1rem;
  color: var(--text-light);
}
</style>
