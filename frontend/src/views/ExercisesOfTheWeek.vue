<template>
  <div class="week-page">
    <h2 class="text-color">Exercises of the Week</h2>
    <p class="page-subtitle">Review your weekly plan and mark each workout as completed.</p>

    <div
      v-for="(items, day) in week"
      :key="day"
      class="day-card"
    >
      <div class="day-header">
  <h3>{{ day }}</h3>

  <button 
    v-if="items.length >= 2"
    class="btn btn--blue" 
    @click="completeAll(day)"
  >
    ✔ Select All Completed
  </button>

</div>

      <div v-if="items.length === 0" class="empty">
        No exercises for this day
      </div>

      <div class="items">
        <div
          v-for="item in items"
          :key="item.id"
          class="exercise-card"
        >
          <img
            :src="getImageUrl(item.exercise.image)"
            class="exercise-img"
          />

          <div class="exercise-info">
            <h4>{{ item.exercise.name }}</h4>
          </div>

          <div class="card-actions">
            <input
              type="checkbox"
              v-model="item.completed"
              @change="toggle(item)"
            />

            <input
              type="number"
              class="reps-input"
              v-model.number="item.reps"
              @change="updateReps(item)"
              min="1"
              max="10"
            />

            <span>reps</span>

              <button class="btn btn--red" @click="remove(item)">
                Remove
              </button>
          </div>
        </div>
      </div>

    </div>
  </div>
</template>

<script>
import { ref, onMounted } from "vue";
import api from "@/services/axios";

export default {
  name: "ExercisesOfTheWeek",

  setup() {
    const week = ref({});

    const fetchWeek = async () => {
      const res = await api.get("/exercise-week");
      week.value = res.data;
    };

    const toggle = async (item) => {
      await api.post(`/exercise-week/toggle/${item.id}`);
    };

    const completeAll = async (day) => {
  await api.post("/exercise-week/complete-all", {
    day_of_week: day,
  });

  if (week.value[day]) {
    week.value[day].forEach(item => {
      item.completed = true;
    });
  }
};

    const remove = async (item) => {
      await api.delete(`/exercise-week/${item.id}`);
      fetchWeek();
    };

    const updateReps = async (item) => {
  if (item.reps > 10) item.reps = 10;
  if (item.reps < 1) item.reps = 1;

  await api.post(`/exercise-week/update-reps/${item.id}`, {
    reps: item.reps,
  });
};

const getImageUrl = (image) => {
  return image
    ? `http://127.0.0.1:8000/uploads/${image}`
    : "https://via.placeholder.com/300x200?text=No+Image";
};

    onMounted(fetchWeek);

 return {
  week,
  toggle,
  completeAll,
  remove,
  updateReps,
  getImageUrl
};
  },
};

</script>

<style scoped>
.week-page {
  width: 100%;
  max-width: var(--page-max-width);
  margin: 0 auto;
  padding: var(--page-padding-y) var(--page-padding-x);
  color: var(--text-light);
}

.text-color {
  color: #97dffc;
  font-size: clamp(1.75rem, 3vw, 2.35rem);
  margin-bottom: 0.75rem;
}

.page-subtitle {
  font-size: var(--text-base);
  margin: 0 0 1.75rem;
  color: var(--text-muted);
}

.day-card {
  background: var(--bg-card);
  border-radius: var(--radius);
  padding: 1.5rem;
  margin-bottom: 1.5rem;
  box-shadow: var(--shadow-sm);
  width: 100%;
}

.day-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
}

.empty {
  color: #9ca3af;
  text-align: center;
  padding: 1rem;
}

.items {
  display: grid;
  grid-template-columns: repeat(5, 1fr);
  gap: 1.25rem;
}

.exercise-card {
  background: var(--bg-card);
  border-radius: var(--radius);
  box-shadow: var(--shadow-sm);
  padding: 1.25rem;
  text-align: center;
}

.exercise-card h4 {
  font-size: var(--text-md);
}

.exercise-img {
  width: 100%;
  height: 210px;
  object-fit: cover;
  border-radius: var(--radius);
  margin-bottom: 0.6rem;
}

@media (max-width: 1200px) {
  .items {
    grid-template-columns: repeat(4, 1fr);
  }
}

@media (max-width: 992px) {
  .items {
    grid-template-columns: repeat(3, 1fr);
  }
}

@media (max-width: 768px) {
  .items {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 480px) {
  .items {
    grid-template-columns: 1fr;
  }
}

.card-actions {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.4rem;
  margin-top: 0.5rem;
}

.reps-input {
  width: 80px;
  text-align: center;
}

.reps-label {
  color: #9ca3af;
}

.remove {
  background: none;
  border: none;
  cursor: pointer;
  font-size: 1rem;
}

.btn {
  padding: 0.4rem 0.8rem;
  border-radius: 6px;
  border: none;
  cursor: pointer;
}

.btn--blue {
  background: var(--accent-blue);
  color: var(--accent-purple);
}

.btn--blue:hover {
  background: #1d4ed8;
}

.btn--red {
  background: var(--accent-plum);
  color: white;
  font-size: 12px;
  padding: 0.5rem 1.5rem;
}

.btn--red:hover {
  background: var(--accent-plum);
}
</style>
