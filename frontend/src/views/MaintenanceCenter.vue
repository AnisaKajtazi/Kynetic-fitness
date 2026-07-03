<template>
  <div class="maintenance-layout">
    <Sidebar />

    <main class="maintenance-main">
      <header class="page-header">
        <h1>Maintenance Center</h1>
        <p>Manage daily task completion and keep equipment issues visible for the team.</p>
      </header>

      <div v-if="loading" class="state-message">Loading maintenance center...</div>
      <div v-else-if="error" class="state-message state-message--error">{{ error }}</div>

      <div v-else class="sections">
        <MaintenanceTable
          title="Today's Tasks"
          subtitle="Mark daily maintenance work as completed as you move through the facility."
          :rows="taskRows"
          mode="tasks"
          :busy-id="busyTaskId"
          empty-text="No maintenance tasks found."
          @toggle-complete="toggleTaskCompletion"
        />

        <MaintenanceTable
          title="Equipment Issues"
          subtitle="Create, update, and remove equipment issues that need attention."
          :rows="issueRows"
          mode="issues"
          can-add
          empty-text="No equipment issues found."
          @add="openIssueModal"
          @edit="openIssueModal"
          @delete="deleteIssue"
        />
      </div>

      <div v-if="showIssueModal" class="modal-overlay" @click.self="closeIssueModal">
        <form class="issue-modal" @submit.prevent="saveIssue">
          <div class="modal-head">
            <h2>{{ editingIssue ? "Edit Equipment Issue" : "Add Equipment Issue" }}</h2>
            <button type="button" class="btn btn--accent modal-close-btn" @click="closeIssueModal">Close</button>
          </div>

          <label>
            Title
            <input v-model.trim="issueForm.title" type="text" placeholder="Example: Treadmill belt slipping" />
          </label>

          <label>
            Description
            <textarea v-model.trim="issueForm.description" rows="4" placeholder="Describe the issue clearly"></textarea>
          </label>

          <label>
            Location
            <input v-model.trim="issueForm.location" type="text" placeholder="Example: Cardio zone" />
          </label>

          <div class="form-grid">
            <label>
              Priority
              <select v-model="issueForm.priority">
                <option>Low</option>
                <option>Medium</option>
                <option>High</option>
              </select>
            </label>

            <label>
              Status
              <select v-model="issueForm.status">
                <option>Pending</option>
                <option>In Progress</option>
                <option>Completed</option>
              </select>
            </label>
          </div>

          <p v-if="formError" class="form-error">{{ formError }}</p>

          <div class="modal-actions">
            <button type="button" class="btn btn--accent" @click="closeIssueModal">Cancel</button>
            <button type="submit" class="btn btn--blue" :disabled="savingIssue">
              {{ savingIssue ? "Saving..." : "Save Issue" }}
            </button>
          </div>
        </form>
      </div>
    </main>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from "vue"
import Sidebar from "@/components/Sidebar.vue"
import MaintenanceTable from "@/components/MaintenanceTable.vue"
import api from "@/services/axios"
import { requestConfirmation } from "@/stores/confirmation"
import { showSuccess, showError } from "@/stores/notifications"

const records = ref([])
const loading = ref(true)
const error = ref("")
const busyTaskId = ref(null)
const showIssueModal = ref(false)
const editingIssue = ref(null)
const savingIssue = ref(false)
const formError = ref("")

const emptyIssueForm = () => ({
  title: "",
  description: "",
  location: "",
  priority: "Medium",
  status: "Pending",
})

const issueForm = ref(emptyIssueForm())

const taskRows = computed(() => records.value.filter((item) => item.type === "Task"))
const issueRows = computed(() => records.value.filter((item) => item.type === "Equipment Issue"))

const fetchMaintenanceTasks = async () => {
  loading.value = true
  error.value = ""

  try {
    const { data } = await api.get("maintenance-tasks")
    records.value = Array.isArray(data) ? data : []
  } catch (err) {
    if (err.response?.status === 401) {
      error.value = "Your session expired. Please log in again."
      return
    }

    if (err.response?.status === 403) {
      error.value = "Only maintenance staff can access the Maintenance Center."
      return
    }

    console.error("Error loading maintenance tasks:", err)
    error.value = "Maintenance records could not be loaded."
  } finally {
    loading.value = false
  }
}

const replaceRecord = (updatedRecord) => {
  records.value = records.value.map((record) =>
    record.id === updatedRecord.id ? updatedRecord : record
  )
}

const toggleTaskCompletion = async (task, completed) => {
  const previous = { ...task }
  busyTaskId.value = task.id
  replaceRecord({ ...task, status: completed ? "Completed" : "Pending" })

  try {
    const { data } = await api.patch(`maintenance-tasks/${task.id}/completion`, { completed })
    replaceRecord(data)
    showSuccess(completed ? "Task marked as completed." : "Task moved back to pending.")
  } catch (err) {
    console.error("Error updating task completion:", err)
    replaceRecord(previous)
    showError("Task could not be updated.")
  } finally {
    busyTaskId.value = null
  }
}

const openIssueModal = (issue = null) => {
  editingIssue.value = issue
  issueForm.value = issue
    ? {
        title: issue.title || "",
        description: issue.description || "",
        location: issue.location || "",
        priority: issue.priority || "Medium",
        status: issue.status || "Pending",
      }
    : emptyIssueForm()
  formError.value = ""
  showIssueModal.value = true
}

