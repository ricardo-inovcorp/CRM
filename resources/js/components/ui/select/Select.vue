<script setup lang="ts">
import { computed, ref } from 'vue';
import {
  SelectRoot,
  SelectTrigger,
  SelectValue,
  SelectContent,
  SelectItem,
  SelectGroup,
  SelectLabel,
  SelectSeparator,
  SelectPortal
} from 'radix-vue';

const props = defineProps<{
  modelValue?: string | number;
  placeholder?: string;
  disabled?: boolean;
}>();

const emit = defineEmits(['update:modelValue']);

const value = computed({
  get: () => props.modelValue,
  set: (val) => emit('update:modelValue', val)
});

const open = ref(false);
</script>

<template>
  <SelectRoot v-model="value" v-model:open="open" :disabled="disabled">
    <slot name="trigger" :value="value">
      <SelectTrigger class="w-full">
        <SelectValue :placeholder="placeholder" />
      </SelectTrigger>
    </slot>
    <SelectPortal>
      <slot name="content">
        <SelectContent class="z-50 min-w-[8rem] overflow-hidden rounded-md border border-input bg-popover text-popover-foreground shadow-md animate-in fade-in-80">
          <slot />
        </SelectContent>
      </slot>
    </SelectPortal>
  </SelectRoot>
</template>