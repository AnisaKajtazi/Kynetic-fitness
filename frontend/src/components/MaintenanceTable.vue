<template>
  <section class="maintenance-section admin-table-panel">
    <div class="admin-table-header">
      <div class="admin-table-title-block">
        <h2>{{ title }}</h2>
        <p v-if="subtitle">{{ subtitle }}</p>
      </div>

      <div class="section-actions">
        <span class="section-count">{{ rows.length }} items</span>
        <button
          v-if="canAdd"
          class="btn btn--blue admin-create-btn"
          type="button"
          @click="$emit('add')"
        >
          Add Issue
        </button>
      </div>
    </div>

    <div class="admin-table-shell">
      <table class="admin-table maintenance-table">
        <thead>
          <tr>
            <th v-if="mode === 'tasks'" class="check-col">Done</th>
            <th>{{ mode === 'issues' ? 'Equipment / Title' : 'Title' }}</th>
            <th>Location</th>
            <th>Priority</th>
            <th>Status</th>
            <th v-if="mode === 'tasks'">Due Date</th>
            <th>Description</th>
            <th v-if="mode === 'issues'" class="actions-col">Actions</th>
          </tr>
        </thead>

        <tbody>
          <tr v-if="!rows.length">
            <td :colspan="colspan" class="empty-cell">{{ emptyText }}</td>
          </tr>

          <tr
            v-for="row in rows"
            :key="row.id"
            :class="{ 'row-completed': isCompleted(row) }"
          >
            <td v-if="mode === 'tasks'" class="check-col">
              <label class="task-check">
                <input
                  type="checkbox"
                  :checked="isCompleted(row)"
                  :disabled="busyId === row.id"
                  @change="$emit('toggle-complete', row, $event.target.checked)"
                />
                <span></span>
              </label>
            </td>

            <td>
              <strong :class="['item-title', { 'title-complete': isCompleted(row) }]">
                {{ row.title }}
              </strong>
            </td>
            <td>{{ row.location || "-" }}</td>
            <td>
              <span :class="['maintenance-badge', priorityClass(row.priority)]">
                {{ row.priority }}
              </span>
            </td>
            <td>
              <span :class="['maintenance-badge', statusClass(row.status)]">
                {{ row.status }}
              </span>
            </td>
            <td v-if="mode === 'tasks'">{{ formatDate(row.due_date) }}</td>
            <td class="description-cell">{{ row.description || "-" }}</td>
            <td v-if="mode === 'issues'" class="actions-col">
              <div class="admin-actions">
                <AdminActionButton
                  title="Edit issue"
                  variant="edit"
                  @click="$emit('edit', row)"
                />
                <AdminActionButton
                  title="Delete issue"
                  variant="delete"
                  @click="$emit('delete', row)"
                />
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </section>
</template>

<script setup>
import { computed } from "vue"
import AdminActionButton from "@/components/AdminActionButton.vue"

const props = defineProps({
  title: {
    type: String,
    required: true,
  },
  subtitle: {
    type: String,
    default: "",
  },
  rows: {
    type: Array,
    default: () => [],
  },
  mode: {
    type: String,
    default: "tasks",
  },
  emptyText: {
    type: String,
    default: "No records to display.",
  },
  canAdd: {
    type: Boolean,
    default: false,
  },
  busyId: {
    type: [Number, String, null],
    default: null,
  },
})

defineEmits(["add", "edit", "delete", "toggle-complete"])

const normalize = (value) => String(value || "").toLowerCase().replace(/\s+/g, "-")
const priorityClass = (priority) => `maintenance-badge--priority-${normalize(priority)}`
const statusClass = (status) => `maintenance-badge--status-${normalize(status)}`
const isCompleted = (row) => row.status === "Completed"
const colspan = computed(() => (props.mode === "tasks" ? 7 : 6))

const formatDate = (value) => {
  if (!value) return "-"
  return new Date(value).toLocaleDateString("en-US", {
    year: "numeric",
    month: "short",
    day: "numeric",
  })
}
</script>

<style scoped>
.maintenance-section {
  padding: 1.5rem;
}

.admin-table-title-block p {
  color: var(--text-muted);
  margin: 0;
}

.section-actions {
  display: flex;
  align-items: center;
  gap: 0.85rem;
  flex-wrap: wrap;
  justify-content: flex-end;
}

.section-count {
  color: var(--text-muted);
  font-weight: 800;
  white-space: nowrap;
}

.maintenance-table {
  min-width: 1120px;
}

.maintenance-table th,
.maintenance-table td {
  text-align: left;
}

.maintenance-table tbody tr {
  transition: background 0.2s ease, opacity 0.2s ease;
}

.maintenance-table tbody tr:hover > * {
  background-color: rgba(var(--theme-ice-rgb), 0.1);
}

.row-completed {
  opacity: 0.68;
}

.check-col {
  width: 82px;
  text-align: center !important;
}

.actions-col {
  width: 110px;
  text-align: center !important;
}

.item-title {
  color: var(--text-light);
  font-weight: 800;
}

.title-complete {
  text-decoration: line-through;
  text-decoration-color: rgba(var(--theme-ice-rgb), 0.75);
}

.description-cell {
  min-width: 280px;
  color: var(--text-muted) !important;
}

.empty-cell {
  text-align: center !important;
  color: var(--text-muted) !important;
  padding: 2rem 1rem !important;
}

.task-check {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 38px;
  height: 38px;
  cursor: pointer;
}

.task-check input {
  position: absolute;
  opacity: 0;
  pointer-events: none;
}

.task-check span {
  display: inline-flex;
  width: 34px;
  height: 34px;
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

@media (max-width: 768px) {
  .maintenance-section {
    padding: 1rem;
  }

  .section-actions {
    align-items: flex-start;
    justify-content: flex-start;
  }

  .section-actions .btn {
    width: 100%;
  }
}
</style>
