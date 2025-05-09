<template>
    <Head title="Detalhes do Utilizador" />
    <AppLayout>
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold">Detalhes do Utilizador</h2>
                <div class="flex space-x-4">
                    <Link v-if="$page.props.auth.user.isAdmin" :href="route('users.edit', user.id)" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        Editar
                    </Link>
                    <Link :href="route('users.index')" class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">
                        Voltar
                    </Link>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">Informações Básicas</h3>
                            <div class="mt-4 space-y-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Nome</p>
                                    <p class="mt-1 text-md text-gray-900">{{ user.name }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Email</p>
                                    <p class="mt-1 text-md text-gray-900">{{ user.email }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Data de Cadastro</p>
                                    <p class="mt-1 text-md text-gray-900">{{ new Date(user.created_at).toLocaleDateString() }}</p>
                                </div>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">Funções</h3>
                            <div class="mt-4">
                                <div class="flex flex-wrap gap-2">
                                    <span v-for="role in user.roles" :key="role.id" class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                        {{ role.nome }}
                                    </span>
                                    <p v-if="user.roles.length === 0" class="text-sm text-gray-500">Nenhuma função atribuída</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

defineProps({
    user: Object,
});
</script> 