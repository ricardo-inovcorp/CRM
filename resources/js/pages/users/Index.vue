<template>
    <Head title="Gestão de Utilizadores" />
    <AppLayout>
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold">Utilizadores</h1>
                <Link v-if="isAdmin" :href="route('users.create')" class="px-4 py-2 rounded-lg bg-primary text-primary-foreground hover:bg-primary/90 font-medium transition">
                    Novo Utilizador
                </Link>
            </div>

            <div class="bg-zinc-800 shadow-lg rounded-lg overflow-hidden">
                <div v-if="$page.props.flash && $page.props.flash.success" class="m-4 p-2 bg-green-900/50 text-green-300 rounded">
                    {{ $page.props.flash.success }}
                </div>

                <div v-if="$page.props.flash && $page.props.flash.error" class="m-4 p-2 bg-red-900/50 text-red-300 rounded">
                    {{ $page.props.flash.error }}
                </div>

                <p v-if="!users || users.length === 0" class="text-center py-6 text-gray-400">
                    Nenhum utilizador encontrado.
                </p>
                <div v-else class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-zinc-700">
                        <thead>
                            <tr class="bg-zinc-900">
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Nome</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Funções</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-400 uppercase tracking-wider">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-700">
                            <tr v-for="user in users" :key="user.id" class="hover:bg-zinc-700 transition group">
                                <td class="px-6 py-4 whitespace-nowrap text-gray-200">{{ user.name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-200">{{ user.email }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span v-for="role in user.roles" :key="role.id" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-900 text-blue-300 mr-2">
                                        {{ role.nome }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="flex items-center justify-center gap-2 opacity-0 group-hover:opacity-100 transition">
                                        <button v-if="isAdmin" @click="openEdit(user)" class="p-1 rounded hover:bg-zinc-600">
                                            <Pencil class="w-5 h-5 text-primary" />
                                        </button>
                                        <button v-if="isAdmin && user.id !== $page.props.auth.user.id" @click="confirmDelete(user)" class="p-1 rounded hover:bg-zinc-600">
                                            <Trash2 class="w-5 h-5 text-red-500" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal de confirmação de exclusão -->
        <Dialog v-model:open="showDeleteModal">
            <DialogContent class="max-w-md w-full">
                <DialogHeader>
                    <DialogTitle>Confirmar exclusão</DialogTitle>
                </DialogHeader>
                <div class="mt-2">
                    <p class="text-sm text-gray-300">
                        Tem certeza que deseja excluir o utilizador <span class="font-medium">{{ userToDelete?.name }}</span>? Esta ação não pode ser desfeita.
                    </p>
                </div>
                <DialogFooter class="flex justify-end gap-2 mt-4">
                    <button @click="cancelDelete" class="px-4 py-2 rounded bg-zinc-700 text-white hover:bg-zinc-600">
                        Cancelar
                    </button>
                    <button @click="deleteUser" class="px-4 py-2 rounded bg-red-600 text-white hover:bg-red-700">
                        Excluir
                    </button>
                </DialogFooter>
            </DialogContent>
        </Dialog>

        <!-- Modal de Edição -->
        <Dialog v-model:open="showEditModal">
            <DialogContent class="max-w-md w-full">
                <DialogHeader>
                    <DialogTitle>Editar Utilizador</DialogTitle>
                </DialogHeader>
                <form @submit.prevent="submitEdit" class="space-y-4 mt-4">
                    <div>
                        <label class="block mb-1 text-sm font-medium text-foreground">Nome</label>
                        <input v-model="editForm.name" type="text" class="w-full border border-input bg-background rounded-lg px-3 py-2 text-foreground focus:outline-none focus:ring-2 focus:ring-primary dark:bg-zinc-800 dark:text-white" required />
                    </div>
                    <div>
                        <label class="block mb-1 text-sm font-medium text-foreground">Email</label>
                        <input v-model="editForm.email" type="email" class="w-full border border-input bg-background rounded-lg px-3 py-2 text-foreground focus:outline-none focus:ring-2 focus:ring-primary dark:bg-zinc-800 dark:text-white" required />
                    </div>
                    <div>
                        <label class="block mb-1 text-sm font-medium text-foreground">Password (deixe em branco para manter a mesma)</label>
                        <input v-model="editForm.password" type="password" class="w-full border border-input bg-background rounded-lg px-3 py-2 text-foreground focus:outline-none focus:ring-2 focus:ring-primary dark:bg-zinc-800 dark:text-white" />
                    </div>
                    <div>
                        <label class="block mb-1 text-sm font-medium text-foreground">Confirmar Password</label>
                        <input v-model="editForm.password_confirmation" type="password" class="w-full border border-input bg-background rounded-lg px-3 py-2 text-foreground focus:outline-none focus:ring-2 focus:ring-primary dark:bg-zinc-800 dark:text-white" />
                    </div>
                    <div>
                        <label class="block mb-1 text-sm font-medium text-foreground">Funções</label>
                        <div class="mt-2 space-y-2">
                            <template v-if="isAdmin">
                                <div v-for="role in roles" :key="role.id" class="flex items-center">
                                    <input
                                        :id="'role-' + role.id"
                                        type="radio"
                                        :value="role.id"
                                        v-model="editForm.roles"
                                        :name="'user-role'"
                                        class="h-4 w-4 text-primary border-input rounded focus:ring-primary"
                                    />
                                    <label :for="'role-' + role.id" class="ml-2 block text-sm text-gray-300">
                                        {{ role.nome }} <span class="text-muted-foreground text-xs">{{ role.descricao }}</span>
                                    </label>
                                </div>
                            </template>
                            <template v-else>
                                <span v-for="roleId in editForm.roles" :key="roleId">
                                    {{ roles.find(r => r.id === roleId)?.nome }}
                                </span>
                            </template>
                        </div>
                    </div>
                    <DialogFooter class="flex justify-end gap-2 mt-4">
                        <button type="button" @click="closeModals" class="px-4 py-2 rounded bg-zinc-700 text-white hover:bg-zinc-600">Cancelar</button>
                        <button v-if="isAdmin" type="submit" class="px-4 py-2 rounded bg-zinc-900 text-grey-900 hover:bg-zinc-800 font-semibold" :disabled="saving">
                            <span v-if="saving">Salvando...</span>
                            <span v-else>Salvar</span>
                        </button>
                    </DialogFooter>
                    <div v-if="feedback" class="text-center mt-2" :class="feedback.includes('Erro') ? 'text-red-500' : 'text-green-500'">{{ feedback }}</div>
                </form>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>

<script setup>
import { Head, Link, useForm, router, usePage } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { ref, onMounted, onErrorCaptured } from 'vue';
import { Pencil, Trash2 } from 'lucide-vue-next';
import Dialog from '@/components/ui/dialog/Dialog.vue';
import DialogContent from '@/components/ui/dialog/DialogContent.vue';
import DialogHeader from '@/components/ui/dialog/DialogHeader.vue';
import DialogTitle from '@/components/ui/dialog/DialogTitle.vue';
import DialogFooter from '@/components/ui/dialog/DialogFooter.vue';

const props = defineProps({
    users: Array,
    roles: Array
});

const showDeleteModal = ref(false);
const userToDelete = ref(null);
const showEditModal = ref(false);
const userToEdit = ref(null);
const editForm = ref({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    roles: []
});
const feedback = ref('');
const saving = ref(false);

const page = usePage();
const user = page.props.auth.user;

// Usar isAdmin/isManager do próprio user (já calculados no backend)
const isAdmin = !!user.isAdmin;
const isManager = !!user.isManager;

function hasRole(user, slug) {
    return user.roles && user.roles.some(r => r.slug === slug);
}

// Debug logs
onMounted(() => {
    console.log('Users component mounted');
    console.log('Props received:', props);
});

// Error capture
onErrorCaptured((err, vm, info) => {
    console.error('Error in Users component:', err);
    console.error('Component:', vm);
    console.error('Info:', info);
    return false; // Allow the error to propagate
});

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

const openEdit = (user) => {
    userToEdit.value = user;
    editForm.value = {
        name: user.name,
        email: user.email,
        password: '',
        password_confirmation: '',
        roles: user.roles.length > 0 ? [user.roles[0].id] : []
    };
    showEditModal.value = true;
};

const closeModals = () => {
    showEditModal.value = false;
    showDeleteModal.value = false;
    userToEdit.value = null;
    userToDelete.value = null;
    feedback.value = '';
};

const submitEdit = () => {
    if (!Array.isArray(editForm.value.roles)) {
        editForm.value.roles = [editForm.value.roles];
    }
    saving.value = true;
    router.put(route('users.update', userToEdit.value.id), editForm.value, {
        preserveScroll: true,
        onSuccess: () => {
            feedback.value = 'Utilizador atualizado com sucesso!';
            saving.value = false;
            setTimeout(() => {
                closeModals();
                setTimeout(() => {
                    router.reload({ only: ['users'] });
                }, 100);
            }, 1000);
        },
        onError: (errors) => {
            feedback.value = Object.values(errors).flat().join(', ') || 'Erro ao atualizar utilizador.';
            saving.value = false;
        }
    });
};
</script> 