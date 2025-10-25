import { ref } from 'vue'

// Global toast state
const toasts = ref([])
let nextId = 0

export function useToast() {
  const showToast = (message, type = 'success', duration = 3000) => {
    const id = nextId++
    const toast = {
      id,
      message,
      type,
      visible: true,
    }

    toasts.value.push(toast)

    // Auto-dismiss after duration
    if (duration > 0) {
      setTimeout(() => {
        removeToast(id)
      }, duration)
    }

    return id
  }

  const removeToast = (id) => {
    const index = toasts.value.findIndex((t) => t.id === id)
    if (index > -1) {
      toasts.value.splice(index, 1)
    }
  }

  const success = (message, duration) => {
    return showToast(message, 'success', duration)
  }

  const error = (message, duration) => {
    return showToast(message, 'error', duration)
  }

  const info = (message, duration) => {
    return showToast(message, 'info', duration)
  }

  const warning = (message, duration) => {
    return showToast(message, 'warning', duration)
  }

  return {
    toasts,
    showToast,
    removeToast,
    success,
    error,
    info,
    warning,
  }
}
