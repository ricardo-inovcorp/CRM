<script setup lang="ts">
import { Head, router, usePage } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import Dialog from '@/components/ui/dialog/Dialog.vue';
import DialogContent from '@/components/ui/dialog/DialogContent.vue';
import DialogHeader from '@/components/ui/dialog/DialogHeader.vue';
import DialogTitle from '@/components/ui/dialog/DialogTitle.vue';
import DialogFooter from '@/components/ui/dialog/DialogFooter.vue';
import { ref } from 'vue';
import { Pencil, Trash2 } from 'lucide-vue-next';

// Simplificando o tipo para evitar erros
interface Props {
    atividades: any;
    entidades: any;
    tipos: any;
    filters: any;
}

defineProps<Props>();

const showEditModal = ref(false);
const atividadeEdit = ref(null);
const saving = ref(false);
const feedback = ref('');
const editForm = ref({
    data: '',
    hora: '',
    duracao: '',
    entidade_id: '',
    contacto_id: '',
    tipo_id: '',
    descricao: ''
});

const showDeleteModal = ref(false);
const atividadeDelete = ref(null);

const page = usePage();
const user = page.props.auth.user;
const isAdmin = !!user.isAdmin;
const isManager = !!user.isManager;
const canEdit = isAdmin || isManager; // Apenas admin e gestor podem editar

function openEdit(atividade) {
    atividadeEdit.value = atividade;
    editForm.value = {
        data: atividade.data || '',
        hora: atividade.hora || '',
        duracao: atividade.duracao || '',
        entidade_id: atividade.entidade_id || '',
        contacto_id: atividade.contacto_id || '',
        tipo_id: atividade.tipo_id || '',
        descricao: atividade.descricao || ''
    };
    showEditModal.value = true;
}

function openDelete(atividade) {
    atividadeDelete.value = atividade;
    showDeleteModal.value = true;
}

function closeModals() {
    showEditModal.value = false;
    showDeleteModal.value = false;
    atividadeEdit.value = null;
    atividadeDelete.value = null;
    feedback.value = '';
}

function submitEdit() {
    saving.value = true;
    router.put(route('atividades.update', atividadeEdit.value.id), editForm.value, {
        preserveScroll: true,
        onSuccess: () => {
            feedback.value = 'Atividade atualizada com sucesso!';
            saving.value = false;
            setTimeout(() => {
                closeModals();
                setTimeout(() => {
                    router.reload({ only: ['atividades'] });
                }, 100);
            }, 1000);
        },
        onError: (errors) => {
            feedback.value = Object.values(errors).flat().join(', ') || 'Erro ao atualizar atividade.';
            saving.value = false;
        }
    });
}

function submitDelete() {
    router.delete(route('atividades.destroy', atividadeDelete.value.id), {
        onSuccess: () => {
            feedback.value = 'Atividade eliminada com sucesso!';
            setTimeout(() => {
                closeModals();
                setTimeout(() => {
                    router.reload({ only: ['atividades'] });
                }, 100);
            }, 1000);
        },
        onError: (errors) => {
            feedback.value = Object.values(errors).flat().join(', ') || 'Erro ao eliminar atividade.';
        }
    });
}

// Função para formatar a data (remove a parte de timestamp)
const formatData = (data: string) => {
    if (!data) return '—';
    try {
        // Se a data contiver um 'T', é uma data ISO com timestamp
        if (data.includes('T')) {
            return data.split('T')[0]; // Retorna apenas YYYY-MM-DD
        }
        return data;
    } catch (e) {
        console.error('Erro ao formatar data:', e);
        return '—';
    }
};

// Função para formatar a hora (com verificação de nulo)
const formatHora = (hora: string) => {
    if (!hora) return '—';
    try {
        return hora.substring(0, 5); // Extrai apenas HH:MM
    } catch (e) {
        console.error('Erro ao formatar hora:', e);
        return '—';
    }
};

