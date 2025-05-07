<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import Dialog from '@/components/ui/dialog/Dialog.vue';
import DialogContent from '@/components/ui/dialog/DialogContent.vue';
import DialogHeader from '@/components/ui/dialog/DialogHeader.vue';
import DialogTitle from '@/components/ui/dialog/DialogTitle.vue';
import DialogFooter from '@/components/ui/dialog/DialogFooter.vue';
import { ref, nextTick, computed } from 'vue';
import { Pencil, Trash2, AlertTriangle } from 'lucide-vue-next';

// Simplificando o tipo para evitar erros
interface Props {
    entidades: any;
    filters: any;
}

defineProps<Props>();

const entidadeEdit = ref(null);
const showEditModal = ref(false);
const showDeleteModal = ref(false);
const showFinalDeleteModal = ref(false);
const entidadeDelete = ref(null);
const confirmDeleteText = ref('');
const editForm = ref({ nome: '', email: '', telefone: '', estado: 'Ativo', morada: '', codigo_postal: '', localidade: '', pais_id: null, website: '', observacoes: '' });
const feedback = ref('');
const saving = ref(false);

const canDelete = computed(() => {
    return confirmDeleteText.value === 'DELETAR';
});

function openEdit(entidade) {
  entidadeEdit.value = entidade;
  editForm.value = { 
    nome: entidade.nome || '',
    email: entidade.email || '',
    telefone: entidade.telefone || '',
    estado: entidade.estado || 'Ativo',
    morada: entidade.morada || '',
    codigo_postal: entidade.codigo_postal || '',
    localidade: entidade.localidade || '',
    pais_id: entidade.pais_id || null,
    website: entidade.website || '',
    observacoes: entidade.observacoes || ''
  };
  showEditModal.value = true;
}

function openDelete(entidade) {
  entidadeDelete.value = entidade;
  showDeleteModal.value = true;
}

function openFinalDelete() {
  showDeleteModal.value = false;
  showFinalDeleteModal.value = true;
  confirmDeleteText.value = '';
}

function closeModals() {
  showEditModal.value = false;
  showDeleteModal.value = false;
  showFinalDeleteModal.value = false;
  entidadeEdit.value = null;
  entidadeDelete.value = null;
  feedback.value = '';
  confirmDeleteText.value = '';
}

function submitEdit() {
  saving.value = true;
  router.put(route('entidades.update', entidadeEdit.value.id), editForm.value, {
    preserveScroll: true,
    onSuccess: () => {
      feedback.value = 'Entidade atualizada com sucesso!';
      saving.value = false;
      setTimeout(() => {
        closeModals();
        router.reload({ only: ['entidades'] });
      }, 1000);
    },
    onError: (errors) => {
      feedback.value = Object.values(errors).flat().join(', ') || 'Erro ao atualizar entidade.';
      saving.value = false;
    }
  });
}

function submitDelete() {
  router.delete(route('entidades.destroy', entidadeDelete.value.id), {
    onSuccess: () => {
      feedback.value = 'Entidade eliminada com sucesso!';
      setTimeout(() => {
        closeModals();
        router.reload({ only: ['entidades'] });
      }, 1000);
    },
    onError: (errors) => {
      feedback.value = Object.values(errors).flat().join(', ') || 'Erro ao eliminar entidade.';
    }
  });
}
</script>

