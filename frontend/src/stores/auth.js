import { ref, computed } from 'vue'

export const loggedIn = ref(false)
export const roleID = ref(null) // reactive roleID

export function setLoggedIn(value, role = null) {
  loggedIn.value = value
  roleID.value = role

  if (!value) {
    localStorage.removeItem('token')
    localStorage.removeItem('user')
    localStorage.removeItem('role')
    roleID.value = null
  } else if (role) {
    localStorage.setItem('role', role)
  }
}

// Reactive computed helpers
export const isUser = computed(() => loggedIn.value && roleID.value === 2)
export const isStaff = computed(() => loggedIn.value && roleID.value === 3)
export const isAdmin = computed(() => loggedIn.value && roleID.value === 1)
export const isGuest = computed(() => !loggedIn.value)

// Initialize auth from localStorage (kur app starton)
export function initializeAuth() {
  const token = localStorage.getItem('token')
  const role = localStorage.getItem('role')
  if (token && role) {
    setLoggedIn(true, parseInt(role))
  } else {
    setLoggedIn(false)
  }
}