const closeIssueModal = () => {
  showIssueModal.value = false
  editingIssue.value = null
  issueForm.value = emptyIssueForm()
  formError.value = ""
}

const validateIssueForm = () => {
  if (!issueForm.value.title || !issueForm.value.description || !issueForm.value.location) {
    formError.value = "Title, description, and location are required."
    return false
  }

  formError.value = ""
  return true
}

const saveIssue = async () => {
  if (!validateIssueForm()) return

  savingIssue.value = true

  try {
    const payload = { ...issueForm.value }
    const request = editingIssue.value
      ? api.put(`maintenance-tasks/issues/${editingIssue.value.id}`, payload)
      : api.post("maintenance-tasks/issues", payload)

    const { data } = await request

    if (editingIssue.value) {
      replaceRecord(data)
      showSuccess("Equipment issue updated.")
    } else {
      records.value = [data, ...records.value]
      showSuccess("Equipment issue created.")
    }

    closeIssueModal()
  } catch (err) {
    console.error("Error saving issue:", err)
    formError.value = err.response?.data?.message || "Issue could not be saved."
  } finally {
    savingIssue.value = false
  }
}

const deleteIssue = async (issue) => {
  const confirmed = await requestConfirmation({
    title: "Delete Equipment Issue",
    message: "Are you sure you want to delete this equipment issue?",
    detail: "This action cannot be undone.",
    confirmText: "Delete",
  })
  if (!confirmed) return

  const previous = [...records.value]
  records.value = records.value.filter((record) => record.id !== issue.id)

  try {
    await api.delete(`maintenance-tasks/issues/${issue.id}`)
    showSuccess("Equipment issue deleted.")
  } catch (err) {
    console.error("Error deleting issue:", err)
    records.value = previous
    showError("Issue could not be deleted.")
  }
}

onMounted(fetchMaintenanceTasks)
</script>

<style scoped>
.maintenance-layout {
  display: flex;
  width: 100vw;
  min-height: 100vh;
  background: var(--bg-dark);
  color: var(--text-light);
}

.maintenance-layout :deep(.sidebar) {
  position: fixed;
  top: 0;
  left: 0;
  width: 230px;
  height: 100vh;
  z-index: 1000;
}

.maintenance-main {
  margin-left: 230px;
  width: calc(100vw - 230px);
  min-height: 100vh;
  padding: 5rem;
  overflow-y: auto;
  box-sizing: border-box;
  background: var(--bg-card);
  border-left: 1px solid var(--border-dark);
}

.page-header {
  margin-bottom: 2.5rem;
}

.page-header h1 {
  color: var(--theme-ice);
  font-size: 2.2rem;
  margin-bottom: 0.5rem;
}

.page-header p {
  color: var(--text-muted);
  max-width: 760px;
  margin: 0;
}

.sections {
  display: flex;
  flex-direction: column;
  gap: 3rem;
}

.state-message {
  color: var(--text-muted);
  background: var(--bg-card);
  border: 1px solid var(--border-dark);
  border-radius: var(--radius);
  padding: 1.5rem;
}

.state-message--error {
  color: #fca5a5;
}

.modal-overlay {
  position: fixed;
  inset: 0;
  z-index: 2000;
  display: grid;
  place-items: center;
  padding: 1rem;
  background: rgba(0, 0, 0, 0.68);
}

.issue-modal {
  width: min(620px, 100%);
  background: var(--bg-card);
  border: 1px solid var(--border-dark);
  border-radius: var(--radius-lg);
  box-shadow: var(--shadow-md);
  color: var(--text-light);
  padding: 1.5rem;
}

.modal-head {
  display: flex;
  justify-content: space-between;
  gap: 1rem;
  margin-bottom: 1.25rem;
}

.modal-head h2 {
  color: var(--theme-ice);
  margin: 0;
}

.modal-close-btn {
  padding: 0.55rem 1rem;
  font-size: var(--text-sm);
}

.issue-modal label {
  display: flex;
  flex-direction: column;
  gap: 0.45rem;
  color: var(--text-muted);
  font-weight: 800;
  margin-bottom: 1rem;
}

.issue-modal input,
.issue-modal textarea,
.issue-modal select {
  background: var(--bg-contrast);
  border: 1px solid var(--border-dark);
  border-radius: var(--radius);
  color: var(--text-light);
  font: inherit;
  padding: 0.75rem 0.85rem;
}

.issue-modal input:focus,
.issue-modal textarea:focus,
.issue-modal select:focus {
  border-color: var(--theme-ice);
  box-shadow: 0 0 0 0.2rem rgba(var(--theme-ice-rgb), 0.16);
  outline: none;
}

.form-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 1rem;
}

.form-error {
  color: #fca5a5;
  font-weight: 800;
  margin: 0 0 1rem;
}

.modal-actions {
  display: flex;
  justify-content: flex-end;
  gap: 0.75rem;
}

.modal-actions .btn:disabled {
  cursor: not-allowed;
  opacity: 0.6;
}

@media (max-width: 768px) {
  .maintenance-layout {
    flex-direction: column;
  }

  .maintenance-layout :deep(.sidebar) {
    position: relative;
    width: 100%;
    height: auto;
    padding: 1rem;
  }

  .maintenance-main {
    margin-left: 0;
    width: 100%;
    padding: 1.25rem;
    border-left: none;
  }

  .form-grid {
    grid-template-columns: 1fr;
    gap: 0;
  }

  .modal-actions {
    flex-direction: column-reverse;
  }
}
</style>
