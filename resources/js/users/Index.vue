<template>
    <Head title="Gestão de Utilizadores" />
    <AppLayout>
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold">Gestão de Utilizadores</h2>
                <Link v-if="$page.props.auth.user.isAdmin" :href="route('users.create')" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    Adicionar Utilizador
                </Link>
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div v-if="$page.props.flash.success" class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                        {{ $page.props.flash.success }}
                    </div>

                    <div v-if="$page.props.flash.error" class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                        {{ $page.props.flash.error }}
                    </div>

                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Funções</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="user in users" :key="user.id">
                                <td class="px-6 py-4 whitespace-nowrap">{{ user.name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ user.email }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span v-for="role in user.roles" :key="role.id" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mr-2">
                                        {{ role.nome }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <Link v-if="$page.props.auth.user.isAdmin" :href="route('users.edit', user.id)" class="text-indigo-600 hover:text-indigo-900 mr-4">Editar</Link>
                                    <button v-if="$page.props.auth.user.isAdmin && user.id !== $page.props.auth.user.id" @click="confirmDelete(user)" class="text-red-600 hover:text-red-900">Excluir</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal de confirmação de exclusão -->
        <div v-if="showDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white p-6 rounded-lg shadow-lg max-w-md w-full">
                <h3 class="text-lg font-medium text-gray-900">Confirmar exclusão</h3>
                <p class="mt-2 text-sm text-gray-500">
                    Tem certeza que deseja excluir o utilizador {{ userToDelete?.name }}? Esta ação não pode ser desfeita.
                </p>
                <div class="mt-4 flex justify-end space-x-3">
                    <button @click="cancelDelete" class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">
                        Cancelar
                    </button>
                    <button @click="deleteUser" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                        Excluir
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { ref } from 'vue';

const props = defineProps({
    users: Array,
    roles: Array
});

const showDeleteModal = ref(false);
const userToDelete = ref(null);

const confirmDelete = (user) => {
    userToDelete.value = user;
    showDeleteModal.value = true;
};

const cancelDelete = () => {
    showDeleteModal.value = false;
    userToDelete.value = null;
};

const deleteUser = () => {
    useForm().delete(route('users.destroy', userToDelete.value.id), {
        onSuccess: () => {
            showDeleteModal.value = false;
            userToDelete.value = null;
        }
    });
};
</script> 