<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import DebugPanel from '@/components/DebugPanel.vue';
</script>

<template>
  <Head title="Teste de Erro" />
  <DebugPanel />
  
  <div class="min-h-screen bg-gray-100 flex flex-col items-center justify-center p-6">
    <div class="bg-white p-8 rounded-lg shadow-lg max-w-md w-full">
      <h1 class="text-2xl font-bold mb-4">Página de Teste</h1>
      <p class="mb-4">Se você está vendo esta página, o Vue está funcionando corretamente.</p>
      <p>Esta é uma página simples sem dependências complexas.</p>
    </div>
  </div>
</template> 