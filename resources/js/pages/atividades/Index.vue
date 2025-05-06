<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

// Simplificando o tipo para evitar erros
interface Props {
    atividades: any;
    entidades: any;
    tipos: any;
    filters: any;
}

defineProps<Props>();

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
                <a :href="route('atividades.create')" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition">
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
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-700">
                                <tr v-for="atividade in atividades.data" :key="atividade.id" class="hover:bg-zinc-700 transition">
                                    <td class="px-4 py-4 whitespace-nowrap text-gray-200">{{ atividade.data || '—' }}</td>
                                    <td class="px-4 py-4 whitespace-nowrap text-gray-200">{{ formatHora(atividade.hora) }}</td>
                                    <td class="px-4 py-4 whitespace-nowrap text-gray-200">{{ formatDuracao(atividade.duracao) }}</td>
                                    <td class="px-4 py-4 whitespace-nowrap text-gray-200">{{ atividade.entidade?.nome || '—' }}</td>
                                    <td class="px-4 py-4 whitespace-nowrap text-gray-200">{{ atividade.contacto?.nome || '—' }}</td>
                                    <td class="px-4 py-4 whitespace-nowrap text-gray-200">{{ atividade.tipo?.nome || '—' }}</td>
                                    <td class="px-4 py-4 max-w-xs truncate text-gray-200">{{ atividade.descricao || '—' }}</td>
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
</template> 