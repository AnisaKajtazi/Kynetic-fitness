<template>
  <Teleport to="body">
    <TransitionGroup name="toast" tag="div" class="toast-stack">
      <article
        v-for="notification in notifications"
        :key="notification.id"
        :class="['toast-card', `toast-card--${notification.type}`]"
      >
        <span class="toast-icon" aria-hidden="true">
          <template v-if="notification.type === 'success'">&#10003;</template>
          <template v-else-if="notification.type === 'error'">&#10005;</template>
          <template v-else-if="notification.type === 'warning'">!</template>
          <template v-else>i</template>
        </span>
        <p>{{ notification.message }}</p>
        <button
          type="button"
          class="toast-close"
          aria-label="Dismiss notification"
          @click="dismissNotification(notification.id)"
        >
          &times;
        </button>
      </article>
    </TransitionGroup>
  </Teleport>
</template>

<script setup>
import { dismissNotification, notifications } from '@/stores/notifications'
</script>

<style scoped>
.toast-stack {
  position: fixed;
  top: 1rem;
  right: 1rem;
  z-index: 7000;
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
  width: min(420px, calc(100vw - 2rem));
  pointer-events: none;
}

.toast-card {
  display: grid;
  grid-template-columns: auto 1fr auto;
  align-items: center;
  gap: 0.8rem;
  min-height: 64px;
  padding: 0.9rem 0.95rem;
  border-radius: var(--radius);
  border: 1px solid rgba(255, 255, 255, 0.12);
  box-shadow: var(--shadow-md);
  color: var(--text-strong);
  pointer-events: auto;
  backdrop-filter: blur(10px);
}

.toast-card p {
  color: inherit;
  margin: 0;
  font-size: var(--text-sm);
  font-weight: 700;
  line-height: 1.35;
}

.toast-icon {
  display: inline-grid;
  place-items: center;
  width: 34px;
  height: 34px;
  border-radius: 999px;
  background: rgba(255, 255, 255, 0.18);
  font-weight: 900;
}

.toast-close {
  display: inline-grid;
  place-items: center;
  width: 30px;
  height: 30px;
  border: none;
  border-radius: 999px;
  background: rgba(255, 255, 255, 0.14);
  color: inherit;
  cursor: pointer;
  font-size: 1.25rem;
  line-height: 1;
  transition: transform 0.2s ease, background 0.2s ease;
}

.toast-close:hover {
  transform: scale(1.05);
  background: rgba(255, 255, 255, 0.22);
}

.toast-card--success {
  background: linear-gradient(135deg, rgba(22, 101, 52, 0.96), rgba(34, 197, 94, 0.78));
}

.toast-card--error {
  background: linear-gradient(135deg, rgba(127, 29, 29, 0.96), rgba(239, 68, 68, 0.78));
}

.toast-card--warning {
  background: linear-gradient(135deg, rgba(146, 64, 14, 0.96), rgba(245, 158, 11, 0.82));
}

.toast-card--info {
  background: linear-gradient(135deg, rgba(30, 64, 175, 0.96), rgba(96, 165, 250, 0.78));
}

.toast-enter-active,
.toast-leave-active {
  transition: opacity 0.25s ease, transform 0.25s ease;
}

.toast-enter-from,
.toast-leave-to {
  opacity: 0;
  transform: translateX(24px);
}

.toast-move {
  transition: transform 0.25s ease;
}

@media (max-width: 640px) {
  .toast-stack {
    top: auto;
    right: 0.75rem;
    bottom: 0.75rem;
    width: calc(100vw - 1.5rem);
  }
}
</style>
