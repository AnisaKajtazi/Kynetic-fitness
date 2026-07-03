import { reactive } from 'vue'

let nextId = 1

export const notifications = reactive([])

const notify = (type, message, options = {}) => {
  const id = nextId++
  const timeout = options.timeout ?? 4000

  notifications.push({
    id,
    type,
    message,
  })

  if (timeout > 0) {
    window.setTimeout(() => dismissNotification(id), timeout)
  }

  return id
}

export const dismissNotification = (id) => {
  const index = notifications.findIndex((item) => item.id === id)
  if (index !== -1) notifications.splice(index, 1)
}

export const showSuccess = (message, options) => notify('success', message, options)
export const showError = (message, options) => notify('error', message, options)
export const showWarning = (message, options) => notify('warning', message, options)
export const showInfo = (message, options) => notify('info', message, options)
