<script setup lang="ts">
import { computed } from 'vue';

const props = defineProps({
  value: {
    type: Number,
    default: 0
  },
  max: {
    type: Number,
    default: 100
  },
  class: {
    type: String,
    default: ''
  }
});

const percentage = computed(() => {
  const value = Math.min(Math.max(props.value, 0), props.max);
  return (value / props.max) * 100;
});
</script>

<template>
  <div
    class="relative h-2 w-full overflow-hidden rounded-full bg-gray-200 dark:bg-gray-700"
    :class="class"
    role="progressbar"
    :aria-valuenow="value"
    aria-valuemin="0"
    :aria-valuemax="max"
  >
    <div
      class="h-full w-full flex-1 bg-primary transition-all duration-500 ease-in-out dark:bg-primary"
      :style="{ width: `${percentage}%` }"
    />
  </div>
</template> 