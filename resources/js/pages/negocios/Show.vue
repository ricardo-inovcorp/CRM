<script setup lang="ts">
import { Link, Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { formatDateTime } from '@/utils';

defineProps({
  negocio: Object,
});

function formatTimestamp(timestamp: string) {
  if (!timestamp) return '';
  return formatDateTime(new Date(timestamp));
}

function getTipoLogClass(tipo: string) {
  switch (tipo) {
    case 'criacao':
    case 'alteracao':
    case 'exclusao':
    default:
      return 'bg-zinc-800 border-zinc-600 border-l-2';
  }
}

function formatValor(valor) {
  if (valor === null || valor === undefined) return '—';
  return valor.toLocaleString('pt-PT', { style: 'currency', currency: 'EUR' });
}
</script>

<template>
  <Head :title="`Negócio: ${negocio.nome}`" />
  <AppLayout>
    <div class="p-6">
      <h1 class="text-2xl font-bold mb-4">Detalhes do Negócio</h1>
      <div class="bg-zinc-800 rounded-lg shadow-lg p-6 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <div class="mb-2"><span class="font-semibold">Nome:</span> {{ negocio.nome }}</div>
            <div class="mb-2"><span class="font-semibold">Tipo:</span> {{ negocio.tipo?.nome || '—' }}</div>
            <div class="mb-2"><span class="font-semibold">Entidade:</span> {{ negocio.entidade?.nome || '—' }}</div>
            <div class="mb-2"><span class="font-semibold">Valor:</span> {{ formatValor(negocio.valor) }}</div>
            <div class="mb-2"><span class="font-semibold">Estado:</span> {{ negocio.estado }}</div>
          </div>
          <div>
            <div class="mb-2"><span class="font-semibold">Contactos:</span>
              <ul class="list-disc ml-6">
                <li v-for="c in negocio.contactos" :key="c.id">{{ c.nome }} {{ c.apelido }}</li>
              </ul>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Histórico de logs -->
      <div class="mb-8">
        <h2 class="text-xl font-semibold mb-4">Histórico de Alterações</h2>
        
        <div v-if="!negocio.logs || negocio.logs.length === 0" class="bg-zinc-800 rounded-lg p-4 text-gray-400 text-center">
          Nenhum histórico de alteração disponível.
        </div>
        
        <div v-else class="space-y-2">
          <div 
            v-for="log in negocio.logs" 
            :key="log.id" 
            class="p-3 rounded-md shadow-sm"
            :class="getTipoLogClass(log.tipo)"
          >
            <div class="flex justify-between items-start mb-2">
              <span class="text-gray-200">{{ log.descricao }}</span>
              <span class="text-sm text-gray-400">{{ formatTimestamp(log.created_at) }}</span>
            </div>
            
            <!-- Detalhes das alterações -->
            <div v-if="log.tipo === 'alteracao' && log.dados_anteriores && log.dados_novos" class="mt-2">
              <details class="cursor-pointer">
                <summary class="text-sm text-gray-400 hover:text-gray-300 transition-colors">Ver detalhes das alterações</summary>
                <div class="pl-3 mt-2 space-y-2 text-sm border-l border-zinc-700">
                  <div v-if="log.dados_anteriores.nome !== log.dados_novos.nome">
                    <span class="text-gray-300">Nome:</span> 
                    <span class="text-gray-400 mr-2">{{ log.dados_anteriores.nome }}</span>
                    <span class="text-gray-300">→</span>
                    <span class="text-gray-300 ml-2">{{ log.dados_novos.nome }}</span>
                  </div>
                  
                  <div v-if="log.dados_anteriores.tipo_id !== log.dados_novos.tipo_id">
                    <span class="text-gray-300">Tipo:</span> 
                    <span class="text-gray-400 mr-2">{{ negocio.tipo?.nome || '—' }}</span>
                    <span class="text-gray-300">→</span>
                    <span class="text-gray-300 ml-2">{{ negocio.tipo?.nome || '—' }}</span>
                  </div>
                  
                  <div v-if="log.dados_anteriores.entidade_id !== log.dados_novos.entidade_id">
                    <span class="text-gray-300">Entidade:</span> 
                    <span class="text-gray-400 mr-2">{{ negocio.entidade?.nome || '—' }}</span>
                    <span class="text-gray-300">→</span>
                    <span class="text-gray-300 ml-2">{{ negocio.entidade?.nome || '—' }}</span>
                  </div>
                  
                  <div v-if="log.dados_anteriores.valor !== log.dados_novos.valor">
                    <span class="text-gray-300">Valor:</span> 
                    <span class="text-gray-400 mr-2">{{ formatValor(log.dados_anteriores.valor) }}</span>
                    <span class="text-gray-300">→</span>
                    <span class="text-gray-300 ml-2">{{ formatValor(log.dados_novos.valor) }}</span>
                  </div>
                  
                  <div v-if="log.dados_anteriores.estado !== log.dados_novos.estado">
                    <span class="text-gray-300">Estado:</span> 
                    <span class="text-gray-400 mr-2">{{ log.dados_anteriores.estado }}</span>
                    <span class="text-gray-300">→</span>
                    <span class="text-gray-300 ml-2">{{ log.dados_novos.estado }}</span>
                  </div>
                </div>
              </details>
            </div>
          </div>
        </div>
      </div>
      
      <div>
        <Link :href="route('negocios.index')" class="px-4 py-2 rounded border">Voltar</Link>
      </div>
    </div>
  </AppLayout>
</template> 