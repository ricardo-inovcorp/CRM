<script setup lang="ts">
import { computed, ref } from 'vue';

const props = defineProps<{
  modelValue?: string;
  name?: string;
  disabled?: boolean;
  options: Array<{value: string, label: string}>;
}>();

const emit = defineEmits(['update:modelValue']);

const value = computed({
  get: () => props.modelValue,
  set: (val) => emit('update:modelValue', val)
});

const handleChange = (val: string) => {
  value.value = val;
};
</script>

<template>
  <div class="flex space-x-4">
    <div v-for="option in options" :key="option.value" class="flex items-center space-x-2">
      <input
        type="radio"
        :id="`radio-${name}-${option.value}`"
        :name="name"
        :value="option.value"
        :checked="value === option.value"
        :disabled="disabled"
        @change="handleChange(option.value)"
        class="h-4 w-4 border-gray-300 text-primary focus:ring-primary"
      />
      <label :for="`radio-${name}-${option.value}`" class="text-sm font-medium leading-none cursor-pointer">
        {{ option.label }}
      </label>
    </div>
  </div>
</template> 