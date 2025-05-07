<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { FormField, FormItem, FormLabel, FormControl, FormMessage } from '@/components/ui/form';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { ref, computed } from 'vue';
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

// Dados do formulário
const nome = ref('');
const apelido = ref('');
const entidade_id = ref(0);
const funcao_id = ref(0);
const telefone = ref('');
const telemovel = ref('');
const email = ref('');
const observacoes = ref('');
const estado = ref('Ativo');

// Campo de pesquisa para funções
const funcaoPesquisa = ref('');

// Funções filtradas com base na pesquisa
const funcoesFiltradas = computed(() => {
    if (!funcaoPesquisa.value || !props.funcoes) return props.funcoes;
    
    const pesquisa = funcaoPesquisa.value.toLowerCase();
    return props.funcoes.filter(f => f.nome.toLowerCase().includes(pesquisa));
});

// Estado para controlar a exibição dos dropdowns
const entidadeDropdownOpen = ref(false);
const funcaoDropdownOpen = ref(false);

// Funções para toggle das dropdowns
function toggleEntidadeDropdown() {
    entidadeDropdownOpen.value = !entidadeDropdownOpen.value;
    if (entidadeDropdownOpen.value) {
        funcaoDropdownOpen.value = false;
    }
}

function toggleFuncaoDropdown() {
    funcaoDropdownOpen.value = !funcaoDropdownOpen.value;
    if (funcaoDropdownOpen.value) {
        entidadeDropdownOpen.value = false;
    }
}

// Funções para selecionar valores
function selecionarEntidade(id: number, nome: string) {
    entidade_id.value = id;
    entidadeDropdownOpen.value = false;
}

function selecionarFuncao(id: number, nome: string) {
    funcao_id.value = id;
    funcaoDropdownOpen.value = false;
}

