<script setup lang="ts">
import { TabsContent } from 'radix-vue';

const props = defineProps<{
  value: string;
  forceMount?: boolean;
  class?: string;
}>();
</script>

<template>
  <TabsContent
    :value="value"
    :force-mount="forceMount"
    :class="[
      'mt-2 ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2',
      props.class
    ]"
  >
    <slot />
  </TabsContent>
</template> 