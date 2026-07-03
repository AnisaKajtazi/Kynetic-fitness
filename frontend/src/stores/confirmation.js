import { reactive } from 'vue'

export const confirmationState = reactive({
  visible: false,
  title: '',
  message: '',
  detail: '',
  confirmText: 'Confirm',
  cancelText: 'Cancel',
  variant: 'danger',
  resolve: null,
})

export const requestConfirmation = (options = {}) => new Promise((resolve) => {
  confirmationState.visible = true
  confirmationState.title = options.title || 'Confirm Action'
  confirmationState.message = options.message || 'Are you sure you want to continue?'
  confirmationState.detail = options.detail || ''
  confirmationState.confirmText = options.confirmText || 'Confirm'
  confirmationState.cancelText = options.cancelText || 'Cancel'
  confirmationState.variant = options.variant || 'danger'
  confirmationState.resolve = resolve
})

export const closeConfirmation = (confirmed = false) => {
  const resolve = confirmationState.resolve

  confirmationState.visible = false
  confirmationState.resolve = null

  if (resolve) resolve(confirmed)
}
