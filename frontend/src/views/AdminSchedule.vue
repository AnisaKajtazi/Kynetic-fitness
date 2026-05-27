<template>
  <div class="admin-schedule">
    <h2>Staff Weekly Schedule</h2>

    <div class="table-wrapper" v-if="allSchedulesLoaded">
      <table class="schedule-table">
        <thead>
          <tr>
            <th class="staff-col">Staff</th>
            <th class="staff-col">Type</th>
            <th v-for="day in weekDays" :key="day">{{ day }}</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="staff in staffList" :key="staff.UserID">
            <td class="staff-name">{{ staff.first_name }} {{ staff.last_name }}</td>
            <td class="staff-type">{{ staff.staff_type || 'N/A' }}</td>
            <td v-for="(_, index) in weekDays" :key="index">
              <div
                v-if="!weeklySchedule[staff.UserID][index].editing"
                class="display-box"
                @click="editTime(staff.UserID, index)"
              >
                <div class="times">
                  <div>{{ formatTime(weeklySchedule[staff.UserID][index].start_time) || '--:--' }}</div>
                  <div>{{ formatTime(weeklySchedule[staff.UserID][index].end_time) || '--:--' }}</div>
                </div>
                <button
                  v-if="weeklySchedule[staff.UserID][index].start_time || weeklySchedule[staff.UserID][index].end_time"
                  class="edit-btn"
                >Edit</button>
              </div>

              <div v-else class="edit-row">
                <div class="time-column">
                  <input type="time" v-model="weeklySchedule[staff.UserID][index].tempStart" />
                  <input type="time" v-model="weeklySchedule[staff.UserID][index].tempEnd" />
                </div>
                <button class="set-btn" @click="setTime(staff.UserID, index)">Set</button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-if="!allSchedulesLoaded" class="loading">Loading schedule...</div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const staffList = ref([])
const weekDays = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday']
const weeklySchedule = ref({})
const allSchedulesLoaded = ref(false)

const API_URL = 'http://127.0.0.1:8000/api'
const token = localStorage.getItem('token')
const authHeader = { Authorization: `Bearer ${token}` }

onMounted(async () => {
  try {
    const staffRes = await axios.get(`${API_URL}/staff-schedule/staff-list`, { headers: authHeader })
    staffList.value = staffRes.data

    for (const staff of staffList.value) {
      const res = await axios.get(`${API_URL}/staff-schedule/staff/${staff.UserID}`, { headers: authHeader })
      weeklySchedule.value[staff.UserID] = weekDays.map(day => {
        const entry = res.data.find(e => e.day === day)
        return {
          day,
          start_time: entry?.start_time?.slice(0,5) || '',
          end_time: entry?.end_time?.slice(0,5) || '',
          tempStart: entry?.start_time?.slice(0,5) || '',
          tempEnd: entry?.end_time?.slice(0,5) || '',
          editing: false
        }
      })
    }

    allSchedulesLoaded.value = true
  } catch (err) {
    console.error('Error loading schedules:', err)
    allSchedulesLoaded.value = true
  }
})

const editTime = (userId, index) => {
  const d = weeklySchedule.value[userId][index]
  d.tempStart = d.start_time
  d.tempEnd = d.end_time
  d.editing = true
}

const setTime = async (userId, index) => {
  const d = weeklySchedule.value[userId][index]
  d.start_time = d.tempStart
  d.end_time = d.tempEnd
  d.editing = false

  const payload = {
    day: d.day,
    start_time: d.start_time || null,
    end_time: d.end_time || null,
    isAvailable: !!(d.start_time || d.end_time),
    RoleID: staffList.value.find(s => s.UserID === userId)?.RoleID || null
  }

  try {
    await axios.post(`${API_URL}/staff-schedule/staff/${userId}`, { schedule: [payload] }, { headers: authHeader })
  } catch (err) {
    console.error('Error saving schedule:', err)
    alert('Failed to save schedule for ' + d.day)
  }
}

const formatTime = t => {
  if (!t) return ''
  let [h, m] = t.split(':').map(Number)
  const ampm = h >= 12 ? 'PM' : 'AM'
  h = h % 12 || 12
  return `${h}:${m.toString().padStart(2,'0')} ${ampm}`
}
</script>

<style scoped>
.admin-schedule {
  padding: 1.5rem;
}
.table-wrapper {
  overflow-x: auto;
}
.schedule-table {
  width: 100%;
  border-collapse: collapse;
}
.schedule-table th,
.schedule-table td {
  border: 1px solid #e5e7eb;
  padding: 8px;
  text-align: center;
  vertical-align: middle;
}
.staff-col {
  width: 200px;
}
.staff-name {
  font-weight: 600;
  white-space: nowrap;
}
.staff-type {
  font-style: italic;
  color: var(--accent-blue);
}
.display-box {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 6px;
  cursor: pointer;
}
.times {
  display: flex;
  flex-direction: column;
  gap: 2px;
  font-size: 0.85rem;
}
.edit-row {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 6px;
}
.time-column {
  display: flex;
  flex-direction: column;
  gap: 4px;
}
.time-column input[type='time'] {
  width: 120px;
  padding: 4px;
  font-size: 0.85rem;
}
.edit-btn {
  font-size: 0.75rem;
  padding: 3px 8px;
  border-radius: 6px;
  border: 1px solid #ccc;
  background: #f3f4f6;
}
.set-btn {
  font-size: 0.75rem;
  padding: 5px 12px;
  border-radius: 6px;
  border: none;
  background: var(--accent-blue);
  color: white;
}
.set-btn:hover {
  background: #1d4ed8;
}
.loading {
  padding: 1rem;
  font-style: italic;
}
</style>
