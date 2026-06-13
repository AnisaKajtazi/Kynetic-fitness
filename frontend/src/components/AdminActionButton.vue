<template>
  <button
    type="button"
    :class="['admin-icon-btn', `admin-icon-btn--${variant}`]"
    :title="title"
    :aria-label="title"
    @click.stop="$emit('click', $event)"
  >
    <span aria-hidden="true">{{ icon }}</span>
  </button>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  variant: {
    type: String,
    default: 'edit',
  },
  title: {
    type: String,
    required: true,
  },
})

defineEmits(['click'])

const iconMap = {
  edit: '✎',
  delete: '🗑',
  save: '✓',
}

const icon = computed(() => iconMap[props.variant] || '•')
</script>