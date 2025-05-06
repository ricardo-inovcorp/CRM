<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Form, FormControl, FormField, FormItem, FormLabel, FormMessage } from '@/components/ui/form';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { z } from 'zod';
import { toTypedSchema } from '@vee-validate/zod';
import { useForm as useVeeForm } from 'vee-validate';
import { computed } from 'vue';

interface Props {
    entidades: Array<{
        id: number;
        nome: string;
    }>;
    contactos: Array<{
        id: number;
        nome: string;
        entidade_id: number;
    }>;
    tipos: Array<{
        id: number;
        nome: string;
    }>;
}

const props = defineProps<Props>();

// Schema para validação
const schema = toTypedSchema(
    z.object({
        titulo: z.string().min(3, 'O título deve ter pelo menos 3 caracteres'),
        descricao: z.string().optional(),
        data: z.string().min(1, 'A data é obrigatória'),
        tipo_id: z.number().int().min(1, 'Selecione um tipo de atividade'),
        entidade_id: z.number().int().optional(),
        contacto_id: z.number().int().optional(),
    })
);

// Form reativo para VeeValidate
const veeForm = useVeeForm({
    validationSchema: schema,
    initialValues: {
        titulo: '',
        descricao: '',
        data: new Date().toISOString().slice(0, 10),
        tipo_id: 0,
        entidade_id: 0,
        contacto_id: 0,
    },
});

// Form do Inertia para submissão
const form = useForm({
    titulo: '',
    descricao: '',
    data: new Date().toISOString().slice(0, 10),
    tipo_id: 0,
    entidade_id: 0,
    contacto_id: 0,
});

// Filtra contactos baseado na entidade selecionada
const contactosFiltrados = computed(() => {
    if (!veeForm.values.entidade_id) return [];
    return props.contactos.filter(c => c.entidade_id === veeForm.values.entidade_id);
});

// Submissão do formulário
const onSubmit = () => {
    form.titulo = veeForm.values.titulo;
    form.descricao = veeForm.values.descricao || '';
    form.data = veeForm.values.data;
    form.tipo_id = veeForm.values.tipo_id;
    form.entidade_id = veeForm.values.entidade_id || null;
    form.contacto_id = veeForm.values.contacto_id || null;
    
    form.post(route('atividades.store'), {
        onSuccess: () => {
            veeForm.resetForm();
        },
    });
};
</script>

<template>
    <Head title="Nova Atividade" />

    <AppLayout :breadcrumbs="[
        { label: 'Atividades', href: route('atividades.index') },
        { label: 'Nova Atividade', active: true }
    ]">
        <div class="p-6">
            <h1 class="text-2xl font-bold mb-6">Nova Atividade</h1>

            <Card>
                <CardHeader>
                    <CardTitle>Detalhes da Atividade</CardTitle>
                    <CardDescription>Preencha as informações para criar uma nova atividade.</CardDescription>
                </CardHeader>
                <CardContent>
                    <Form @submit="onSubmit" :validation-schema="schema" class="space-y-4">
                        <!-- Título -->
                        <FormField v-slot="{ field, errorMessage }" name="titulo">
                            <FormItem>
                                <FormLabel>Título <span class="text-destructive">*</span></FormLabel>
                                <FormControl>
                                    <Input v-model="veeForm.values.titulo" placeholder="Título da atividade" />
                                </FormControl>
                                <FormMessage>{{ errorMessage }}</FormMessage>
                            </FormItem>
                        </FormField>

                        <!-- Descrição -->
                        <FormField v-slot="{ field, errorMessage }" name="descricao">
                            <FormItem>
                                <FormLabel>Descrição</FormLabel>
                                <FormControl>
                                    <Textarea v-model="veeForm.values.descricao" placeholder="Descrição da atividade" rows="4" />
                                </FormControl>
                                <FormMessage>{{ errorMessage }}</FormMessage>
                            </FormItem>
                        </FormField>

                        <!-- Data -->
                        <FormField v-slot="{ field, errorMessage }" name="data">
                            <FormItem>
                                <FormLabel>Data <span class="text-destructive">*</span></FormLabel>
                                <FormControl>
                                    <Input v-model="veeForm.values.data" type="date" />
                                </FormControl>
                                <FormMessage>{{ errorMessage }}</FormMessage>
                            </FormItem>
                        </FormField>

                        <!-- Tipo de Atividade -->
                        <FormField v-slot="{ field, errorMessage }" name="tipo_id">
                            <FormItem>
                                <FormLabel>Tipo de Atividade <span class="text-destructive">*</span></FormLabel>
                                <Select v-model="veeForm.values.tipo_id">
                                    <FormControl>
                                        <SelectTrigger>
                                            <SelectValue placeholder="Selecione um tipo" />
                                        </SelectTrigger>
                                    </FormControl>
                                    <SelectContent>
                                        <SelectItem v-for="tipo in tipos" :key="tipo.id" :value="tipo.id">
                                            {{ tipo.nome }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                <FormMessage>{{ errorMessage }}</FormMessage>
                            </FormItem>
                        </FormField>

                        <!-- Entidade -->
                        <FormField v-slot="{ field, errorMessage }" name="entidade_id">
                            <FormItem>
                                <FormLabel>Entidade</FormLabel>
                                <Select v-model="veeForm.values.entidade_id">
                                    <FormControl>
                                        <SelectTrigger>
                                            <SelectValue placeholder="Selecione uma entidade" />
                                        </SelectTrigger>
                                    </FormControl>
                                    <SelectContent>
                                        <SelectItem :value="0">Nenhuma</SelectItem>
                                        <SelectItem v-for="entidade in entidades" :key="entidade.id" :value="entidade.id">
                                            {{ entidade.nome }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                <FormMessage>{{ errorMessage }}</FormMessage>
                            </FormItem>
                        </FormField>

                        <!-- Contacto -->
                        <FormField v-slot="{ field, errorMessage }" name="contacto_id">
                            <FormItem>
                                <FormLabel>Contacto</FormLabel>
                                <Select v-model="veeForm.values.contacto_id" :disabled="!veeForm.values.entidade_id">
                                    <FormControl>
                                        <SelectTrigger>
                                            <SelectValue placeholder="Selecione um contacto" />
                                        </SelectTrigger>
                                    </FormControl>
                                    <SelectContent>
                                        <SelectItem :value="0">Nenhum</SelectItem>
                                        <SelectItem v-for="contacto in contactosFiltrados" :key="contacto.id" :value="contacto.id">
                                            {{ contacto.nome }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                <FormMessage>{{ errorMessage }}</FormMessage>
                            </FormItem>
                        </FormField>
                    </Form>
                </CardContent>
                <CardFooter class="flex justify-end gap-2">
                    <Button type="button" variant="outline" @click="$inertia.visit(route('atividades.index'))">
                        Cancelar
                    </Button>
                    <Button type="submit" @click="onSubmit" :disabled="form.processing">
                        Criar Atividade
                    </Button>
                </CardFooter>
            </Card>
        </div>
    </AppLayout>
</template> 