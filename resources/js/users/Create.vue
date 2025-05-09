<template>
    <Head title="Criar Utilizador" />
    <AppLayout>
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold">Criar Novo Utilizador</h2>
                <Link :href="route('users.index')" class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">
                    Voltar
                </Link>
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form @submit.prevent="submit">
                        <div class="grid grid-cols-1 gap-6">
                            <!-- Nome -->
                            <div>
                                <InputLabel for="name" value="Nome" />
                                <TextInput
                                    id="name"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.name"
                                    required
                                    autofocus
                                />
                                <InputError :message="form.errors.name" class="mt-2" />
                            </div>

                            <!-- Email -->
                            <div>
                                <InputLabel for="email" value="Email" />
                                <TextInput
                                    id="email"
                                    type="email"
                                    class="mt-1 block w-full"
                                    v-model="form.email"
                                    required
                                />
                                <InputError :message="form.errors.email" class="mt-2" />
                            </div>

                            <!-- Password -->
                            <div>
                                <InputLabel for="password" value="Password" />
                                <TextInput
                                    id="password"
                                    type="password"
                                    class="mt-1 block w-full"
                                    v-model="form.password"
                                    required
                                    autocomplete="new-password"
                                />
                                <InputError :message="form.errors.password" class="mt-2" />
                            </div>

                            <!-- Password Confirmation -->
                            <div>
                                <InputLabel for="password_confirmation" value="Confirmar Password" />
                                <TextInput
                                    id="password_confirmation"
                                    type="password"
                                    class="mt-1 block w-full"
                                    v-model="form.password_confirmation"
                                    required
                                    autocomplete="new-password"
                                />
                                <InputError :message="form.errors.password_confirmation" class="mt-2" />
                            </div>

                            <!-- Roles -->
                            <div>
                                <InputLabel value="Funções" />
                                <div class="mt-2 space-y-2">
                                    <div v-for="role in roles" :key="role.id" class="flex items-center">
                                        <input
                                            :id="'role-' + role.id"
                                            type="checkbox"
                                            :value="role.id"
                                            v-model="form.roles"
                                            class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                        />
                                        <label :for="'role-' + role.id" class="ml-2 block text-sm text-gray-900">
                                            {{ role.nome }} - {{ role.descricao }}
                                        </label>
                                    </div>
                                </div>
                                <InputError :message="form.errors.roles" class="mt-2" />
                            </div>

                            <div class="flex items-center justify-end mt-4">
                                <PrimaryButton class="ml-4" :disabled="form.processing">
                                    Criar Utilizador
                                </PrimaryButton>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    roles: Array,
});

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    roles: [],
});

const submit = () => {
    form.post(route('users.store'), {
        onSuccess: () => {
            form.reset('password', 'password_confirmation');
        }
    });
};
</script> 