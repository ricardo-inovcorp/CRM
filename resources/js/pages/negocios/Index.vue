<script setup>
import { Link, router, usePage } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Pencil, Trash2, Eye, LayoutGrid, Table, Search, Filter, Building, ListFilter, AlertTriangle } from 'lucide-vue-next';
import Dialog from '@/components/ui/dialog/Dialog.vue';
import DialogContent from '@/components/ui/dialog/DialogContent.vue';
import DialogHeader from '@/components/ui/dialog/DialogHeader.vue';
import DialogTitle from '@/components/ui/dialog/DialogTitle.vue';
import DialogFooter from '@/components/ui/dialog/DialogFooter.vue';
import KanbanBoard from '@/components/KanbanBoard.vue';
import axios from 'axios';

const props = defineProps({
  negocios: Object,
  tipos: Array,
  entidades: Array,
  contactos: Array,
  estados: Array,
  filters: Object,
});

const page = usePage();
const user = page.props.auth.user;
const isAdmin = !!user.isAdmin;
const isManager = !!user.isManager;
const canEdit = isAdmin || isManager; // Apenas admin e gestor podem editar

// Dados de depuração para verificar o que está sendo recebido do servidor
console.log('Negócios recebidos do servidor:', props.negocios);

const showEditModal = ref(false);
const negocioEdit = ref(null);
const editForm = ref({
  nome: '',
  tipo_id: '',
  entidade_id: '',
  valor: '',
  estado: '',
  contactos: []
});
const feedback = ref('');
const saving = ref(false);
const viewMode = ref('table'); // 'table' or 'kanban'

// Filtros
const searchQuery = ref(props.filters?.search || '');
const tipoFilter = ref(props.filters?.tipo_id || '');
const entidadeFilter = ref(props.filters?.entidade_id || '');
const estadoFilter = ref(props.filters?.estado || '');

// Watch para aplicar filtros quando mudarem
watch(searchQuery, debounceFilter, { immediate: false });
watch(tipoFilter, aplicarFiltros, { immediate: false });
watch(entidadeFilter, aplicarFiltros, { immediate: false });
watch(estadoFilter, aplicarFiltros, { immediate: false });

// Função para debounce da busca
let timeoutId = null;
function debounceFilter() {
  clearTimeout(timeoutId);
  timeoutId = setTimeout(() => {
    aplicarFiltros();
  }, 300);
}

// Função para aplicar os filtros
function aplicarFiltros() {
  router.get(route('negocios.index'), {
    search: searchQuery.value,
    tipo_id: tipoFilter.value,
    entidade_id: entidadeFilter.value,
    estado: estadoFilter.value
  }, {
    preserveState: true,
    replace: true,
    only: ['negocios']
  });
}

// Função para limpar filtros
function limparFiltros() {
  searchQuery.value = '';
  tipoFilter.value = '';
  entidadeFilter.value = '';
  estadoFilter.value = '';
  aplicarFiltros();
}

// Estado local dos negócios
const localNegocios = ref([]);
const isUpdating = ref(false);

// Inicializar o estado local imediatamente
localNegocios.value = props.negocios?.data || [];
console.log('Estado local inicializado com', localNegocios.value.length, 'negócios');

// Atualizar estado local quando os props mudarem
watch(() => props.negocios, (newNegocios) => {
  if (newNegocios && newNegocios.data) {
    localNegocios.value = [...newNegocios.data];
    console.log('Watch: Negócios atualizados do servidor para estado local:', localNegocios.value.length);
  }
}, { deep: true, immediate: true });

function openEdit(negocio) {
  negocioEdit.value = negocio;
  editForm.value = {
    nome: negocio.nome || '',
    tipo_id: negocio.tipo_id || '',
    entidade_id: negocio.entidade_id || '',
    valor: negocio.valor || '',
    estado: negocio.estado || '',
    contactos: negocio.contactos?.map(c => c.id) || []
  };
  showEditModal.value = true;
}

function closeModal() {
  showEditModal.value = false;
  negocioEdit.value = null;
  feedback.value = '';
}

