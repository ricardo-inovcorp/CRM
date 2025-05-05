<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Form, FormControl, FormField, FormItem, FormLabel, FormMessage } from '@/components/ui/form';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import { Head, useForm } from '@inertiajs/vue3';
import { useForm as useVeeForm } from 'vee-validate';
import { toTypedSchema } from '@vee-validate/zod';
import * as z from 'zod';
import { ArrowLeft, Save } from 'lucide-vue-next';

interface Props {
    entidades: Array<{
        id: number;
        nome: string;
    }>;
    funcoes: Array<{
        id: number;
        nome: string;
    }>;
}

const props = defineProps<Props>();

// Definindo o schema de validação
const formSchema = toTypedSchema(
    z.object({
        nome: z.string().min(2, 'Nome é obrigatório').max(255),
        apelido: z.string().max(255).nullable().optional(),
        entidade_id: z.string().min(1, 'Entidade é obrigatória'),
        funcao_id: z.string().nullable().optional(),
        telefone: z.string().max(20).nullable().optional(),
        telemovel: z.string().max(20).nullable().optional(),
        email: z.string().email('Email inválido').max(255).nullable().optional(),
        observacoes: z.string().nullable().optional(),
        estado: z.string().min(1, 'Estado é obrigatório'),
    })
);

// Formulário Inertia para submissão de dados
const form = useForm({
    nome: '',
    apelido: '',
    entidade_id: '',
    funcao_id: '',
    telefone: '',
    telemovel: '',
    email: '',
    observacoes: '',
    estado: 'Ativo',
});

// Formulário VeeValidate para validação
const veeForm = useVeeForm({
    validationSchema: formSchema,
    initialValues: {
        nome: '',
        apelido: '',
        entidade_id: '',
        funcao_id: '',
        telefone: '',
        telemovel: '',
        email: '',
        observacoes: '',
        estado: 'Ativo',
    },
});

// Enviar o formulário
const onSubmit = veeForm.handleSubmit((values) => {
    form.clearErrors();
    
    // Converter valores para o formato esperado pela API
    const formattedValues = {
        ...values,
        entidade_id: parseInt(values.entidade_id),
        funcao_id: values.funcao_id ? parseInt(values.funcao_id) : null,
    };
    
    form.post(route('contactos.store'), formattedValues);
});
</script>