<template>
    <Head title="Entidades" />

    <AppLayout>
        <div class="p-6">
            <div class="mb-6 flex items-center justify-between">
                <h1 class="text-2xl font-bold">Entidades</h1>
                <a :href="route('entidades.create')" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition">
                    Nova Entidade
                </a>
            </div>

            <div class="bg-zinc-800 shadow-lg rounded-lg overflow-hidden">
                <p v-if="!entidades || !entidades.data || entidades.data.length === 0" class="text-center py-6 text-gray-400">
                    Nenhuma entidade encontrada.
                </p>
                <div v-else>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-zinc-700">
                            <thead>
                                <tr class="bg-zinc-900">
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Nome</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Telefone</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Estado</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-400 uppercase tracking-wider">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-700">
                                <tr v-for="entidade in entidades.data" :key="entidade.id" class="hover:bg-zinc-700 transition group cursor-pointer" @click="() => router.visit(route('entidades.show', entidade.id))">
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-200">{{ entidade.nome }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-200">{{ entidade.email || '—' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-200">{{ entidade.telefone || '—' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full"
                                            :class="entidade.estado === 'Ativo' ? 'bg-green-900 text-green-300' : 'bg-red-900 text-red-300'">
                                            {{ entidade.estado }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="flex items-center justify-center gap-2 opacity-0 group-hover:opacity-100 transition">
                                            <button @click.stop="openEdit(entidade)" class="p-1 rounded hover:bg-zinc-600" title="Editar">
                                                <Pencil class="w-5 h-5 text-primary" />
                                            </button>
                                            <button @click.stop="openDelete(entidade)" class="p-1 rounded hover:bg-zinc-600" title="Eliminar">
                                                <Trash2 class="w-5 h-5 text-red-500" />
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div v-if="entidades.meta && entidades.meta.last_page > 1" class="mt-4 bg-zinc-800 px-6 py-3 flex justify-between items-center">
                        <div class="text-sm text-gray-400">
                            Mostrando {{ entidades.meta.current_page }} de {{ entidades.meta.last_page }} páginas
                        </div>
                        <div class="flex space-x-2">
                            <a v-if="entidades.meta.current_page > 1" 
                                :href="`?page=${entidades.meta.current_page - 1}`"
                                class="px-3 py-1 bg-zinc-700 text-gray-200 rounded-md hover:bg-zinc-600 transition">
                                Anterior
                            </a>
                            <a v-if="entidades.meta.current_page < entidades.meta.last_page" 
                                :href="`?page=${entidades.meta.current_page + 1}`"
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
                        <DialogTitle>Editar Entidade</DialogTitle>
                    </DialogHeader>
                    <form @submit.prevent="submitEdit" class="space-y-4 mt-4">
                        <div>
                            <label class="block mb-1 text-sm font-medium text-foreground">Nome</label>
                            <input v-model="editForm.nome" type="text" class="w-full border border-input bg-background rounded-lg px-3 py-2 text-foreground focus:outline-none focus:ring-2 focus:ring-primary dark:bg-zinc-800 dark:text-white" required />
                        </div>
                        <div>
                            <label class="block mb-1 text-sm font-medium text-foreground">Email</label>
                            <input v-model="editForm.email" type="email" class="w-full border border-input bg-background rounded-lg px-3 py-2 text-foreground focus:outline-none focus:ring-2 focus:ring-primary dark:bg-zinc-800 dark:text-white" />
                        </div>
                        <div>
                            <label class="block mb-1 text-sm font-medium text-foreground">Telefone</label>
                            <input v-model="editForm.telefone" type="text" class="w-full border border-input bg-background rounded-lg px-3 py-2 text-foreground focus:outline-none focus:ring-2 focus:ring-primary dark:bg-zinc-800 dark:text-white" />
                        </div>
                        <div>
                            <label class="block mb-1 text-sm font-medium text-foreground">Estado</label>
                            <select v-model="editForm.estado" class="w-full border border-input bg-background rounded-lg px-3 py-2 text-foreground focus:outline-none focus:ring-2 focus:ring-primary dark:bg-zinc-800 dark:text-white">
                                <option value="Ativo">Ativo</option>
                                <option value="Inativo">Inativo</option>
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

            <!-- Modal de Confirmação de Exclusão (Primeiro) -->
            <Dialog v-model:open="showDeleteModal">
                <DialogContent class="max-w-md w-full">
                    <DialogHeader>
                        <DialogTitle>Eliminar Entidade</DialogTitle>
                    </DialogHeader>
                    <div class="py-4">
                        <p class="text-gray-200 mb-4">
                            Tem certeza que deseja eliminar <span class="font-semibold">{{ entidadeDelete?.nome }}</span>?
                        </p>
                        <p class="text-red-400 text-sm">
                            Esta ação irá eliminar também todos os contactos e atividades relacionados a esta entidade.
                        </p>
                    </div>
                    <DialogFooter class="flex justify-end gap-2 mt-4">
                        <button type="button" @click="closeModals" class="px-4 py-2 rounded bg-zinc-700 text-white hover:bg-zinc-600">Cancelar</button>
                        <button type="button" @click="openFinalDelete" class="px-4 py-2 rounded bg-red-600 text-white hover:bg-red-700 font-semibold">Continuar</button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>

            <!-- Modal de Confirmação Final -->
            <Dialog v-model:open="showFinalDeleteModal">
                <DialogContent class="max-w-md w-full">
                    <DialogHeader>
                        <DialogTitle class="flex items-center gap-2 text-red-500">
                            <AlertTriangle class="w-5 h-5" />
                            Confirmação Final
                        </DialogTitle>
                    </DialogHeader>
                    <div class="py-4">
                        <p class="text-gray-200 mb-4">
                            Esta ação é <span class="font-bold text-red-500">irreversível</span>. Tem certeza absoluta que deseja eliminar esta entidade e todos os seus dados relacionados?
                        </p>
                        <div class="mt-4">
                            <label class="block mb-2 text-sm font-medium text-gray-200">
                                Digite "DELETAR" para confirmar:
                            </label>
                            <input 
                                v-model="confirmDeleteText"
                                type="text" 
                                class="w-full border border-input bg-background rounded-lg px-3 py-2 text-foreground focus:outline-none focus:ring-2 focus:ring-red-500 dark:bg-zinc-800 dark:text-white"
                                placeholder="Digite DELETAR"
                            />
                        </div>
                    </div>
                    <DialogFooter class="flex justify-end gap-2 mt-4">
                        <button type="button" @click="closeModals" class="px-4 py-2 rounded bg-zinc-700 text-white hover:bg-zinc-600">Cancelar</button>
                        <button 
                            type="button" 
                            @click="submitDelete" 
                            class="px-4 py-2 rounded bg-red-600 text-white hover:bg-red-700 font-semibold"
                            :disabled="!canDelete"
                            :class="{ 'opacity-50 cursor-not-allowed': !canDelete }"
                        >
                            Eliminar
                        </button>
                    </DialogFooter>
                    <div v-if="feedback" class="text-center mt-2" :class="feedback.includes('Erro') ? 'text-red-500' : 'text-green-500'">{{ feedback }}</div>
                </DialogContent>
            </Dialog>
        </div>
    </AppLayout>
</template> 