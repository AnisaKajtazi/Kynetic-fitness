<template>
  <div class="admin-maintenance">
    <header class="page-head">
      <div>
        <h2>Maintenance Management</h2>
        <p>Monitor maintenance tasks and equipment issues from the same records used by maintenance staff.</p>
      </div>
      <span class="live-badge">Live shared data</span>
    </header>

    <div v-if="loading" class="state-message">Loading maintenance data...</div>
    <div v-else-if="error" class="state-message state-message--error">{{ error }}</div>

    <template v-else>
      <section class="stats-grid">
        <article class="stat-card">
          <span>Total Tasks</span>
          <strong>{{ taskRows.length }}</strong>
        </article>
        <article class="stat-card">
          <span>Completed Tasks</span>
          <strong>{{ completedTasks.length }}</strong>
        </article>
        <article class="stat-card">
          <span>Pending Tasks</span>
          <strong>{{ incompleteTasks.length }}</strong>
        </article>
        <article class="stat-card">
          <span>Total Equipment Issues</span>
          <strong>{{ issueRows.length }}</strong>
        </article>
        <article class="stat-card">
          <span>High Priority Issues</span>
          <strong>{{ highPriorityIssues.length }}</strong>
        </article>
      </section>

      <section class="admin-table-panel maintenance-panel">
        <div class="admin-table-header">
          <div class="admin-table-title-block">
            <h2>Maintenance Tasks Overview</h2>
            <p>Read-only task progress overview. Maintenance staff update completion from Maintenance Center.</p>
          </div>

          <div class="progress-card">
            <span>Tasks Completed</span>
            <strong>{{ completedTasks.length }} / {{ taskRows.length }} Completed</strong>
            <div class="progress-track">
              <div class="progress-fill" :style="{ width: `${taskProgress}%` }"></div>
            </div>
            <small>{{ taskProgress }}%</small>
          </div>
        </div>

        <div class="filter-row">
          <div class="segmented">
            <button
              v-for="option in taskFilterOptions"
              :key="option.value"
              :class="{ active: taskFilter === option.value }"
              type="button"
              @click="taskFilter = option.value"
            >
              {{ option.label }}
            </button>
          </div>
        </div>

        <div class="admin-table-shell">
          <table class="admin-table maintenance-table">
            <thead>
              <tr>
                <th>Task Title</th>
                <th>Description</th>
                <th>Location</th>
                <th>Priority</th>
                <th>Status</th>
                <th>Due Date</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="!filteredTasks.length">
                <td colspan="6" class="empty-cell">No tasks match this filter.</td>
              </tr>
              <tr v-for="task in filteredTasks" :key="task.id">
                <td><strong>{{ task.title }}</strong></td>
                <td class="description-cell">{{ task.description || "-" }}</td>
                <td>{{ task.location || "-" }}</td>
                <td><span :class="['maintenance-badge', priorityClass(task.priority)]">{{ task.priority }}</span></td>
                <td><span :class="['maintenance-badge', statusClass(task.status)]">{{ task.status }}</span></td>
                <td>{{ formatDate(task.due_date) }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </section>

      <section class="admin-table-panel maintenance-panel">
        <div class="admin-table-header">
          <div class="admin-table-title-block">
            <h2>Equipment Issues Report</h2>
            <p>Read-only report of issues created and maintained by maintenance staff.</p>
          </div>
        </div>

        <div class="filter-row filter-row--issues">
          <input
            v-model.trim="issueSearch"
            class="admin-search-input"
            type="search"
            placeholder="Search issues by title or location..."
          />

          <select v-model="issueStatusFilter" class="filter-select">
            <option value="all">All statuses</option>
            <option value="Pending">Pending</option>
            <option value="In Progress">In Progress</option>
            <option value="Completed">Completed</option>
          </select>

          <select v-model="issuePriorityFilter" class="filter-select">
            <option value="all">All priorities</option>
            <option value="Low">Low</option>
            <option value="Medium">Medium</option>
            <option value="High">High</option>
          </select>
        </div>

        <div class="admin-table-shell">
          <table class="admin-table maintenance-table">
            <thead>
              <tr>
                <th>Title</th>
                <th>Description</th>
                <th>Location</th>
                <th>Priority</th>
                <th>Status</th>
                <th>Created Date</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="!filteredIssues.length">
                <td colspan="6" class="empty-cell">No equipment issues match your filters.</td>
              </tr>
              <tr v-for="issue in filteredIssues" :key="issue.id">
                <td><strong>{{ issue.title }}</strong></td>
                <td class="description-cell">{{ issue.description || "-" }}</td>
                <td>{{ issue.location || "-" }}</td>
                <td><span :class="['maintenance-badge', priorityClass(issue.priority)]">{{ issue.priority }}</span></td>
                <td><span :class="['maintenance-badge', statusClass(issue.status)]">{{ issue.status }}</span></td>
                <td>{{ formatDate(issue.created_at) }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </section>
    </template>
  </div>
</template>

<script setup>
import { computed, onMounted, onUnmounted, ref } from "vue"
import api from "@/services/axios"

const records = ref([])
const loading = ref(true)
const error = ref("")
const taskFilter = ref("all")
const issueSearch = ref("")
const issueStatusFilter = ref("all")
const issuePriorityFilter = ref("all")
let refreshTimer = null

const taskFilterOptions = [
  { label: "All Tasks", value: "all" },
  { label: "Completed Tasks", value: "completed" },
  { label: "Incomplete Tasks", value: "incomplete" },
]

const taskRows = computed(() => records.value.filter((item) => item.type === "Task"))
const issueRows = computed(() => records.value.filter((item) => item.type === "Equipment Issue"))
const completedTasks = computed(() => taskRows.value.filter((task) => task.status === "Completed"))
const incompleteTasks = computed(() => taskRows.value.filter((task) => task.status !== "Completed"))
const highPriorityIssues = computed(() => issueRows.value.filter((issue) => issue.priority === "High"))

const taskProgress = computed(() => {
  if (!taskRows.value.length) return 0
  return Math.round((completedTasks.value.length / taskRows.value.length) * 100)
})

const filteredTasks = computed(() => {
  if (taskFilter.value === "completed") return completedTasks.value
  if (taskFilter.value === "incomplete") return incompleteTasks.value
  return taskRows.value
})

const filteredIssues = computed(() => {
  const search = issueSearch.value.toLowerCase()

  return issueRows.value.filter((issue) => {
    const matchesSearch = search
      ? `${issue.title || ""} ${issue.location || ""}`.toLowerCase().includes(search)
      : true
    const matchesStatus = issueStatusFilter.value === "all" || issue.status === issueStatusFilter.value
    const matchesPriority = issuePriorityFilter.value === "all" || issue.priority === issuePriorityFilter.value

    return matchesSearch && matchesStatus && matchesPriority
  })
})

const normalize = (value) => String(value || "").toLowerCase().replace(/\s+/g, "-")
const priorityClass = (priority) => `maintenance-badge--priority-${normalize(priority)}`
const statusClass = (status) => `maintenance-badge--status-${normalize(status)}`

const formatDate = (value) => {
  if (!value) return "-"
  return new Date(value).toLocaleDateString("en-US", {
    year: "numeric",
    month: "short",
    day: "numeric",
  })
}

const fetchMaintenanceData = async ({ silent = false } = {}) => {
  if (!silent) {
    loading.value = true
    error.value = ""
  }

  try {
    const { data } = await api.get("maintenance-tasks")
    records.value = Array.isArray(data) ? data : []
  } catch (err) {
    if (!silent) {
      error.value = err.response?.status === 403
        ? "Only admins can view this maintenance report."
        : "Maintenance data could not be loaded."
    }
  } finally {
    if (!silent) loading.value = false
  }
}

onMounted(() => {
  fetchMaintenanceData()
  refreshTimer = window.setInterval(() => fetchMaintenanceData({ silent: true }), 15000)
})

onUnmounted(() => {
  if (refreshTimer) window.clearInterval(refreshTimer)
})
</script>

<style scoped>
.admin-maintenance {
  width: 100%;
  color: var(--text-light);
}

.page-head {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 1rem;
  margin-bottom: 1.5rem;
}

.page-head h2 {
  color: var(--theme-ice);
  font-size: 2.2rem;
  margin-bottom: 0.4rem;
}

.page-head p {
  max-width: 780px;
  margin: 0;
}

.live-badge {
  flex-shrink: 0;
  border: 1px solid rgba(var(--theme-ice-rgb), 0.25);
  border-radius: 999px;
  background: rgba(var(--theme-ice-rgb), 0.12);
  color: var(--theme-ice);
  font-weight: 800;
  padding: 0.55rem 0.85rem;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(5, minmax(140px, 1fr));
  gap: 1rem;
  margin-bottom: 1.5rem;
}

.stat-card {
  background: var(--bg-contrast);
  border: 1px solid var(--border-dark);
  border-radius: var(--radius);
  box-shadow: var(--shadow-sm);
  padding: 1rem;
}

.stat-card span {
  color: var(--text-muted);
  display: block;
  font-size: var(--text-sm);
  font-weight: 700;
  margin-bottom: 0.4rem;
}

.stat-card strong {
  color: var(--theme-ice);
  font-size: 2rem;
  line-height: 1;
}

.maintenance-panel {
  padding: 1.5rem;
  margin-bottom: 2rem;
}

.progress-card {
  min-width: 260px;
  background: var(--bg-contrast);
  border: 1px solid var(--border-dark);
  border-radius: var(--radius);
  padding: 1rem;
}

.progress-card span,
.progress-card small {
  color: var(--text-muted);
  font-weight: 700;
}

.progress-card strong {
  display: block;
  color: var(--text-light);
  margin: 0.35rem 0 0.55rem;
}

.progress-track {
  width: 100%;
  height: 10px;
  overflow: hidden;
  border-radius: 999px;
  background: rgba(var(--theme-night-rgb), 0.55);
}

.progress-fill {
  height: 100%;
  border-radius: inherit;
  background: linear-gradient(90deg, var(--theme-lavender), var(--theme-ice));
  transition: width 0.25s ease;
}

.filter-row {
  display: flex;
  gap: 1rem;
  margin-bottom: 1rem;
  flex-wrap: wrap;
}

.filter-row--issues {
  align-items: center;
}

.segmented {
  display: inline-flex;
  flex-wrap: wrap;
  gap: 0.45rem;
  padding: 0.35rem;
  background: var(--bg-contrast);
  border: 1px solid var(--border-dark);
  border-radius: 999px;
}

.segmented button {
  border: none;
  border-radius: 999px;
  background: transparent;
  color: var(--text-muted);
  cursor: pointer;
  font-weight: 800;
  padding: 0.55rem 0.8rem;
}

.segmented button.active {
  background: var(--theme-ice);
  color: var(--theme-night);
}

.admin-search-input {
  max-width: 420px;
  margin-left: 0;
}

.filter-select {
  width: auto;
  min-width: 180px;
  background: var(--bg-contrast);
}

.maintenance-table {
  min-width: 1180px;
}

.description-cell {
  min-width: 280px;
  color: var(--text-muted) !important;
}

.empty-cell {
  text-align: center;
  color: var(--text-muted) !important;
  padding: 2rem 1rem !important;
}

.maintenance-badge {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-width: 88px;
  padding: 0.35rem 0.7rem;
  border-radius: 999px;
  font-size: 0.9rem;
  font-weight: 800;
  line-height: 1;
  white-space: nowrap;
  border: 1px solid transparent;
}

.maintenance-badge--priority-low,
.maintenance-badge--status-completed {
  background: rgba(74, 222, 128, 0.16);
  color: #86efac;
  border-color: rgba(74, 222, 128, 0.28);
}

.maintenance-badge--priority-medium {
  background: rgba(250, 204, 21, 0.16);
  color: #fde68a;
  border-color: rgba(250, 204, 21, 0.3);
}

.maintenance-badge--priority-high {
  background: rgba(248, 113, 113, 0.16);
  color: #fca5a5;
  border-color: rgba(248, 113, 113, 0.3);
}

.maintenance-badge--status-pending {
  background: rgba(251, 146, 60, 0.16);
  color: #fdba74;
  border-color: rgba(251, 146, 60, 0.3);
}

.maintenance-badge--status-in-progress {
  background: rgba(96, 165, 250, 0.16);
  color: #93c5fd;
  border-color: rgba(96, 165, 250, 0.3);
}

.state-message {
  background: var(--bg-contrast);
  border: 1px solid var(--border-dark);
  border-radius: var(--radius);
  color: var(--text-muted);
  padding: 1.5rem;
}

.state-message--error {
  color: #fca5a5;
}

@media (max-width: 1200px) {
  .stats-grid {
    grid-template-columns: repeat(3, 1fr);
  }
}

@media (max-width: 768px) {
  .page-head,
  .admin-table-header {
    flex-direction: column;
  }

  .stats-grid {
    grid-template-columns: 1fr;
  }

  .progress-card,
  .filter-select,
  .admin-search-input {
    width: 100%;
    max-width: none;
  }

  .segmented {
    width: 100%;
    border-radius: var(--radius);
  }

  .segmented button {
    flex: 1;
  }
}
</style>
