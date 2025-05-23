<script setup lang="ts">
import { Head, router, usePage } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import Dialog from '@/components/ui/dialog/Dialog.vue';
import DialogContent from '@/components/ui/dialog/DialogContent.vue';
import DialogHeader from '@/components/ui/dialog/DialogHeader.vue';
import DialogTitle from '@/components/ui/dialog/DialogTitle.vue';
import DialogFooter from '@/components/ui/dialog/DialogFooter.vue';
import { ref, computed, watch } from 'vue';
import { Pencil, Trash2, AlertTriangle, Search, Filter, Building } from 'lucide-vue-next';

// Simplificando o tipo para evitar erros
interface Props {
    contactos: any;
    entidades: any;
    filters: any;
    estados_disponiveis: string[];
}

const props = defineProps<Props>();

const contactoEdit = ref(null);
const showEditModal = ref(false);
const showDeleteModal = ref(false);
const contactoDelete = ref(null);
const confirmDeleteText = ref('');
const editForm = ref({ 
    nome: '', 
    apelido: '', 
    entidade_id: null, 
    funcao_id: null, 
    telefone: '', 
    telemovel: '', 
    email: '', 
    observacoes: '', 
    estado: 'Ativo' 
});
const feedback = ref('');
const saving = ref(false);

// Filtros
const searchQuery = ref(props.filters.search || '');
const entidadeFilter = ref(props.filters.entidade_id || '');
const estadoFilter = ref(props.filters.estado || '');

// Watch para aplicar filtros quando mudarem
watch(searchQuery, debounceFilter, { immediate: false });
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
  router.get(route('contactos.index'), {
    search: searchQuery.value,
    entidade_id: entidadeFilter.value,
    estado: estadoFilter.value
  }, {
    preserveState: true,
    replace: true,
    only: ['contactos']
  });
}

// Função para limpar filtros
function limparFiltros() {
  searchQuery.value = '';
  entidadeFilter.value = '';
  estadoFilter.value = '';
  aplicarFiltros();
}

const page = usePage();
const user = page.props.auth.user;
const isAdmin = !!user.isAdmin;
const isManager = !!user.isManager;
const canEdit = isAdmin || isManager; // Apenas admin e gestor podem editar

const canDelete = computed(() => {
    return confirmDeleteText.value === 'DELETAR';
});

function openEdit(contacto) {
    contactoEdit.value = contacto;
    editForm.value = { 
        nome: contacto.nome || '',
        apelido: contacto.apelido || '',
        entidade_id: contacto.entidade_id || null,
        funcao_id: contacto.funcao_id || null,
        telefone: contacto.telefone || '',
        telemovel: contacto.telemovel || '',
        email: contacto.email || '',
        observacoes: contacto.observacoes || '',
        estado: contacto.estado || 'Ativo'
    };
    showEditModal.value = true;
}

function openDelete(contacto) {
    contactoDelete.value = contacto;
    showDeleteModal.value = true;
}

function closeModals() {
    showEditModal.value = false;
    showDeleteModal.value = false;
    contactoEdit.value = null;
    contactoDelete.value = null;
    feedback.value = '';
}

function submitEdit() {
    saving.value = true;
    router.put(route('contactos.update', contactoEdit.value.id), editForm.value, {
        preserveScroll: true,
        onSuccess: () => {
            feedback.value = 'Contacto atualizado com sucesso!';
            saving.value = false;
            setTimeout(() => {
                closeModals();
                setTimeout(() => {
                    router.reload({ only: ['contactos'] });
                }, 100);
            }, 1000);
        },
        onError: (errors) => {
            feedback.value = Object.values(errors).flat().join(', ') || 'Erro ao atualizar contacto.';
            saving.value = false;
        }
    });
}

function submitDelete() {
    router.delete(route('contactos.destroy', contactoDelete.value.id), {
        onSuccess: () => {
            feedback.value = 'Contacto eliminado com sucesso!';
            setTimeout(() => {
                closeModals();
                setTimeout(() => {
                    router.reload({ only: ['contactos'] });
                }, 100);
            }, 1000);
        },
        onError: (errors) => {
            feedback.value = Object.values(errors).flat().join(', ') || 'Erro ao eliminar contacto.';
        }
    });
}
</script>

