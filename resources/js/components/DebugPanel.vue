<script setup lang="ts">
import { ref, onMounted, onErrorCaptured } from 'vue';
import { usePage } from '@inertiajs/vue3';

const errors = ref<string[]>([]);
const info = ref<Record<string, any>>({});
const page = usePage();

onMounted(() => {
  try {
    info.value = {
      vueVersion: '3.x',
      inertiaVersion: 'latest',
      currentRoute: window.location.pathname,
      timestamp: new Date().toISOString(),
      props: JSON.parse(JSON.stringify(page.props)),
      component: page.component.value,
    };
    
    console.log('Debug Info:', info.value);
    console.log('Page Props:', page.props);
    
    // Adicionar listener global para erros
    window.addEventListener('error', (event) => {
      errors.value.push(`JS Error: ${event.message} at ${event.filename}:${event.lineno}`);
      console.error('Global Error:', event);
    });
    
  } catch (err) {
    console.error('Error in DebugPanel setup:', err);
    errors.value.push(`Setup Error: ${err}`);
  }
});

onErrorCaptured((err, instance, info) => {
  errors.value.push(`Vue Error: ${err} (${info})`);
  console.error('Vue Error:', err);
  return false; // permite que o erro se propague
});
</script>

<template>
  <div class="debug-panel fixed bottom-0 right-0 m-4 p-4 bg-black text-white rounded opacity-90 z-50 max-w-md max-h-80 overflow-auto">
    <h3 class="text-lg font-bold mb-2">Debug Panel</h3>
    
    <div class="mb-4">
      <h4 class="font-semibold">Info:</h4>
      <pre class="text-xs">{{ JSON.stringify(info, null, 2) }}</pre>
    </div>
    
    <div v-if="errors.length">
      <h4 class="font-semibold text-red-400">Errors:</h4>
      <ul class="text-xs">
        <li v-for="(error, index) in errors" :key="index" class="mb-1 text-red-300">
          {{ error }}
        </li>
      </ul>
    </div>
    <div v-else>
      <p class="text-green-400 text-xs">Nenhum erro capturado</p>
    </div>
  </div>
</template> 