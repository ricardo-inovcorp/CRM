<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

// Simplificando o tipo para evitar erros
interface Props {
    entidades: any;
    filters: any;
}

defineProps<Props>();
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
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-700">
                                <tr v-for="entidade in entidades.data" :key="entidade.id" class="hover:bg-zinc-700 transition">
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-200">{{ entidade.nome }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-200">{{ entidade.email || '—' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-200">{{ entidade.telefone || '—' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full"
                                            :class="entidade.estado === 'Ativo' ? 'bg-green-900 text-green-300' : 'bg-red-900 text-red-300'">
                                            {{ entidade.estado }}
                                        </span>
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
        </div>
    </AppLayout>
</template> 