function submitEdit() {
  saving.value = true;
  router.put(route('negocios.update', negocioEdit.value.id), editForm.value, {
    preserveScroll: true,
    preserveState: false,
    onSuccess: () => {
      feedback.value = 'Negócio atualizado com sucesso!';
      saving.value = false;
      setTimeout(() => {
        closeModal();
      }, 1000);
    },
    onError: (errors) => {
      feedback.value = Object.values(errors).flat().join(', ') || 'Erro ao atualizar negócio.';
      saving.value = false;
    }
  });
}

const showDeleteModal = ref(false);
const negocioDelete = ref(null);

function openDelete(negocio) {
  negocioDelete.value = negocio;
  showDeleteModal.value = true;
}

function closeModals() {
  showEditModal.value = false;
  showDeleteModal.value = false;
  negocioEdit.value = null;
  negocioDelete.value = null;
  feedback.value = '';
}

function submitDelete() {
  router.delete(route('negocios.destroy', negocioDelete.value.id), {
    onSuccess: () => {
      feedback.value = 'Negócio eliminado com sucesso!';
      setTimeout(() => {
        closeModals();
        setTimeout(() => {
          router.reload({ only: ['negocios'] });
        }, 100);
      }, 1000);
    },
    onError: (errors) => {
      feedback.value = Object.values(errors).flat().join(', ') || 'Erro ao eliminar negócio.';
    }
  });
}

function destroy(id) {
  const negocio = localNegocios.value.find(n => n.id === id);
  openDelete(negocio);
}

function handleEstadoUpdate({ negocio, novoEstado }) {
  console.log('Index: Recebido evento de update:estado', { 
    negocioId: negocio.id, 
    negocioNome: negocio.nome,
    estadoAtual: negocio.estado,
    novoEstado 
  });
  
  // Atualizar o estado local imediatamente para feedback visual
  const negocioIndex = localNegocios.value.findIndex(n => n.id === negocio.id);
  if (negocioIndex !== -1) {
    // Armazenar o estado anterior para debug
    const estadoAnterior = localNegocios.value[negocioIndex].estado;
    
    // Atualizar o estado local
    localNegocios.value[negocioIndex].estado = novoEstado;
    
    // Feedback visual temporário
    feedback.value = `Salvando negócio "${negocio.nome}"...`;
    
    // Salvar imediatamente a alteração no servidor
    axios.put(route('negocios.update', negocio.id), { estado: novoEstado })
      .then(response => {
        console.log('Estado atualizado com sucesso:', response.data);
        feedback.value = `Negócio "${negocio.nome}" movido para ${novoEstado}`;
        
        // Limpar o feedback após 2 segundos
  setTimeout(() => {
    feedback.value = '';
  }, 2000);
      })
      .catch(error => {
        console.error('Erro ao atualizar estado:', error);
        // Reverter a alteração local em caso de erro
        localNegocios.value[negocioIndex].estado = estadoAnterior;
        feedback.value = 'Erro ao atualizar estado. Tente novamente.';
        
        // Limpar o feedback de erro após 3 segundos
        setTimeout(() => {
          feedback.value = '';
        }, 3000);
      });
  }
}

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

const filteredContactos = computed(() => {
  if (!editForm.value.entidade_id) return [];
  return props.contactos.filter(c => c.entidade_id === editForm.value.entidade_id);
});
</script>

