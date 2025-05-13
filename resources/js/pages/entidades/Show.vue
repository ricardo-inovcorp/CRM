<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { computed } from 'vue';
import { formatDateTime } from '@/utils';

interface Props {
  entidade: any;
}

const props = defineProps<Props>();

const paisNome = computed(() => props.entidade?.pais?.nome || '—');

function formatTimestamp(timestamp: string) {
  if (!timestamp) return '';
  return formatDateTime(new Date(timestamp));
}

function getTipoLogClass(tipo: string) {
  switch (tipo) {
    case 'criacao':
      return 'bg-zinc-800 border-zinc-600 border-l-2';
    case 'alteracao':
      return 'bg-zinc-800 border-zinc-600 border-l-2';
    case 'exclusao':
      return 'bg-zinc-800 border-zinc-600 border-l-2';
    default:
      return 'bg-zinc-800 border-zinc-600 border-l-2';
  }
}
</script>

<template>
  <Head :title="props.entidade?.nome ? `Entidade: ${props.entidade.nome}` : 'Entidade'" />
  <AppLayout>
    <div class="p-6">
      <template v-if="props.entidade && props.entidade.nome">
        <h1 class="text-2xl font-bold mb-4">Detalhes da Entidade</h1>
        <div class="bg-zinc-800 rounded-lg shadow-lg p-6 mb-8">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <div class="mb-2"><span class="font-semibold">Nome:</span> {{ props.entidade.nome }}</div>
              <div class="mb-2"><span class="font-semibold">Email:</span> {{ props.entidade.email || '—' }}</div>
              <div class="mb-2"><span class="font-semibold">Telefone:</span> {{ props.entidade.telefone || '—' }}</div>
              <div class="mb-2"><span class="font-semibold">Estado:</span> {{ props.entidade.estado }}</div>
              <div class="mb-2"><span class="font-semibold">País:</span> {{ paisNome }}</div>
              <div class="mb-2"><span class="font-semibold">Morada:</span> {{ props.entidade.morada || '—' }}</div>
              <div class="mb-2"><span class="font-semibold">Código Postal:</span> {{ props.entidade.codigo_postal || '—' }}</div>
              <div class="mb-2"><span class="font-semibold">Localidade:</span> {{ props.entidade.localidade || '—' }}</div>
              <div class="mb-2"><span class="font-semibold">Website:</span> <a v-if="props.entidade.website" :href="props.entidade.website" style="all: unset; color: inherit; text-decoration: none;" target="_blank">{{ props.entidade.website }}</a><span v-else>—</span></div>
            </div>
            <div>
              <div class="mb-2"><span class="font-semibold">Observações:</span> {{ props.entidade.observacoes || '—' }}</div>
            </div>
          </div>
        </div>
        
        <!-- Histórico de logs -->
        <div class="mb-8">
          <h2 class="text-xl font-semibold mb-4">Histórico de Alterações</h2>
          
          <div v-if="!props.entidade.logs || props.entidade.logs.length === 0" class="bg-zinc-800 rounded-lg p-4 text-gray-400 text-center">
            Nenhum histórico de alteração disponível.
          </div>
          
          <div v-else class="space-y-2">
            <div 
              v-for="log in props.entidade.logs" 
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
                    
                    <div v-if="log.dados_anteriores.telefone !== log.dados_novos.telefone">
                      <span class="text-gray-300">Telefone:</span> 
                      <span class="text-gray-400 mr-2">{{ log.dados_anteriores.telefone || '—' }}</span>
                      <span class="text-gray-300">→</span>
                      <span class="text-gray-300 ml-2">{{ log.dados_novos.telefone || '—' }}</span>
                    </div>
                    
                    <div v-if="log.dados_anteriores.email !== log.dados_novos.email">
                      <span class="text-gray-300">Email:</span> 
                      <span class="text-gray-400 mr-2">{{ log.dados_anteriores.email || '—' }}</span>
                      <span class="text-gray-300">→</span>
                      <span class="text-gray-300 ml-2">{{ log.dados_novos.email || '—' }}</span>
                    </div>
                    
                    <div v-if="log.dados_anteriores.estado !== log.dados_novos.estado">
                      <span class="text-gray-300">Estado:</span> 
                      <span class="text-gray-400 mr-2">{{ log.dados_anteriores.estado }}</span>
                      <span class="text-gray-300">→</span>
                      <span class="text-gray-300 ml-2">{{ log.dados_novos.estado }}</span>
                    </div>
                    
                    <div v-if="log.dados_anteriores.morada !== log.dados_novos.morada">
                      <span class="text-gray-300">Morada:</span> 
                      <span class="text-gray-400 mr-2">{{ log.dados_anteriores.morada || '—' }}</span>
                      <span class="text-gray-300">→</span>
                      <span class="text-gray-300 ml-2">{{ log.dados_novos.morada || '—' }}</span>
                    </div>
                    
                    <div v-if="log.dados_anteriores.website !== log.dados_novos.website">
                      <span class="text-gray-300">Website:</span> 
                      <span class="text-gray-400 mr-2">{{ log.dados_anteriores.website || '—' }}</span>
                      <span class="text-gray-300">→</span>
                      <span class="text-gray-300 ml-2">{{ log.dados_novos.website || '—' }}</span>
                    </div>
                    
                    <div v-if="log.dados_anteriores.observacoes !== log.dados_novos.observacoes">
                      <span class="text-gray-300">Observações:</span> 
                      <div class="mt-1 pl-3 border-l border-zinc-700">
                        <div class="text-gray-400">{{ log.dados_anteriores.observacoes || '—' }}</div>
                        <div class="text-gray-300 mt-1 flex items-center">
                          <span class="text-gray-400 mr-2">→</span>
                          {{ log.dados_novos.observacoes || '—' }}
                        </div>
                      </div>
                    </div>
                  </div>
                </details>
              </div>
            </div>
          </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
          <div>
            <h2 class="text-xl font-semibold mb-2">Contactos</h2>
            <div v-if="props.entidade.contactos && props.entidade.contactos.length">
              <ul class="divide-y divide-zinc-700">
                <li v-for="c in props.entidade.contactos" :key="c.id" class="py-2 flex justify-between items-center">
                  <span>{{ c.nome }} {{ c.apelido }}</span>
                  <a :href="route('contactos.show', c.id)" style="all: unset; cursor: pointer; color: inherit; text-decoration: none;" @click.stop>Ver detalhes</a>
                </li>
              </ul>
            </div>
            <div v-else class="text-gray-400">Nenhum contacto cadastrado.</div>
          </div>
          <div>
            <h2 class="text-xl font-semibold mb-2">Atividades</h2>
            <div v-if="props.entidade.atividades && props.entidade.atividades.length">
              <ul class="divide-y divide-zinc-700">
                <li v-for="a in props.entidade.atividades" :key="a.id" class="py-2 flex justify-between items-center">
                  <span>{{ a.data }} - {{ a.tipo?.nome || '—' }} - {{ a.descricao?.slice(0, 40) }}</span>
                  <a :href="route('atividades.show', a.id)" style="all: unset; cursor: pointer; color: inherit; text-decoration: none;" @click.stop>Ver detalhes</a>
                </li>
              </ul>
            </div>
            <div v-else class="text-gray-400">Nenhuma atividade cadastrada.</div>
          </div>
        </div>
      </template>
      <template v-else>
        <div class="text-center text-gray-400 text-xl py-20">Entidade não encontrada ou dados não carregados.</div>
      </template>
    </div>
  </AppLayout>
</template> 