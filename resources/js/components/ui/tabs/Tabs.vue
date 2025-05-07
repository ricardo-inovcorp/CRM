<script setup lang="ts">
import { TabsRoot } from 'radix-vue';
import { computed } from 'vue';

const props = defineProps<{
  defaultValue?: string;
  modelValue?: string;
  class?: string;
}>();

const emit = defineEmits(['update:modelValue']);

const tabsValue = computed({
  get: () => props.modelValue,
  set: (value) => emit('update:modelValue', value),
});
</script>

<template>
  <TabsRoot 
    :default-value="defaultValue" 
    v-model="tabsValue" 
    :class="['w-full', props.class]"
    orientation="horizontal"
  >
    <slot />
  </TabsRoot>
</template> 