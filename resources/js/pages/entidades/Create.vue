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
    paises: Array<{
        id: number;
        nome: string;
    }>;
}

const props = defineProps<Props>();

// Definindo o schema de validação
const formSchema = toTypedSchema(
    z.object({
        nome: z.string().min(2, 'Nome é obrigatório').max(255),
        morada: z.string().max(255).nullable().optional(),
        codigo_postal: z.string().max(20).nullable().optional(),
        localidade: z.string().max(100).nullable().optional(),
        pais_id: z.string().nullable().optional(),
        telefone: z.string().max(20).nullable().optional(),
        email: z.string().email('Email inválido').max(255).nullable().optional(),
        website: z.string().url('URL inválida').max(255).nullable().optional(),
        observacoes: z.string().nullable().optional(),
        estado: z.string().min(1, 'Estado é obrigatório'),
    })
);

// Formulário Inertia para submissão de dados
const form = useForm({
    nome: '',
    morada: '',
    codigo_postal: '',
    localidade: '',
    pais_id: '',
    telefone: '',
    email: '',
    website: '',
    observacoes: '',
    estado: 'Ativo',
});

// Formulário VeeValidate para validação
const veeForm = useVeeForm({
    validationSchema: formSchema,
    initialValues: {
        nome: '',
        morada: '',
        codigo_postal: '',
        localidade: '',
        pais_id: '',
        telefone: '',
        email: '',
        website: '',
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
        pais_id: values.pais_id ? parseInt(values.pais_id) : null,
    };
    
    form.post(route('entidades.store'), formattedValues);
});
</script>

<template>
    <Head title="Nova Entidade" />

    <AppLayout :breadcrumbs="[
        { label: 'Entidades', href: route('entidades.index') },
        { label: 'Nova Entidade', active: true }
    ]">
        <div class="p-6">
            <div class="mb-6 flex items-center">
                <Button variant="ghost" size="sm" :href="route('entidades.index')" as-child>
                    <a class="flex items-center">
                        <ArrowLeft class="mr-2 h-4 w-4" />
                        Voltar
                    </a>
                </Button>
                <h1 class="ml-4 text-2xl font-bold">Nova Entidade</h1>
            </div>

            <Card>
                <CardHeader>
                    <CardTitle>Informações da Entidade</CardTitle>
                    <CardDescription>
                        Preencha os dados da nova entidade. Campos marcados com * são obrigatórios.
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

                            <!-- País -->
                            <FormField
                                :validator="veeForm.getFieldState('pais_id')"
                                name="pais_id"
                                :server-error="form.errors.pais_id"
                            >
                                <FormItem>
                                    <FormLabel>País</FormLabel>
                                    <FormControl>
                                        <Select v-model="form.pais_id">
                                            <SelectTrigger>
                                                <SelectValue placeholder="Selecione um país" />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectItem value="">Nenhum</SelectItem>
                                                <SelectItem
                                                    v-for="pais in paises"
                                                    :key="pais.id"
                                                    :value="pais.id.toString()"
                                                >
                                                    {{ pais.nome }}
                                                </SelectItem>
                                            </SelectContent>
                                        </Select>
                                    </FormControl>
                                    <FormMessage />
                                </FormItem>
                            </FormField>

                            <!-- Morada -->
                            <FormField
                                :validator="veeForm.getFieldState('morada')"
                                name="morada"
                                :server-error="form.errors.morada"
                            >
                                <FormItem>
                                    <FormLabel>Morada</FormLabel>
                                    <FormControl>
                                        <Input id="morada" v-model="form.morada" />
                                    </FormControl>
                                    <FormMessage />
                                </FormItem>
                            </FormField>

                            <!-- Localidade -->
                            <FormField
                                :validator="veeForm.getFieldState('localidade')"
                                name="localidade"
                                :server-error="form.errors.localidade"
                            >
                                <FormItem>
                                    <FormLabel>Localidade</FormLabel>
                                    <FormControl>
                                        <Input id="localidade" v-model="form.localidade" />
                                    </FormControl>
                                    <FormMessage />
                                </FormItem>
                            </FormField>

                            <!-- Código Postal -->
                            <FormField
                                :validator="veeForm.getFieldState('codigo_postal')"
                                name="codigo_postal"
                                :server-error="form.errors.codigo_postal"
                            >
                                <FormItem>
                                    <FormLabel>Código Postal</FormLabel>
                                    <FormControl>
                                        <Input id="codigo_postal" v-model="form.codigo_postal" />
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

                            <!-- Website -->
                            <FormField
                                :validator="veeForm.getFieldState('website')"
                                name="website"
                                :server-error="form.errors.website"
                            >
                                <FormItem>
                                    <FormLabel>Website</FormLabel>
                                    <FormControl>
                                        <Input id="website" v-model="form.website" />
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
                                :href="route('entidades.index')"
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