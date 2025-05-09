<template>
    <Head title="Novo Utilizador" />
    <AppLayout :breadcrumbs="[
        { label: 'Utilizadores', href: route('users.index') },
        { label: 'Novo Utilizador', active: true }
    ]">
        <div class="p-6">
            <div class="mb-6 flex items-center">
                <Button variant="ghost" size="sm" :href="route('users.index')" as-child>
                    <a class="flex items-center">
                        <ArrowLeft class="mr-2 h-4 w-4" />
                        Voltar
                    </a>
                </Button>
                <h1 class="ml-4 text-2xl font-bold">Novo Utilizador</h1>
            </div>

            <Card>
                <CardHeader>
                    <CardTitle>Informações do Utilizador</CardTitle>
                    <CardDescription>
                        Preencha os dados do novo utilizador. Campos marcados com * são obrigatórios.
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <!-- Nome -->
                            <div class="space-y-2">
                                <label class="text-sm font-medium">Nome <span class="text-red-500">*</span></label>
                                <Input v-model="form.name" placeholder="Nome do utilizador" required autofocus />
                                <p v-if="form.errors.name" class="text-sm text-red-500">{{ form.errors.name }}</p>
                            </div>

                            <!-- Email -->
                            <div class="space-y-2">
                                <label class="text-sm font-medium">Email <span class="text-red-500">*</span></label>
                                <Input v-model="form.email" type="email" placeholder="Email do utilizador" required />
                                <p v-if="form.errors.email" class="text-sm text-red-500">{{ form.errors.email }}</p>
                            </div>

                            <!-- Password -->
                            <div class="space-y-2">
                                <label class="text-sm font-medium">Password <span class="text-red-500">*</span></label>
                                <Input 
                                    v-model="form.password" 
                                    type="password" 
                                    placeholder="Password" 
                                    required 
                                    autocomplete="new-password"
                                />
                                <p v-if="form.errors.password" class="text-sm text-red-500">{{ form.errors.password }}</p>
                            </div>

                            <!-- Password Confirmation -->
                            <div class="space-y-2">
                                <label class="text-sm font-medium">Confirmar Password <span class="text-red-500">*</span></label>
                                <Input 
                                    v-model="form.password_confirmation" 
                                    type="password" 
                                    placeholder="Confirmar password" 
                                    required 
                                    autocomplete="new-password"
                                />
                                <p v-if="form.errors.password_confirmation" class="text-sm text-red-500">{{ form.errors.password_confirmation }}</p>
                            </div>

                            <!-- Estado -->
                            <div class="space-y-2">
                                <label class="text-sm font-medium">Estado</label>
                                <div class="flex items-center space-x-4">
                                    <div class="flex items-center">
                                        <input 
                                            id="estado-ativo" 
                                            type="radio" 
                                            name="estado" 
                                            value="ativo" 
                                            checked 
                                            class="h-4 w-4 text-primary focus:ring-primary"
                                        />
                                        <label for="estado-ativo" class="ml-2 text-sm">Ativo</label>
                                    </div>
                                    <div class="flex items-center">
                                        <input 
                                            id="estado-inativo" 
                                            type="radio" 
                                            name="estado" 
                                            value="inativo" 
                                            class="h-4 w-4 text-primary focus:ring-primary"
                                        />
                                        <label for="estado-inativo" class="ml-2 text-sm">Inativo</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Funções - Ocupa toda a largura -->
                        <div class="space-y-2">
                            <label class="text-sm font-medium">Funções <span class="text-red-500">*</span></label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 mt-2">
                                <div v-for="role in roles" :key="role.id" class="flex items-center">
                                    <input
                                        :id="'role-' + role.id"
                                        type="checkbox"
                                        :value="role.id"
                                        v-model="form.roles"
                                        class="h-4 w-4 text-primary border-input rounded focus:ring-primary"
                                    />
                                    <label :for="'role-' + role.id" class="ml-2 block text-sm">
                                        {{ role.nome }} <span class="text-muted-foreground text-xs">{{ role.descricao }}</span>
                                    </label>
                                </div>
                            </div>
                            <p v-if="form.errors.roles" class="text-sm text-red-500">{{ form.errors.roles }}</p>
                        </div>
                        
                        <CardFooter class="flex justify-end px-0 pt-6 pb-0 gap-4">
                            <Button variant="outline" type="button" :href="route('users.index')" as-child>
                                <a>Cancelar</a>
                            </Button>
                            <Button type="submit" :disabled="form.processing" class="flex items-center gap-1">
                                <Save class="h-4 w-4" />
                                Salvar
                            </Button>
                        </CardFooter>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { FormLabel, FormMessage } from '@/components/ui/form';
import { ArrowLeft, Save } from 'lucide-vue-next';

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