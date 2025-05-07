<script setup lang="ts">
import { TabsList } from 'radix-vue';

const props = defineProps<{
  class?: string;
}>();
</script>

<template>
  <TabsList
    :class="[
      'inline-flex h-10 items-center justify-center rounded-md bg-muted p-1 text-muted-foreground',
      props.class
    ]"
  >
    <slot />
  </TabsList>
</template> 