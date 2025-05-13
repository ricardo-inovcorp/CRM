<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { formatDate, formatDateTime } from '@/utils';

interface Props {
  atividade: any;
}

defineProps<Props>();

function formatTimestamp(timestamp: string) {
  if (!timestamp) return '';
  return formatDateTime(new Date(timestamp));
}

function getTipoLogClass(tipo: string) {
  switch (tipo) {
    case 'criacao':
      return 'bg-green-950 border-green-600 text-green-400';
    case 'alteracao':
      return 'bg-blue-950 border-blue-600 text-blue-400';
    case 'exclusao':
      return 'bg-red-950 border-red-600 text-red-400';
    default:
      return 'bg-zinc-800 border-zinc-600 text-zinc-300';
  }
}
</script>

<template>
  <Head :title="`Atividade: ${atividade.tipo?.nome || ''}`" />
  <AppLayout>
    <div class="p-6">
      <h1 class="text-2xl font-bold mb-4">Detalhes da Atividade</h1>
      
      <!-- Detalhes da atividade -->
      <div class="bg-zinc-800 rounded-lg shadow-lg p-6 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <div class="mb-2"><span class="font-semibold">Data:</span> {{ atividade.data }}</div>
            <div class="mb-2"><span class="font-semibold">Hora:</span> {{ atividade.hora }}</div>
            <div class="mb-2"><span class="font-semibold">Duração:</span> {{ atividade.duracao ? atividade.duracao + ' min' : '—' }}</div>
            <div class="mb-2"><span class="font-semibold">Entidade:</span> <a v-if="atividade.entidade" :href="route('entidades.show', atividade.entidade.id)" style="all: unset; cursor: pointer; color: inherit; text-decoration: none;" @click.stop>{{ atividade.entidade.nome }}</a><span v-else>—</span></div>
            <div class="mb-2"><span class="font-semibold">Contacto:</span> <a v-if="atividade.contacto" :href="route('contactos.show', atividade.contacto.id)" style="all: unset; cursor: pointer; color: inherit; text-decoration: none;" @click.stop>{{ atividade.contacto.nome }} {{ atividade.contacto.apelido }}</a><span v-else>—</span></div>
            <div class="mb-2"><span class="font-semibold">Tipo:</span> {{ atividade.tipo?.nome || '—' }}</div>
          </div>
          <div>
            <div class="mb-2"><span class="font-semibold">Descrição:</span> {{ atividade.descricao || '—' }}</div>
          </div>
        </div>
      </div>
      
      <!-- Histórico de logs -->
      <div class="mb-8">
        <h2 class="text-xl font-semibold mb-4">Histórico de Alterações</h2>
        
        <div v-if="!atividade.logs || atividade.logs.length === 0" class="bg-zinc-800 rounded-lg p-4 text-gray-400 text-center">
          Nenhum histórico de alteração disponível.
        </div>
        
        <div v-else class="space-y-4">
          <div 
            v-for="log in atividade.logs" 
            :key="log.id" 
            class="border-l-4 p-4 rounded-r-lg shadow-md"
            :class="getTipoLogClass(log.tipo)"
          >
            <div class="flex justify-between items-start mb-2">
              <span class="font-medium">{{ log.descricao }}</span>
              <span class="text-sm opacity-80">{{ formatTimestamp(log.created_at) }}</span>
            </div>
            
            <div class="mt-2 text-sm">
              <!-- Detalhes simplificados das alterações - apenas para logs de alteração -->
              <div v-if="log.tipo === 'alteracao' && log.dados_anteriores && log.dados_novos" class="mt-2">
                <details class="cursor-pointer">
                  <summary class="font-medium mb-2">Ver detalhes das alterações</summary>
                  <div class="pl-4 mt-2 space-y-2">
                    <div v-if="log.dados_anteriores.data !== log.dados_novos.data">
                      <span class="font-medium">Data:</span> 
                      <span class="line-through mr-2">{{ log.dados_anteriores.data }}</span>
                      <span class="text-green-400">{{ log.dados_novos.data }}</span>
                    </div>
                    
                    <div v-if="log.dados_anteriores.hora !== log.dados_novos.hora">
                      <span class="font-medium">Hora:</span> 
                      <span class="line-through mr-2">{{ log.dados_anteriores.hora }}</span>
                      <span class="text-green-400">{{ log.dados_novos.hora }}</span>
                    </div>
                    
                    <div v-if="log.dados_anteriores.duracao !== log.dados_novos.duracao">
                      <span class="font-medium">Duração:</span> 
                      <span class="line-through mr-2">{{ log.dados_anteriores.duracao || '—' }} min</span>
                      <span class="text-green-400">{{ log.dados_novos.duracao || '—' }} min</span>
                    </div>
                    
                    <div v-if="log.dados_anteriores.descricao !== log.dados_novos.descricao">
                      <span class="font-medium">Descrição:</span> 
                      <div class="mt-1 pl-3 border-l-2 border-zinc-600">
                        <div class="line-through">{{ log.dados_anteriores.descricao || '—' }}</div>
                        <div class="text-green-400 mt-1">{{ log.dados_novos.descricao || '—' }}</div>
                      </div>
                    </div>
                  </div>
                </details>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template> 