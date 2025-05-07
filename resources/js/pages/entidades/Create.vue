<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { FormField, FormItem, FormLabel, FormControl, FormMessage } from '@/components/ui/form';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { ref } from 'vue';
import { ArrowLeft, Save } from 'lucide-vue-next';

interface Props {
    paises: Array<{
        id: number;
        nome: string;
    }>;
}

const props = defineProps<Props>();

// Debug para verificar os países recebidos
console.log('Países recebidos:', props.paises);

// Dados do formulário
const nome = ref('');
const morada = ref('');
const codigo_postal = ref('');
const localidade = ref('');
const pais_id = ref(0);
const telefone = ref('');
const email = ref('');
const website = ref('');
const observacoes = ref('');
const estado = ref('Ativo');

// Estado para controlar a exibição dos dropdowns
const paisDropdownOpen = ref(false);

// Funções para toggle das dropdowns
function togglePaisDropdown() {
    paisDropdownOpen.value = !paisDropdownOpen.value;
}

// Funções para selecionar valores
function selecionarPais(id: number, nome: string) {
    pais_id.value = id;
    paisDropdownOpen.value = false;
}

// Função para enviar o formulário
function submitForm() {
    // Validações básicas
    if (!nome.value) {
        alert('Por favor, preencha o nome da entidade');
        return;
    }
    
    console.log('Submitting form...');
    
    // Preparar os dados para envio
    const formData = {
        nome: nome.value,
        morada: morada.value,
        codigo_postal: codigo_postal.value,
        localidade: localidade.value,
        pais_id: pais_id.value || null,
        telefone: telefone.value,
        email: email.value,
        // Certificar-se de que o website inclui http:// ou https://
        website: website.value ? (website.value.startsWith('http://') || website.value.startsWith('https://') ? website.value : `https://${website.value}`) : '',
        observacoes: observacoes.value,
        estado: estado.value,
    };

    console.log('Form data:', formData);
    console.log('Posting to route:', route('entidades.store'));

    try {
        // Usar router.post diretamente com callbacks para sucesso e erro
        router.post(route('entidades.store'), formData, {
            onSuccess: () => {
                console.log('Form submitted successfully');
            },
            onError: (errors) => {
                console.error('Form submission errors:', errors);
                
                // Mostrar erros específicos em vez de mensagem genérica
                if (errors && Object.keys(errors).length > 0) {
                    let errorMessages = [];
                    for (const field in errors) {
                        errorMessages.push(`${field}: ${errors[field].join(', ')}`);
                    }
                    alert('Erros de validação:\n' + errorMessages.join('\n'));
                } else {
                    alert('Ocorreu um erro ao salvar a entidade. Por favor, verifique os campos.');
                }
            }
        });
    } catch (error) {
        console.error('Error submitting form:', error);
        alert('Ocorreu um erro ao processar o formulário.');
    }
}
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
                    <form @submit.prevent="submitForm" class="space-y-6">
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <!-- Nome -->
                            <div class="space-y-2">
                                <label class="text-sm font-medium">Nome <span class="text-red-500">*</span></label>
                                <Input v-model="nome" placeholder="Nome da entidade" required />
                            </div>

                            <!-- País -->
                            <div class="space-y-2">
                                <label class="text-sm font-medium">País</label>
                                <div class="relative">
                                    <button 
                                        type="button" 
                                        @click="togglePaisDropdown" 
                                        class="w-full flex items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background"
                                    >
                                        <span>{{ pais_id ? props.paises.find(p => p.id === pais_id)?.nome : 'Selecione um país' }}</span>
                                        <span>▼</span>
                                    </button>
                                    <div 
                                        v-if="paisDropdownOpen" 
                                        class="absolute left-0 top-full mt-1 w-full z-50 rounded-md border bg-popover p-1 shadow-md max-h-60 overflow-y-auto"
                                    >
                                        <div 
                                            @click="pais_id = 0; paisDropdownOpen = false"
                                            class="cursor-pointer relative flex w-full select-none items-center rounded-sm py-1.5 px-2 text-sm outline-none hover:bg-accent hover:text-accent-foreground"
                                        >
                                            Nenhum
                                        </div>
                                        <div v-if="props.paises && props.paises.length">
                                            <div 
                                                v-for="pais in props.paises" 
                                                :key="pais.id" 
                                                @click="selecionarPais(pais.id, pais.nome)"
                                                class="cursor-pointer relative flex w-full select-none items-center rounded-sm py-1.5 px-2 text-sm outline-none hover:bg-accent hover:text-accent-foreground"
                                            >
                                                {{ pais.nome }}
                                            </div>
                                        </div>
                                        <div v-else class="py-1.5 px-2 text-sm text-gray-500 italic">
                                            Nenhum país disponível
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Morada -->
                            <div class="space-y-2">
                                <label class="text-sm font-medium">Morada</label>
                                <Input v-model="morada" placeholder="Morada" />
                            </div>

                            <!-- Localidade -->
                            <div class="space-y-2">
                                <label class="text-sm font-medium">Localidade</label>
                                <Input v-model="localidade" placeholder="Localidade" />
                            </div>

                            <!-- Código Postal -->
                            <div class="space-y-2">
                                <label class="text-sm font-medium">Código Postal</label>
                                <Input v-model="codigo_postal" placeholder="Código Postal" />
                            </div>

                            <!-- Telefone -->
                            <div class="space-y-2">
                                <label class="text-sm font-medium">Telefone</label>
                                <Input v-model="telefone" placeholder="Telefone" />
                            </div>

                            <!-- Email -->
                            <div class="space-y-2">
                                <label class="text-sm font-medium">Email</label>
                                <Input v-model="email" type="email" placeholder="Email" />
                            </div>

                            <!-- Website -->
                            <div class="space-y-2">
                                <label class="text-sm font-medium">Website</label>
                                <Input v-model="website" placeholder="Website (ex: www.example.com)" />
                                <p class="text-xs text-gray-500 mt-1">Inclua http:// ou https:// se não for adicionado automaticamente</p>
                            </div>

                            <!-- Estado -->
                            <div class="space-y-2">
                                <label class="text-sm font-medium">Estado</label>
                                <div class="flex items-center space-x-4">
                                    <label class="flex items-center space-x-2">
                                        <input type="radio" v-model="estado" value="Ativo" class="rounded border-gray-300 text-primary focus:ring-primary" />
                                        <span>Ativo</span>
                                    </label>
                                    <label class="flex items-center space-x-2">
                                        <input type="radio" v-model="estado" value="Inativo" class="rounded border-gray-300 text-primary focus:ring-primary" />
                                        <span>Inativo</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Observações -->
                        <div class="space-y-2">
                            <label class="text-sm font-medium">Observações</label>
                            <Textarea v-model="observacoes" placeholder="Observações" rows="4" />
                        </div>
                    </form>
                </CardContent>
                <CardFooter class="flex justify-end gap-2">
                    <Button type="button" variant="outline" @click="$inertia.visit(route('entidades.index'))">
                        Cancelar
                    </Button>
                    <Button type="button" @click="submitForm">
                        <Save class="mr-2 h-4 w-4" />
                        Salvar
                    </Button>
                </CardFooter>
            </Card>
        </div>
    </AppLayout>
</template> 