<template>
  <AppLayout>
    <div class="p-6">
      <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold">Negócios</h1>
        <div class="flex items-center gap-4">
          <div class="flex items-center gap-2 bg-zinc-800 rounded-lg p-1">
            <button 
              @click="viewMode = 'table'" 
              class="p-2 rounded-lg transition"
              :class="viewMode === 'table' ? 'bg-zinc-700' : 'hover:bg-zinc-700'"
              title="Visualização em Tabela"
            >
              <Table class="w-5 h-5" />
            </button>
            <button 
              @click="viewMode = 'kanban'" 
              class="p-2 rounded-lg transition"
              :class="viewMode === 'kanban' ? 'bg-zinc-700' : 'hover:bg-zinc-700'"
              title="Visualização Kanban"
            >
              <LayoutGrid class="w-5 h-5" />
            </button>
          </div>
          <Link v-if="canEdit" :href="route('negocios.create')" class="px-4 py-2 rounded-lg bg-primary text-primary-foreground hover:bg-primary/90 font-medium transition">Novo Negócio</Link>
        </div>
      </div>

      <!-- Barra de filtros -->
      <div class="mb-6 bg-zinc-800 p-4 rounded-lg shadow-md">
        <div class="flex flex-col md:flex-row gap-4">
          <div class="flex-1">
            <label class="text-sm font-medium mb-1 block text-gray-300">Buscar por nome</label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <Search class="w-4 h-4 text-gray-400" />
              </div>
              <input 
                type="text" 
                v-model="searchQuery" 
                placeholder="Digite o nome do negócio..." 
                class="w-full pl-10 pr-4 py-2 rounded-md border border-zinc-700 bg-zinc-900 text-gray-200 focus:outline-none focus:ring-2 focus:ring-primary"
              />
            </div>
          </div>
          <div class="w-full md:w-56">
            <label class="text-sm font-medium mb-1 block text-gray-300">Filtrar por tipo</label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <ListFilter class="w-4 h-4 text-gray-400" />
              </div>
              <select 
                v-model="tipoFilter" 
                class="w-full pl-10 pr-4 py-2 rounded-md border border-zinc-700 bg-zinc-900 text-gray-200 focus:outline-none focus:ring-2 focus:ring-primary appearance-none"
              >
                <option value="">Todos os tipos</option>
                <option v-for="tipo in tipos" :key="tipo.id" :value="tipo.id">
                  {{ tipo.nome }}
                </option>
              </select>
            </div>
          </div>
          <div class="w-full md:w-64">
            <label class="text-sm font-medium mb-1 block text-gray-300">Filtrar por entidade</label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <Building class="w-4 h-4 text-gray-400" />
              </div>
              <select 
                v-model="entidadeFilter" 
                class="w-full pl-10 pr-4 py-2 rounded-md border border-zinc-700 bg-zinc-900 text-gray-200 focus:outline-none focus:ring-2 focus:ring-primary appearance-none"
              >
                <option value="">Todas as entidades</option>
                <option v-for="entidade in entidades" :key="entidade.id" :value="entidade.id">
                  {{ entidade.nome }}
                </option>
              </select>
            </div>
          </div>
          <div class="w-full md:w-56">
            <label class="text-sm font-medium mb-1 block text-gray-300">Filtrar por estado</label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <Filter class="w-4 h-4 text-gray-400" />
              </div>
              <select 
                v-model="estadoFilter" 
                class="w-full pl-10 pr-4 py-2 rounded-md border border-zinc-700 bg-zinc-900 text-gray-200 focus:outline-none focus:ring-2 focus:ring-primary appearance-none"
              >
                <option value="">Todos os estados</option>
                <option v-for="estado in estados" :key="estado" :value="estado">
                  {{ estado }}
                </option>
              </select>
            </div>
          </div>
          <div class="flex items-end">
            <button 
              @click="limparFiltros" 
              class="w-full md:w-auto px-4 py-2 rounded-md border border-zinc-700 bg-zinc-900 text-gray-200 hover:bg-zinc-700 transition"
            >
              Limpar Filtros
            </button>
          </div>
        </div>
      </div>

      <!-- Tabela -->
      <div v-if="viewMode === 'table'" class="bg-zinc-800 shadow-lg rounded-lg overflow-hidden">
        <p v-if="!localNegocios.length" class="text-center py-6 text-gray-400">
          Nenhum negócio encontrado.
        </p>
        <div v-else>
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-zinc-700">
              <thead>
                <tr class="bg-zinc-900">
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Nome</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Tipo</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Entidade</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Valor</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Estado</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Contactos</th>
                  <th class="px-6 py-3 text-center text-xs font-medium text-gray-400 uppercase tracking-wider">Ações</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-zinc-700">
                <tr v-for="negocio in localNegocios" :key="negocio.id" class="hover:bg-zinc-700 transition group cursor-pointer" @click="() => router.visit(route('negocios.show', negocio.id))">
                  <td class="px-6 py-4 whitespace-nowrap text-gray-200">{{ negocio.nome }}</td>
                  <td class="px-6 py-4 whitespace-nowrap text-gray-200">{{ negocio.tipo?.nome }}</td>
                  <td class="px-6 py-4 whitespace-nowrap text-gray-200">{{ negocio.entidade?.nome }}</td>
                  <td class="px-6 py-4 whitespace-nowrap text-gray-200">{{ negocio.valor ? negocio.valor.toLocaleString('pt-PT', { style: 'currency', currency: 'EUR' }) : '—' }}</td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full" :class="estadoBadgeClass(negocio.estado)">
                      {{ negocio.estado }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-gray-200">
                    <ul>
                      <li v-for="c in negocio.contactos" :key="c.id">{{ c.nome }} {{ c.apelido }}</li>
                    </ul>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-center">
                    <div class="flex items-center justify-center gap-2 opacity-0 group-hover:opacity-100 transition">
                      <button v-if="canEdit" @click.stop="openEdit(negocio)" class="p-1 rounded hover:bg-zinc-600" title="Editar">
                        <Pencil class="w-5 h-5 text-primary" />
                      </button>
                      <button v-if="isAdmin" @click.stop="openDelete(negocio)" class="p-1 rounded hover:bg-zinc-600" title="Eliminar">
                        <Trash2 class="w-5 h-5 text-red-500" />
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <div v-if="negocios.meta && negocios.meta.last_page > 1" class="mt-4 bg-zinc-800 px-6 py-3 flex justify-between items-center">
            <div class="text-sm text-gray-400">
              Mostrando {{ negocios.meta.current_page }} de {{ negocios.meta.last_page }} páginas
            </div>
            <div class="flex space-x-2">
              <a v-if="negocios.meta.current_page > 1"
                 :href="`?page=${negocios.meta.current_page - 1}&search=${searchQuery}&tipo_id=${tipoFilter}&entidade_id=${entidadeFilter}&estado=${estadoFilter}`"
                 class="px-3 py-1 bg-zinc-700 text-gray-200 rounded-md hover:bg-zinc-600 transition">
                Anterior
              </a>
              <a v-if="negocios.meta.current_page < negocios.meta.last_page"
                 :href="`?page=${negocios.meta.current_page + 1}&search=${searchQuery}&tipo_id=${tipoFilter}&entidade_id=${entidadeFilter}&estado=${estadoFilter}`"
                 class="px-3 py-1 bg-zinc-700 text-gray-200 rounded-md hover:bg-zinc-600 transition">
                Próxima
              </a>
            </div>
          </div>
        </div>
      </div>

      <!-- Kanban -->
      <div v-else class="bg-zinc-800 shadow-lg rounded-lg p-4">
        <div v-if="feedback" class="mb-4 p-2 rounded text-center" :class="feedback.includes('Erro') ? 'bg-red-900/50 text-red-300' : 'bg-green-900/50 text-green-300'">
          {{ feedback }}
        </div>
        <KanbanBoard 
          :negocios="localNegocios" 
          :estados="estados"
          @edit="openEdit"
          @delete="destroy"
          @update:estado="handleEstadoUpdate"
        />
      </div>
    </div>

    <!-- Modal de Edição -->
    <Dialog v-model:open="showEditModal">
      <DialogContent class="max-w-md w-full">
        <DialogHeader>
          <DialogTitle>Editar Negócio</DialogTitle>
        </DialogHeader>
        <form @submit.prevent="submitEdit" class="space-y-4 mt-4">
          <div>
            <label class="block mb-1 text-sm font-medium text-foreground">Nome</label>
            <input v-model="editForm.nome" type="text" class="w-full border border-input bg-background rounded-lg px-3 py-2 text-foreground focus:outline-none focus:ring-2 focus:ring-primary dark:bg-zinc-800 dark:text-white" required />
          </div>
          <div>
            <label class="block mb-1 text-sm font-medium text-foreground">Tipo</label>
            <select v-model="editForm.tipo_id" class="w-full border border-input bg-background rounded-lg px-3 py-2 text-foreground focus:outline-none focus:ring-2 focus:ring-primary dark:bg-zinc-800 dark:text-white" required>
              <option value="">Selecione...</option>
              <option v-for="tipo in tipos" :key="tipo.id" :value="tipo.id">{{ tipo.nome }}</option>
            </select>
          </div>
          <div>
            <label class="block mb-1 text-sm font-medium text-foreground">Entidade</label>
            <select v-model="editForm.entidade_id" class="w-full border border-input bg-background rounded-lg px-3 py-2 text-foreground focus:outline-none focus:ring-2 focus:ring-primary dark:bg-zinc-800 dark:text-white" required>
              <option value="">Selecione...</option>
              <option v-for="entidade in entidades" :key="entidade.id" :value="entidade.id">{{ entidade.nome }}</option>
            </select>
          </div>
          <div>
            <label class="block mb-1 text-sm font-medium text-foreground">Valor</label>
            <input v-model="editForm.valor" type="number" step="0.01" class="w-full border border-input bg-background rounded-lg px-3 py-2 text-foreground focus:outline-none focus:ring-2 focus:ring-primary dark:bg-zinc-800 dark:text-white" />
          </div>
          <div>
            <label class="block mb-1 text-sm font-medium text-foreground">Estado</label>
            <select v-model="editForm.estado" class="w-full border border-input bg-background rounded-lg px-3 py-2 text-foreground focus:outline-none focus:ring-2 focus:ring-primary dark:bg-zinc-800 dark:text-white" required>
              <option value="">Selecione...</option>
              <option v-for="estado in estados" :key="estado" :value="estado">{{ estado }}</option>
            </select>
          </div>
          <div>
            <label class="block mb-1 text-sm font-medium text-foreground">Contactos</label>
            <select v-model="editForm.contactos" multiple class="w-full border border-input bg-background rounded-lg px-3 py-2 h-32 text-foreground focus:outline-none focus:ring-2 focus:ring-primary dark:bg-zinc-800 dark:text-white">
              <option v-for="contacto in filteredContactos" :key="contacto.id" :value="contacto.id">{{ contacto.nome }} {{ contacto.apelido }}</option>
            </select>
          </div>
          <DialogFooter class="flex justify-end gap-2 mt-4">
            <button type="button" @click="closeModal" class="px-4 py-2 rounded bg-zinc-700 text-white hover:bg-zinc-600">Cancelar</button>
            <button type="submit" class="px-4 py-2 rounded bg-primary text-primary-foreground hover:bg-primary/90 font-semibold" :disabled="saving">
              <span v-if="saving">Salvando...</span>
              <span v-else>Salvar</span>
            </button>
          </DialogFooter>
        </form>
      </DialogContent>
    </Dialog>

    <!-- Modal de Confirmação de Exclusão -->
    <Dialog v-model:open="showDeleteModal">
      <DialogContent class="max-w-md w-full">
        <DialogHeader>
          <DialogTitle class="flex items-center gap-2 text-red-500">
            <AlertTriangle class="w-5 h-5" />
            Eliminar Negócio
          </DialogTitle>
        </DialogHeader>
        <div class="py-4">
          <p class="text-gray-200 mb-4">
            Tem certeza que deseja eliminar <span class="font-semibold">{{ negocioDelete?.nome }}</span>?
          </p>
          <p class="text-red-400 text-sm">
            Esta ação é <span class="font-bold">irreversível</span> e irá eliminar também todas as atividades relacionadas a este negócio.
          </p>
        </div>
        <DialogFooter class="flex justify-end gap-2 mt-4">
          <button type="button" @click="closeModals" class="px-4 py-2 rounded bg-zinc-700 text-white hover:bg-zinc-600">Cancelar</button>
          <button type="button" @click="submitDelete" class="px-4 py-2 rounded bg-red-600 text-white hover:bg-red-700 font-semibold">Eliminar</button>
        </DialogFooter>
        <div v-if="feedback" class="text-center mt-2" :class="feedback.includes('Erro') ? 'text-red-500' : 'text-green-500'">{{ feedback }}</div>
      </DialogContent>
    </Dialog>
  </AppLayout>
</template> 