<template>
    <Head title="Contactos" />

    <AppLayout>
        <div class="p-6">
            <div class="mb-6 flex items-center justify-between">
                <h1 class="text-2xl font-bold">Contactos</h1>
                <a v-if="canEdit" :href="route('contactos.create')" class="px-4 py-2 rounded-lg bg-primary text-primary-foreground hover:bg-primary/90 font-medium transition">
                    Novo Contacto
                </a>
            </div>

            <!-- Barra de filtros -->
            <div class="mb-6 bg-zinc-800 p-4 rounded-lg shadow-md">
                <div class="flex flex-col md:flex-row gap-4">
                    <div class="flex-1">
                        <label class="text-sm font-medium mb-1 block text-gray-300">Buscar por nome ou entidade</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <Search class="w-4 h-4 text-gray-400" />
                            </div>
                            <input 
                                type="text" 
                                v-model="searchQuery" 
                                placeholder="Digite o nome do contacto ou entidade..." 
                                class="w-full pl-10 pr-4 py-2 rounded-md border border-zinc-700 bg-zinc-900 text-gray-200 focus:outline-none focus:ring-2 focus:ring-primary"
                            />
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
                                <option v-for="estado in props.estados_disponiveis" :key="estado" :value="estado">
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

            <div class="bg-zinc-800 shadow-lg rounded-lg overflow-hidden">
                <p v-if="!contactos || !contactos.data || contactos.data.length === 0" class="text-center py-6 text-gray-400">
                    Nenhum contacto encontrado.
                </p>
                <div v-else>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-zinc-700">
                            <thead>
                                <tr class="bg-zinc-900">
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Nome</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Entidade</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Telefone</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Estado</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-400 uppercase tracking-wider">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-700">
                                <tr v-for="contacto in contactos.data" :key="contacto.id" class="hover:bg-zinc-700 transition group cursor-pointer" @click="() => router.visit(route('contactos.show', contacto.id))">
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-200">{{ contacto.nome }} {{ contacto.apelido }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-200">{{ contacto.entidade?.nome || '—' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-200">{{ contacto.telefone || '—' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-200">{{ contacto.email || '—' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full"
                                            :class="contacto.estado === 'Ativo' ? 'bg-green-900 text-green-300' : 'bg-red-900 text-red-300'">
                                            {{ contacto.estado }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="flex items-center justify-center gap-2 opacity-0 group-hover:opacity-100 transition">
                                            <button v-if="canEdit" @click.stop="openEdit(contacto)" class="p-1 rounded hover:bg-zinc-600" title="Editar">
                                                <Pencil class="w-5 h-5 text-primary" />
                                            </button>
                                            <button v-if="isAdmin" @click.stop="openDelete(contacto)" class="p-1 rounded hover:bg-zinc-600" title="Eliminar">
                                                <Trash2 class="w-5 h-5 text-red-500" />
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div v-if="contactos.meta && contactos.meta.last_page > 1" class="mt-4 bg-zinc-800 px-6 py-3 flex justify-between items-center">
                        <div class="text-sm text-gray-400">
                            Mostrando {{ contactos.meta.current_page }} de {{ contactos.meta.last_page }} páginas
                        </div>
                        <div class="flex space-x-2">
                            <a v-if="contactos.meta.current_page > 1" 
                               :href="`?page=${contactos.meta.current_page - 1}&search=${searchQuery}&entidade_id=${entidadeFilter}&estado=${estadoFilter}`"
                               class="px-3 py-1 bg-zinc-700 text-gray-200 rounded-md hover:bg-zinc-600 transition">
                                Anterior
                            </a>
                            <a v-if="contactos.meta.current_page < contactos.meta.last_page" 
                               :href="`?page=${contactos.meta.current_page + 1}&search=${searchQuery}&entidade_id=${entidadeFilter}&estado=${estadoFilter}`"
                               class="px-3 py-1 bg-zinc-700 text-gray-200 rounded-md hover:bg-zinc-600 transition">
                                Próxima
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal de Edição -->
            <Dialog v-model:open="showEditModal">
                <DialogContent class="max-w-md w-full">
                    <DialogHeader>
                        <DialogTitle>Editar Contacto</DialogTitle>
                    </DialogHeader>
                    <form @submit.prevent="submitEdit" class="space-y-4 mt-4">
                        <div>
                            <label class="block mb-1 text-sm font-medium text-foreground">Nome</label>
                            <input v-model="editForm.nome" type="text" class="w-full border border-input bg-background rounded-lg px-3 py-2 text-foreground focus:outline-none focus:ring-2 focus:ring-primary dark:bg-zinc-800 dark:text-white" required />
                        </div>
                        <div>
                            <label class="block mb-1 text-sm font-medium text-foreground">Apelido</label>
                            <input v-model="editForm.apelido" type="text" class="w-full border border-input bg-background rounded-lg px-3 py-2 text-foreground focus:outline-none focus:ring-2 focus:ring-primary dark:bg-zinc-800 dark:text-white" required />
                        </div>
                        <div>
                            <label class="block mb-1 text-sm font-medium text-foreground">Entidade</label>
                            <select v-model="editForm.entidade_id" class="w-full border border-input bg-background rounded-lg px-3 py-2 text-foreground focus:outline-none focus:ring-2 focus:ring-primary dark:bg-zinc-800 dark:text-white">
                                <option value="">Selecione uma entidade</option>
                                <option v-for="entidade in entidades" :key="entidade.id" :value="entidade.id">
                                    {{ entidade.nome }}
                                </option>
                            </select>
                        </div>
                        <div>
                            <label class="block mb-1 text-sm font-medium text-foreground">Telefone</label>
                            <input v-model="editForm.telefone" type="text" class="w-full border border-input bg-background rounded-lg px-3 py-2 text-foreground focus:outline-none focus:ring-2 focus:ring-primary dark:bg-zinc-800 dark:text-white" />
                        </div>
                        <div>
                            <label class="block mb-1 text-sm font-medium text-foreground">Telemóvel</label>
                            <input v-model="editForm.telemovel" type="text" class="w-full border border-input bg-background rounded-lg px-3 py-2 text-foreground focus:outline-none focus:ring-2 focus:ring-primary dark:bg-zinc-800 dark:text-white" />
                        </div>
                        <div>
                            <label class="block mb-1 text-sm font-medium text-foreground">Email</label>
                            <input v-model="editForm.email" type="email" class="w-full border border-input bg-background rounded-lg px-3 py-2 text-foreground focus:outline-none focus:ring-2 focus:ring-primary dark:bg-zinc-800 dark:text-white" />
                        </div>
                        <div>
                            <label class="block mb-1 text-sm font-medium text-foreground">Estado</label>
                            <select v-model="editForm.estado" class="w-full border border-input bg-background rounded-lg px-3 py-2 text-foreground focus:outline-none focus:ring-2 focus:ring-primary dark:bg-zinc-800 dark:text-white">
                                <option v-for="estado in props.estados_disponiveis" :key="estado" :value="estado">
                                    {{ estado }}
                                </option>
                            </select>
                        </div>
                        <DialogFooter class="flex justify-end gap-2 mt-4">
                            <button type="button" @click="closeModals" class="px-4 py-2 rounded bg-zinc-700 text-white hover:bg-zinc-600">Cancelar</button>
                            <button type="submit" class="px-4 py-2 rounded bg-zinc-900 text-grey-900 hover:bg-zinc-800 font-semibold" :disabled="saving">
                                <span v-if="saving">Salvando...</span>
                                <span v-else>Salvar</span>
                            </button>
                        </DialogFooter>
                        <div v-if="feedback" class="text-center mt-2" :class="feedback.includes('Erro') ? 'text-red-500' : 'text-green-500'">{{ feedback }}</div>
                    </form>
                </DialogContent>
            </Dialog>

            <!-- Modal de Confirmação de Exclusão -->
            <Dialog v-model:open="showDeleteModal">
                <DialogContent class="max-w-md w-full">
                    <DialogHeader>
                        <DialogTitle class="flex items-center gap-2 text-red-500">
                            <AlertTriangle class="w-5 h-5" />
                            Eliminar Contacto
                        </DialogTitle>
                    </DialogHeader>
                    <div class="py-4">
                        <p class="text-gray-200 mb-4">
                            Tem certeza que deseja eliminar <span class="font-semibold">{{ contactoDelete?.nome }} {{ contactoDelete?.apelido }}</span>?
                        </p>
                        <p class="text-red-400 text-sm">
                            Esta ação é <span class="font-bold">irreversível</span> e irá eliminar também todas as atividades relacionadas a este contacto.
                        </p>
                    </div>
                    <DialogFooter class="flex justify-end gap-2 mt-4">
                        <button type="button" @click="closeModals" class="px-4 py-2 rounded bg-zinc-700 text-white hover:bg-zinc-600">Cancelar</button>
                        <button type="button" @click="submitDelete" class="px-4 py-2 rounded bg-red-600 text-white hover:bg-red-700 font-semibold">Eliminar</button>
                    </DialogFooter>
                    <div v-if="feedback" class="text-center mt-2" :class="feedback.includes('Erro') ? 'text-red-500' : 'text-green-500'">{{ feedback }}</div>
                </DialogContent>
            </Dialog>
        </div>
    </AppLayout>
</template> 