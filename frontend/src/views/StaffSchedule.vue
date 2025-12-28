<template>
  <div class="staff-layout">
    <Sidebar />

    <div class="page-container">
      <h1>My Schedule</h1>

      <div v-if="loading">Loading...</div>

      <table v-else class="schedule-table">
        <thead>
          <tr>
            <th>Day</th>
            <th>Start</th>
            <th>End</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="slot in schedule" :key="slot.ScheduleID">
            <td>{{ slot.day }}</td>
            <td>{{ slot.start_time.slice(0,5) }}</td>
            <td>{{ slot.end_time.slice(0,5) }}</td>
            <td>
              <span :class="slot.isAvailable ? 'available' : 'unavailable'">
                {{ slot.isAvailable ? 'Available' : 'Unavailable' }}
              </span>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/services/axios'
import Sidebar from '@/components/Sidebar.vue'

const schedule = ref([])
const loading = ref(true)

onMounted(async () => {
  try {
    const res = await api.get('staff-schedule/my-schedule')
    schedule.value = res.data
  } catch (e) {
    schedule.value = []
  } finally {
    loading.value = false
  }
})
</script>

<style scoped>
.staff-layout {
  display: flex;
}

.page-container {
  margin-left: 240px;
  padding: 2rem;
  width: calc(100vw - 240px);
}

.schedule-table {
  width: 100%;
  border-collapse: collapse;
}

.schedule-table th,
.schedule-table td {
  padding: 0.8rem;
  border-bottom: 1px solid #ddd;
}

.available {
  color: green;
  font-weight: 600;
}

.unavailable {
  color: red;
  font-weight: 600;
}
</style>