// Função para enviar o formulário
function submitForm() {
    // Validações básicas
    if (!nome.value || !entidade_id.value) {
        alert('Por favor, preencha todos os campos obrigatórios');
        return;
    }
    
    // Preparar os dados para envio
    const formData = {
        nome: nome.value,
        apelido: apelido.value,
        entidade_id: entidade_id.value,
        funcao_id: funcao_id.value || null,
        telefone: telefone.value,
        telemovel: telemovel.value,
        email: email.value,
        observacoes: observacoes.value,
        estado: estado.value,
    };

    // Usar router.post diretamente
    router.post(route('contactos.store'), formData);
}
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
                    <form @submit.prevent="submitForm" class="space-y-6">
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <!-- Nome -->
                            <div class="space-y-2">
                                <label class="text-sm font-medium">Nome <span class="text-red-500">*</span></label>
                                <Input v-model="nome" placeholder="Nome do contacto" required />
                            </div>

                            <!-- Apelido -->
                            <div class="space-y-2">
                                <label class="text-sm font-medium">Apelido</label>
                                <Input v-model="apelido" placeholder="Apelido do contacto" />
                            </div>

                            <!-- Entidade -->
                            <div class="space-y-2">
                                <label class="text-sm font-medium">Entidade <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <button 
                                        type="button" 
                                        @click="toggleEntidadeDropdown" 
                                        class="w-full flex items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background"
                                    >
                                        <span>{{ entidade_id ? props.entidades.find(e => e.id === entidade_id)?.nome : 'Selecione uma entidade' }}</span>
                                        <span>▼</span>
                                    </button>
                                    <div 
                                        v-if="entidadeDropdownOpen" 
                                        class="absolute left-0 top-full mt-1 w-full z-50 rounded-md border bg-popover p-1 shadow-md"
                                    >
                                        <div 
                                            v-for="entidade in props.entidades" 
                                            :key="entidade.id" 
                                            @click="selecionarEntidade(entidade.id, entidade.nome)"
                                            class="cursor-pointer relative flex w-full select-none items-center rounded-sm py-1.5 px-2 text-sm outline-none hover:bg-accent hover:text-accent-foreground"
                                        >
                                            {{ entidade.nome }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Função -->
                            <div class="space-y-2">
                                <label class="text-sm font-medium">Função</label>
                                <div class="relative">
                                    <button 
                                        type="button" 
                                        @click="toggleFuncaoDropdown" 
                                        class="w-full flex items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background"
                                    >
                                        <span>{{ funcao_id ? props.funcoes.find(f => f.id === funcao_id)?.nome : 'Selecione uma função' }}</span>
                                        <span>▼</span>
                                    </button>
                                    <div 
                                        v-if="funcaoDropdownOpen" 
                                        class="absolute left-0 top-full mt-1 w-full z-50 rounded-md border bg-popover p-1 shadow-md max-h-60 overflow-y-auto"
                                    >
                                        <!-- Pesquisa -->
                                        <div class="sticky top-0 bg-background p-1 border-b">
                                            <Input 
                                                v-model="funcaoPesquisa" 
                                                placeholder="Pesquisar função..." 
                                                class="w-full text-sm" 
                                                @click.stop 
                                            />
                                        </div>
                                        
                                        <div 
                                            @click="funcao_id = 0; funcaoDropdownOpen = false"
                                            class="cursor-pointer relative flex w-full select-none items-center rounded-sm py-1.5 px-2 text-sm outline-none hover:bg-accent hover:text-accent-foreground"
                                        >
                                            Nenhuma
                                        </div>
                                        
                                        <div v-if="funcaoPesquisa">
                                            <!-- Resultados da pesquisa -->
                                            <div class="pt-2 pl-2 pb-1 text-xs font-semibold text-gray-500">Resultados da Pesquisa</div>
                                            <div 
                                                v-for="funcao in funcoesFiltradas" 
                                                :key="funcao.id" 
                                                @click="selecionarFuncao(funcao.id, funcao.nome)"
                                                class="cursor-pointer relative flex w-full select-none items-center rounded-sm py-1.5 px-2 text-sm outline-none hover:bg-accent hover:text-accent-foreground"
                                            >
                                                {{ funcao.nome }}
                                            </div>
                                        </div>
                                        
                                        <div v-else>
                                            <!-- Funções de direção -->
                                            <div class="pt-2 pl-2 pb-1 text-xs font-semibold text-gray-500">Direção</div>
                                            <div 
                                                v-for="funcao in props.funcoes.filter(f => ['CEO', 'Diretor Geral', 'Diretor Executivo', 'Diretor Financeiro', 'Diretor de Marketing', 'Diretor Comercial', 'Diretor de Operações', 'Diretor de Tecnologia', 'Diretor de Recursos Humanos'].includes(f.nome))" 
                                                :key="funcao.id" 
                                                @click="selecionarFuncao(funcao.id, funcao.nome)"
                                                class="cursor-pointer relative flex w-full select-none items-center rounded-sm py-1.5 px-2 text-sm outline-none hover:bg-accent hover:text-accent-foreground"
                                            >
                                                {{ funcao.nome }}
                                            </div>
                                            
                                            <!-- Funções de gestão -->
                                            <div class="pt-2 pl-2 pb-1 text-xs font-semibold text-gray-500">Gestão</div>
                                            <div 
                                                v-for="funcao in props.funcoes.filter(f => ['Gerente', 'Gerente de Projeto', 'Gerente de Produto', 'Gerente de Vendas', 'Gerente de Marketing', 'Gerente Administrativo', 'Gerente Financeiro', 'Gerente de RH', 'Coordenador', 'Supervisor'].includes(f.nome))" 
                                                :key="funcao.id" 
                                                @click="selecionarFuncao(funcao.id, funcao.nome)"
                                                class="cursor-pointer relative flex w-full select-none items-center rounded-sm py-1.5 px-2 text-sm outline-none hover:bg-accent hover:text-accent-foreground"
                                            >
                                                {{ funcao.nome }}
                                            </div>
                                            
                                            <!-- Funções comerciais -->
                                            <div class="pt-2 pl-2 pb-1 text-xs font-semibold text-gray-500">Comercial</div>
                                            <div 
                                                v-for="funcao in props.funcoes.filter(f => ['Vendedor', 'Representante Comercial', 'Executivo de Contas', 'Consultor de Vendas', 'Assistente Comercial'].includes(f.nome))" 
                                                :key="funcao.id" 
                                                @click="selecionarFuncao(funcao.id, funcao.nome)"
                                                class="cursor-pointer relative flex w-full select-none items-center rounded-sm py-1.5 px-2 text-sm outline-none hover:bg-accent hover:text-accent-foreground"
                                            >
                                                {{ funcao.nome }}
                                            </div>
                                            
                                            <!-- Outras funções -->
                                            <div class="pt-2 pl-2 pb-1 text-xs font-semibold text-gray-500">Outras Funções</div>
                                            <div 
                                                v-for="funcao in props.funcoes.filter(f => 
                                                    !['CEO', 'Diretor Geral', 'Diretor Executivo', 'Diretor Financeiro', 'Diretor de Marketing', 'Diretor Comercial', 'Diretor de Operações', 'Diretor de Tecnologia', 'Diretor de Recursos Humanos',
                                                    'Gerente', 'Gerente de Projeto', 'Gerente de Produto', 'Gerente de Vendas', 'Gerente de Marketing', 'Gerente Administrativo', 'Gerente Financeiro', 'Gerente de RH', 'Coordenador', 'Supervisor',
                                                    'Vendedor', 'Representante Comercial', 'Executivo de Contas', 'Consultor de Vendas', 'Assistente Comercial'].includes(f.nome))" 
                                                :key="funcao.id" 
                                                @click="selecionarFuncao(funcao.id, funcao.nome)"
                                                class="cursor-pointer relative flex w-full select-none items-center rounded-sm py-1.5 px-2 text-sm outline-none hover:bg-accent hover:text-accent-foreground"
                                            >
                                                {{ funcao.nome }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Telefone -->
                            <div class="space-y-2">
                                <label class="text-sm font-medium">Telefone</label>
                                <Input v-model="telefone" placeholder="Telefone" />
                            </div>

                            <!-- Telemóvel -->
                            <div class="space-y-2">
                                <label class="text-sm font-medium">Telemóvel</label>
                                <Input v-model="telemovel" placeholder="Telemóvel" />
                            </div>

                            <!-- Email -->
                            <div class="space-y-2">
                                <label class="text-sm font-medium">Email</label>
                                <Input v-model="email" type="email" placeholder="Email" />
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

                        <div class="flex justify-end space-x-2">
                            <Button variant="outline" :href="route('contactos.index')" as-child>
                                <a>Cancelar</a>
                            </Button>
                            <Button type="submit" variant="default">
                                <Save class="mr-2 h-4 w-4" />
                                Salvar
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template> 