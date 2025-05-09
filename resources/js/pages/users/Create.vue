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
                                <FormLabel for="name">Nome</FormLabel>
                                <Input
                                    id="name"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.name"
                                    required
                                    autofocus
                                />
                                <FormMessage v-if="form.errors.name" :message="form.errors.name" />
                            </div>

                            <!-- Email -->
                            <div>
                                <FormLabel for="email">Email</FormLabel>
                                <Input
                                    id="email"
                                    type="email"
                                    class="mt-1 block w-full"
                                    v-model="form.email"
                                    required
                                />
                                <FormMessage v-if="form.errors.email" :message="form.errors.email" />
                            </div>

                            <!-- Password -->
                            <div>
                                <FormLabel for="password">Password</FormLabel>
                                <Input
                                    id="password"
                                    type="password"
                                    class="mt-1 block w-full"
                                    v-model="form.password"
                                    required
                                    autocomplete="new-password"
                                />
                                <FormMessage v-if="form.errors.password" :message="form.errors.password" />
                            </div>

                            <!-- Password Confirmation -->
                            <div>
                                <FormLabel for="password_confirmation">Confirmar Password</FormLabel>
                                <Input
                                    id="password_confirmation"
                                    type="password"
                                    class="mt-1 block w-full"
                                    v-model="form.password_confirmation"
                                    required
                                    autocomplete="new-password"
                                />
                                <FormMessage v-if="form.errors.password_confirmation" :message="form.errors.password_confirmation" />
                            </div>

                            <!-- Roles -->
                            <div>
                                <FormLabel>Funções</FormLabel>
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
                                <FormMessage v-if="form.errors.roles" :message="form.errors.roles" />
                            </div>

                            <div class="flex items-center justify-end mt-4">
                                <Button class="ml-4" :disabled="form.processing">
                                    Criar Utilizador
                                </Button>
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
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { FormLabel, FormMessage } from '@/components/ui/form';

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