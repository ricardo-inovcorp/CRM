<script setup>
import { ref, computed, watch } from 'vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Pencil, Trash2 } from 'lucide-vue-next';
import draggable from 'vuedraggable';
import { router } from '@inertiajs/vue3';

const props = defineProps({
  negocios: {
    type: Array,
    required: true
  },
  estados: {
    type: Array,
    required: true
  }
});

const emit = defineEmits(['edit', 'delete', 'update:estado']);

// Estado local para cada coluna
const columnStates = ref({});

// Inicializar o estado local quando os props mudarem
watch(() => props.negocios, (newNegocios) => {
  console.log('KanbanBoard: Atualizando colunas com', newNegocios?.length || 0, 'negócios');
  const newStates = {};
  if (!newNegocios || newNegocios.length === 0) {
    console.warn('KanbanBoard: Array de negócios vazio ou undefined');
    props.estados.forEach(estado => {
      newStates[estado] = [];
    });
  } else {
    props.estados.forEach(estado => {
      newStates[estado] = newNegocios.filter(negocio => negocio.estado === estado);
      console.log(`KanbanBoard: Coluna ${estado} tem ${newStates[estado].length} negócios`);
    });
  }
  columnStates.value = newStates;
}, { immediate: true, deep: true });

function estadoBadgeClass(estado) {
  switch (estado) {
    case 'ganho':
      return 'bg-green-900 text-green-300';
    case 'perdido':
      return 'bg-red-900 text-red-300';
    case 'proposta':
      return 'bg-yellow-900 text-yellow-300';
    case 'negociacao':
      return 'bg-blue-900 text-blue-300';
    case 'contactado':
      return 'bg-cyan-900 text-cyan-300';
    default:
      return 'bg-zinc-700 text-zinc-200';
  }
}

function formatCurrency(value) {
  return value ? value.toLocaleString('pt-PT', { style: 'currency', currency: 'EUR' }) : '—';
}

function onDragEnd(evt) {
  // evt.to é o container da coluna de destino
  // evt.item é o DOM do item arrastado
  if (!evt.to || !evt.item) {
    console.warn('KanbanBoard: evt.to ou evt.item indefinido', evt);
    return;
  }
  const novoEstado = evt.to.dataset.estado;
  // O negócio pode ser encontrado pelo id no dataset do item
  const negocioId = evt.item.__draggable_context?.element?.id;
  const negocio = evt.item.__draggable_context?.element;
  if (!novoEstado || !negocioId || !negocio) {
    console.warn('KanbanBoard: Dados insuficientes para emitir update:estado', {novoEstado, negocioId, negocio, evt});
    return;
  }
  console.log('KanbanBoard: Emitindo update:estado', { negocioId, negocioNome: negocio.nome, novoEstado });
  
  // Definir localStorage flag para indicar que um negócio foi atualizado
  localStorage.setItem('negocio_atualizado', 'true');
  
  // Emitir evento para o componente pai
  emit('update:estado', { negocio: { ...negocio, estado: novoEstado }, novoEstado });
}
</script>

<template>
  <div class="flex gap-4 overflow-x-auto pb-4">
    <div v-for="estado in estados" :key="estado" class="flex-none w-80">
      <Card class="h-full">
        <CardHeader class="py-3">
          <CardTitle class="text-sm font-medium flex items-center justify-between">
            <span>{{ estado }}</span>
            <span class="text-xs text-gray-400">{{ columnStates[estado]?.length || 0 }}</span>
          </CardTitle>
        </CardHeader>
        <CardContent class="p-2">
          <draggable
            v-model="columnStates[estado]"
            :group="{ name: 'negocios', pull: true, put: true }"
            item-key="id"
            :data-estado="estado"
            class="space-y-2 min-h-[100px]"
            ghost-class="opacity-50"
            drag-class="cursor-grabbing"
            @end="onDragEnd"
          >
            <template #item="{ element: negocio }">
              <div 
                class="bg-zinc-800 rounded-lg p-3 hover:bg-zinc-700 transition cursor-grab active:cursor-grabbing group"
                @click="() => router.visit(route('negocios.show', negocio.id))"
              >
                <div class="flex justify-between items-start mb-2">
                  <h3 class="font-medium text-sm">{{ negocio.nome }}</h3>
                  <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition">
                    <button @click.stop="emit('edit', negocio)" class="p-1 rounded hover:bg-zinc-600" title="Editar">
                      <Pencil class="w-4 h-4 text-primary" />
                    </button>
                    <button @click.stop="emit('delete', negocio)" class="p-1 rounded hover:bg-zinc-600" title="Eliminar">
                      <Trash2 class="w-4 h-4 text-red-500" />
                    </button>
                  </div>
                </div>
                <div class="text-xs text-gray-400 space-y-1">
                  <div>{{ negocio.tipo?.nome }}</div>
                  <div>{{ negocio.entidade?.nome }}</div>
                  <div class="font-medium text-gray-300">{{ formatCurrency(negocio.valor) }}</div>
                </div>
              </div>
            </template>
          </draggable>
        </CardContent>
      </Card>
    </div>
  </div>
</template> 