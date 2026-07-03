<template>
  <Teleport to="body">
    <Transition name="confirm-fade">
      <div
        v-if="confirmationState.visible"
        class="confirm-overlay"
        role="presentation"
        @click.self="closeConfirmation(false)"
      >
        <section
          class="confirm-modal"
          role="dialog"
          aria-modal="true"
          :aria-labelledby="titleId"
        >
          <div class="confirm-icon" aria-hidden="true">
            <template v-if="confirmationState.variant === 'danger'">&#10005;</template>
            <template v-else>?</template>
          </div>

          <div class="confirm-copy">
            <h2 :id="titleId">{{ confirmationState.title }}</h2>
            <p>{{ confirmationState.message }}</p>
            <p v-if="confirmationState.detail" class="confirm-detail">
              {{ confirmationState.detail }}
            </p>
          </div>

          <div class="confirm-actions">
            <button type="button" class="btn btn--secondary" @click="closeConfirmation(false)">
              {{ confirmationState.cancelText }}
            </button>
            <button
              type="button"
              :class="['btn', confirmationState.variant === 'danger' ? 'btn--danger' : 'btn--blue']"
              @click="closeConfirmation(true)"
            >
              {{ confirmationState.confirmText }}
            </button>
          </div>
        </section>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { confirmationState, closeConfirmation } from '@/stores/confirmation'

const titleId = 'global-confirmation-title'
</script>

<style scoped>
.confirm-overlay {
  position: fixed;
  inset: 0;
  z-index: 6000;
  display: grid;
  place-items: center;
  padding: 1rem;
  background: rgba(0, 0, 0, 0.68);
  backdrop-filter: blur(5px);
}

.confirm-modal {
  width: min(460px, 100%);
  border-radius: var(--radius-lg);
  border: 1px solid rgba(var(--theme-ice-rgb), 0.16);
  background: linear-gradient(145deg, var(--bg-card), var(--bg-contrast));
  color: var(--text-strong);
  box-shadow: var(--shadow-lg);
  padding: 1.6rem;
  transform-origin: center;
}

.confirm-icon {
  display: inline-grid;
  place-items: center;
  width: 44px;
  height: 44px;
  margin-bottom: 1rem;
  border-radius: 999px;
  background: rgba(239, 68, 68, 0.16);
  color: #fecaca;
  border: 1px solid rgba(239, 68, 68, 0.32);
  font-weight: 900;
}

.confirm-copy h2 {
  margin: 0 0 0.65rem;
  color: var(--theme-ice);
  font-size: 1.35rem;
  font-weight: 800;
}

.confirm-copy p {
  margin: 0;
  color: var(--text-light);
  line-height: 1.65;
}

.confirm-detail {
  margin-top: 0.75rem !important;
  color: var(--text-muted) !important;
  font-size: var(--text-sm);
}

.confirm-actions {
  display: flex;
  justify-content: flex-end;
  gap: 0.75rem;
  margin-top: 1.5rem;
}

.btn {
  border: none;
  border-radius: 999px;
  padding: 0.75rem 1.25rem;
  font-weight: 800;
  cursor: pointer;
  transition: transform 0.2s ease, filter 0.2s ease;
}

.btn:hover {
  transform: translateY(-1px);
  filter: brightness(1.06);
}

.btn--secondary {
  background: rgba(255, 255, 255, 0.08);
  color: var(--text-light);
  border: 1px solid rgba(255, 255, 255, 0.14);
}

.btn--danger {
  background: linear-gradient(135deg, #991b1b, #ef4444);
  color: var(--text-strong);
}

.btn--blue {
  background: var(--accent-blue);
  color: var(--accent-purple);
}

.confirm-fade-enter-active,
.confirm-fade-leave-active {
  transition: opacity 0.2s ease;
}

.confirm-fade-enter-active .confirm-modal,
.confirm-fade-leave-active .confirm-modal {
  transition: transform 0.2s ease, opacity 0.2s ease;
}

.confirm-fade-enter-from,
.confirm-fade-leave-to {
  opacity: 0;
}

.confirm-fade-enter-from .confirm-modal,
.confirm-fade-leave-to .confirm-modal {
  opacity: 0;
  transform: scale(0.96);
}

@media (max-width: 520px) {
  .confirm-actions {
    flex-direction: column-reverse;
  }

  .confirm-actions .btn {
    width: 100%;
  }
}
</style>
