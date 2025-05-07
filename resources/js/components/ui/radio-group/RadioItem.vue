<script setup lang="ts">
import { cn } from '@/lib/utils';

const props = defineProps<{
  value: string;
  label: string;
  modelValue?: string;
  name?: string;
  disabled?: boolean;
}>();

const emit = defineEmits(['change']);

const handleChange = () => {
  if (!props.disabled) {
    emit('change', props.value);
  }
};
</script>

<template>
  <div class="flex items-center space-x-2">
    <input
      type="radio"
      :id="`radio-${value}`"
      :name="name"
      :value="value"
      :checked="modelValue === value"
      :disabled="disabled"
      @change="handleChange"
      class="h-4 w-4 border-gray-300 text-primary focus:ring-primary"
    />
    <label :for="`radio-${value}`" class="text-sm font-medium leading-none cursor-pointer">
      {{ label }}
    </label>
  </div>
</template> 