<template>
    <Head title="Novo Contacto" />

    <AppLayout :breadcrumbs="[
        { label: 'Contactos', href: route('contactos.index') },
        { label: 'Novo Contacto', active: true }
    ]">
        <div class="p-6">
            <div class="mb-6 flex items-center">
                <Button variant="ghost" size="sm" :href="route('contactos.index')" as-child>
                    <a class="flex items-center">
                        <ArrowLeft class="mr-2 h-4 w-4" />
                        Voltar
                    </a>
                </Button>
                <h1 class="ml-4 text-2xl font-bold">Novo Contacto</h1>
            </div>

            <Card>
                <CardHeader>
                    <CardTitle>Informações do Contacto</CardTitle>
                    <CardDescription>
                        Preencha os dados do novo contacto. Campos marcados com * são obrigatórios.
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <Form @submit="onSubmit" class="space-y-6">
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <!-- Nome -->
                            <FormField
                                :validator="veeForm.getFieldState('nome')"
                                name="nome"
                                :server-error="form.errors.nome"
                            >
                                <FormItem>
                                    <FormLabel>Nome *</FormLabel>
                                    <FormControl>
                                        <Input id="nome" v-model="form.nome" />
                                    </FormControl>
                                    <FormMessage />
                                </FormItem>
                            </FormField>

                            <!-- Apelido -->
                            <FormField
                                :validator="veeForm.getFieldState('apelido')"
                                name="apelido"
                                :server-error="form.errors.apelido"
                            >
                                <FormItem>
                                    <FormLabel>Apelido</FormLabel>
                                    <FormControl>
                                        <Input id="apelido" v-model="form.apelido" />
                                    </FormControl>
                                    <FormMessage />
                                </FormItem>
                            </FormField>

                            <!-- Entidade -->
                            <FormField
                                :validator="veeForm.getFieldState('entidade_id')"
                                name="entidade_id"
                                :server-error="form.errors.entidade_id"
                            >
                                <FormItem>
                                    <FormLabel>Entidade *</FormLabel>
                                    <FormControl>
                                        <Select v-model="form.entidade_id">
                                            <SelectTrigger>
                                                <SelectValue placeholder="Selecione uma entidade" />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectItem
                                                    v-for="entidade in entidades"
                                                    :key="entidade.id"
                                                    :value="entidade.id.toString()"
                                                >
                                                    {{ entidade.nome }}
                                                </SelectItem>
                                            </SelectContent>
                                        </Select>
                                    </FormControl>
                                    <FormMessage />
                                </FormItem>
                            </FormField>

                            <!-- Função -->
                            <FormField
                                :validator="veeForm.getFieldState('funcao_id')"
                                name="funcao_id"
                                :server-error="form.errors.funcao_id"
                            >
                                <FormItem>
                                    <FormLabel>Função</FormLabel>
                                    <FormControl>
                                        <Select v-model="form.funcao_id">
                                            <SelectTrigger>
                                                <SelectValue placeholder="Selecione uma função" />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectItem value="">Nenhuma</SelectItem>
                                                <SelectItem
                                                    v-for="funcao in funcoes"
                                                    :key="funcao.id"
                                                    :value="funcao.id.toString()"
                                                >
                                                    {{ funcao.nome }}
                                                </SelectItem>
                                            </SelectContent>
                                        </Select>
                                    </FormControl>
                                    <FormMessage />
                                </FormItem>
                            </FormField>

                            <!-- Telefone -->
                            <FormField
                                :validator="veeForm.getFieldState('telefone')"
                                name="telefone"
                                :server-error="form.errors.telefone"
                            >
                                <FormItem>
                                    <FormLabel>Telefone</FormLabel>
                                    <FormControl>
                                        <Input id="telefone" v-model="form.telefone" />
                                    </FormControl>
                                    <FormMessage />
                                </FormItem>
                            </FormField>

                            <!-- Telemóvel -->
                            <FormField
                                :validator="veeForm.getFieldState('telemovel')"
                                name="telemovel"
                                :server-error="form.errors.telemovel"
                            >
                                <FormItem>
                                    <FormLabel>Telemóvel</FormLabel>
                                    <FormControl>
                                        <Input id="telemovel" v-model="form.telemovel" />
                                    </FormControl>
                                    <FormMessage />
                                </FormItem>
                            </FormField>

                            <!-- Email -->
                            <FormField
                                :validator="veeForm.getFieldState('email')"
                                name="email"
                                :server-error="form.errors.email"
                            >
                                <FormItem>
                                    <FormLabel>Email</FormLabel>
                                    <FormControl>
                                        <Input id="email" type="email" v-model="form.email" />
                                    </FormControl>
                                    <FormMessage />
                                </FormItem>
                            </FormField>

                            <!-- Estado -->
                            <FormField
                                :validator="veeForm.getFieldState('estado')"
                                name="estado"
                                :server-error="form.errors.estado"
                            >
                                <FormItem>
                                    <FormLabel>Estado *</FormLabel>
                                    <FormControl>
                                        <Select v-model="form.estado">
                                            <SelectTrigger>
                                                <SelectValue placeholder="Selecione um estado" />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectItem value="Ativo">Ativo</SelectItem>
                                                <SelectItem value="Inativo">Inativo</SelectItem>
                                            </SelectContent>
                                        </Select>
                                    </FormControl>
                                    <FormMessage />
                                </FormItem>
                            </FormField>
                        </div>

                        <!-- Observações -->
                        <FormField
                            :validator="veeForm.getFieldState('observacoes')"
                            name="observacoes"
                            :server-error="form.errors.observacoes"
                        >
                            <FormItem>
                                <FormLabel>Observações</FormLabel>
                                <FormControl>
                                    <Textarea
                                        id="observacoes"
                                        v-model="form.observacoes"
                                        rows="3"
                                    />
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>

                        <CardFooter class="flex justify-end space-x-4 px-0 pb-0">
                            <Button
                                type="button"
                                variant="outline"
                                :href="route('contactos.index')"
                                as-child
                            >
                                <a>Cancelar</a>
                            </Button>
                            <Button type="submit" :disabled="form.processing">
                                <Save class="mr-2 h-4 w-4" />
                                Salvar
                            </Button>
                        </CardFooter>
                    </Form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template> 