// Função para formatar duração em horas e minutos (com verificação de nulo)
const formatDuracao = (minutos: number | null | undefined) => {
    if (!minutos) return '—';
    try {
        const horas = Math.floor(minutos / 60);
        const min = minutos % 60;
        if (horas > 0) {
            return `${horas}h${min > 0 ? ` ${min}min` : ''}`;
        }
        return `${min}min`;
    } catch (e) {
        console.error('Erro ao formatar duração:', e);
        return '—';
    }
};
</script>

<template>
    <Head title="Atividades" />

    <AppLayout>
        <div class="p-6">
            <div class="mb-6 flex items-center justify-between">
                <h1 class="text-2xl font-bold">Atividades</h1>
                <a v-if="canEdit" :href="route('atividades.create')" class="px-4 py-2 rounded-lg bg-primary text-primary-foreground hover:bg-primary/90 font-medium transition">
                    Nova Atividade
                </a>
            </div>

            <div class="bg-zinc-800 shadow-lg rounded-lg overflow-hidden">
                <p v-if="!atividades || !atividades.data || atividades.data.length === 0" class="text-center py-6 text-gray-400">
                    Nenhuma atividade encontrada.
                </p>
                <div v-else class="overflow-hidden max-w-full">
                    <div class="overflow-x-auto">
                        <table class="w-full divide-y divide-zinc-700">
                            <thead>
                                <tr class="bg-zinc-900">
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Data</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Hora</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Duração</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Entidade</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Contacto</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Tipo</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Descrição</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-700">
                                <tr v-for="atividade in atividades.data" :key="atividade.id" class="hover:bg-zinc-700 transition cursor-pointer group" @click="() => router.visit(route('atividades.show', atividade.id))">
                                    <td class="px-4 py-4 whitespace-nowrap text-gray-200">{{ formatData(atividade.data) }}</td>
                                    <td class="px-4 py-4 whitespace-nowrap text-gray-200">{{ formatHora(atividade.hora) }}</td>
                                    <td class="px-4 py-4 whitespace-nowrap text-gray-200">{{ formatDuracao(atividade.duracao) }}</td>
                                    <td class="px-4 py-4 whitespace-nowrap text-gray-200">{{ atividade.entidade?.nome || '—' }}</td>
                                    <td class="px-4 py-4 whitespace-nowrap text-gray-200">{{ atividade.contacto?.nome || '—' }}</td>
                                    <td class="px-4 py-4 whitespace-nowrap text-gray-200">{{ atividade.tipo?.nome || '—' }}</td>
                                    <td class="px-4 py-4 max-w-xs truncate text-gray-200">{{ atividade.descricao || '—' }}</td>
                                    <td class="px-4 py-4 whitespace-nowrap text-center">
                                        <div class="flex items-center justify-center gap-2 opacity-0 group-hover:opacity-100 transition">
                                            <button v-if="canEdit" @click.stop="openEdit(atividade)" class="p-1 rounded hover:bg-zinc-600" title="Editar">
                                                <Pencil class="w-5 h-5 text-primary" />
                                            </button>
                                            <button v-if="isAdmin" @click.stop="openDelete(atividade)" class="p-1 rounded hover:bg-zinc-600" title="Eliminar">
                                                <Trash2 class="w-5 h-5 text-red-500" />
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div v-if="atividades.meta && atividades.meta.last_page > 1" class="mt-4 bg-zinc-800 px-6 py-3 flex justify-between items-center">
                        <div class="text-sm text-gray-400">
                            Mostrando {{ atividades.meta.current_page }} de {{ atividades.meta.last_page }} páginas
                        </div>
                        <div class="flex space-x-2">
                            <a v-if="atividades.meta.current_page > 1" 
                                :href="`?page=${atividades.meta.current_page - 1}`"
                                class="px-3 py-1 bg-zinc-700 text-gray-200 rounded-md hover:bg-zinc-600 transition">
                                Anterior
                            </a>
                            <a v-if="atividades.meta.current_page < atividades.meta.last_page" 
                                :href="`?page=${atividades.meta.current_page + 1}`"
                                class="px-3 py-1 bg-zinc-700 text-gray-200 rounded-md hover:bg-zinc-600 transition">
                                Próxima
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>

    <!-- Modal de Edição -->
    <Dialog v-model:open="showEditModal">
        <DialogContent class="max-w-md w-full">
            <DialogHeader>
                <DialogTitle>Editar Atividade</DialogTitle>
            </DialogHeader>
            <form @submit.prevent="submitEdit" class="space-y-4 mt-4">
                <div>
                    <label class="block mb-1 text-sm font-medium text-foreground">Data</label>
                    <input v-model="editForm.data" type="date" class="w-full border border-input bg-background rounded-lg px-3 py-2 text-foreground focus:outline-none focus:ring-2 focus:ring-primary dark:bg-zinc-800 dark:text-white" required />
                </div>
                <div>
                    <label class="block mb-1 text-sm font-medium text-foreground">Hora</label>
                    <input v-model="editForm.hora" type="time" class="w-full border border-input bg-background rounded-lg px-3 py-2 text-foreground focus:outline-none focus:ring-2 focus:ring-primary dark:bg-zinc-800 dark:text-white" required />
                </div>
                <div>
                    <label class="block mb-1 text-sm font-medium text-foreground">Duração (minutos)</label>
                    <input v-model="editForm.duracao" type="number" min="1" class="w-full border border-input bg-background rounded-lg px-3 py-2 text-foreground focus:outline-none focus:ring-2 focus:ring-primary dark:bg-zinc-800 dark:text-white" />
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
                    <label class="block mb-1 text-sm font-medium text-foreground">Contacto</label>
                    <input v-model="editForm.contacto_id" type="text" class="w-full border border-input bg-background rounded-lg px-3 py-2 text-foreground focus:outline-none focus:ring-2 focus:ring-primary dark:bg-zinc-800 dark:text-white" />
                </div>
                <div>
                    <label class="block mb-1 text-sm font-medium text-foreground">Tipo</label>
                    <select v-model="editForm.tipo_id" class="w-full border border-input bg-background rounded-lg px-3 py-2 text-foreground focus:outline-none focus:ring-2 focus:ring-primary dark:bg-zinc-800 dark:text-white">
                        <option value="">Selecione um tipo</option>
                        <option v-for="tipo in tipos" :key="tipo.id" :value="tipo.id">
                            {{ tipo.nome }}
                        </option>
                    </select>
                </div>
                <div>
                    <label class="block mb-1 text-sm font-medium text-foreground">Descrição</label>
                    <textarea v-model="editForm.descricao" class="w-full border border-input bg-background rounded-lg px-3 py-2 text-foreground focus:outline-none focus:ring-2 focus:ring-primary dark:bg-zinc-800 dark:text-white"></textarea>
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
                    <Trash2 class="w-5 h-5" />
                    Eliminar Atividade
                </DialogTitle>
            </DialogHeader>
            <div class="py-4">
                <p class="text-gray-200 mb-4">
                    Tem certeza que deseja eliminar esta atividade?
                </p>
                <p class="text-red-400 text-sm">
                    Esta ação é <span class="font-bold">irreversível</span>.
                </p>
            </div>
            <DialogFooter class="flex justify-end gap-2 mt-4">
                <button type="button" @click="closeModals" class="px-4 py-2 rounded bg-zinc-700 text-white hover:bg-zinc-600">Cancelar</button>
                <button type="button" @click="submitDelete" class="px-4 py-2 rounded bg-red-600 text-white hover:bg-red-700 font-semibold">Eliminar</button>
            </DialogFooter>
            <div v-if="feedback" class="text-center mt-2" :class="feedback.includes('Erro') ? 'text-red-500' : 'text-green-500'">{{ feedback }}</div>
        </DialogContent>
    </Dialog>
</template> 