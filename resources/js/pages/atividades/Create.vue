<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { FormField, FormItem, FormLabel, FormControl, FormMessage } from '@/components/ui/form';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { ref, computed, watch } from 'vue';
import axios from 'axios';

interface Props {
    entidades: Array<{
        id: number;
        nome: string;
    }>;
    tipos: Array<{
        id: number;
        nome: string;
    }>;
    users: Array<{
        id: number;
        name: string;
    }>;
    currentUser: {
        id: number;
        name: string;
    };
}

interface Contacto {
    id: number;
    nome: string;
    apelido: string | null;
}

const props = defineProps<Props>();

// Dados do formulário
const descricao = ref('');
const data = ref(new Date().toISOString().split('T')[0]);
const hora = ref('09:00');
const duracao = ref(60);
const tipo_id = ref(0);
const entidade_id = ref(0);
const contacto_id = ref(0);
const contacto_nome = ref('');

// Lista de contactos da entidade selecionada
const contactos = ref<Contacto[]>([]);
const carregandoContactos = ref(false);

// Pesquisa de contactos
const contactoPesquisa = ref('');
const contactosFiltrados = computed(() => {
    console.log('Calculando contactosFiltrados...');
    console.log('contactos.value.length:', contactos.value ? contactos.value.length : 0);
    
    if (!contactoPesquisa.value || !contactos.value || contactos.value.length === 0) {
        console.log('Retornando todos os contactos sem filtro');
        return contactos.value || [];
    }
    
    const pesquisa = contactoPesquisa.value.toLowerCase();
    console.log('Filtrando por:', pesquisa);
    
    return contactos.value.filter(c => {
        const matchNome = c.nome.toLowerCase().includes(pesquisa);
        const matchApelido = c.apelido && c.apelido.toLowerCase().includes(pesquisa);
        return matchNome || matchApelido;
    });
});

// Campo de pesquisa para tipos
const tipoPesquisa = ref('');

// Tipos filtrados com base na pesquisa
const tiposFiltrados = computed(() => {
    if (!tipoPesquisa.value || !props.tipos) return props.tipos;
    
    const pesquisa = tipoPesquisa.value.toLowerCase();
    return props.tipos.filter(t => t.nome.toLowerCase().includes(pesquisa));
});

// Estado para controlar a exibição dos dropdowns
const tipoDropdownOpen = ref(false);
const entidadeDropdownOpen = ref(false);
const contactoDropdownOpen = ref(false);

// Funções para toggle das dropdowns
function toggleTipoDropdown() {
    tipoDropdownOpen.value = !tipoDropdownOpen.value;
    // Fechar outros dropdowns quando abrir este
    if (tipoDropdownOpen.value) {
        entidadeDropdownOpen.value = false;
        contactoDropdownOpen.value = false;
    }
}

function toggleEntidadeDropdown() {
    entidadeDropdownOpen.value = !entidadeDropdownOpen.value;
    // Fechar outros dropdowns quando abrir este
    if (entidadeDropdownOpen.value) {
        tipoDropdownOpen.value = false;
        contactoDropdownOpen.value = false;
    }
}

function toggleContactoDropdown() {
    contactoDropdownOpen.value = !contactoDropdownOpen.value;
    // Fechar outros dropdowns quando abrir este
    if (contactoDropdownOpen.value) {
        tipoDropdownOpen.value = false;
        entidadeDropdownOpen.value = false;
    }
}

// Funções para selecionar valores
function selecionarTipo(id: number, nome: string) {
    tipo_id.value = id;
    tipoDropdownOpen.value = false;
}

function selecionarEntidade(id: number, nome: string) {
    entidade_id.value = id;
    entidadeDropdownOpen.value = false;
    contacto_id.value = 0; // Resetar contacto selecionado
    
    // Buscar contactos desta entidade
    buscarContactos();
}

function selecionarContacto(id: number, nome: string, apelido: string | null) {
    contacto_id.value = id;
    contacto_nome.value = apelido ? `${nome} ${apelido}` : nome;
    contactoDropdownOpen.value = false;
}

// Função para buscar contactos da entidade selecionada
async function buscarContactos() {
    if (!entidade_id.value) {
        contactos.value = [];
        return;
    }
    
    console.log('Buscando contactos para entidade ID:', entidade_id.value);
    carregandoContactos.value = true;
    
    try {
        const url = route('atividades.getContacts');
        console.log('URL de busca:', url);
        
        const response = await axios.get(url, {
            params: { entidade_id: entidade_id.value }
        });
        
        console.log('Contactos recebidos:', response.data);
        contactos.value = response.data;
        
        if (response.data.length === 0) {
            console.log('Nenhum contacto encontrado para esta entidade');
        }
    } catch (error) {
        console.error('Erro ao buscar contactos:', error);
        if (error.response) {
            console.error('Resposta de erro:', error.response.data);
            console.error('Status do erro:', error.response.status);
        }
        contactos.value = [];
    } finally {
        carregandoContactos.value = false;
    }
}

// Função para enviar o formulário
function submitForm() {
    // Validações básicas
    if (!descricao.value || !data.value || !hora.value || !tipo_id.value || !entidade_id.value) {
        alert('Por favor, preencha todos os campos obrigatórios');
        return;
    }
    
    // Garantir que a data está no formato correto YYYY-MM-DD
    const formattedDate = data.value.split('T')[0];
    
    // Preparar os dados para envio
    const formData = {
        descricao: descricao.value,
        data: formattedDate,
        hora: hora.value,
        duracao: duracao.value || 0,
        tipo_id: tipo_id.value,
        entidade_id: entidade_id.value,
        contacto_id: contacto_id.value || null,
        contacto_nome: contacto_nome.value || '',
        participantes: [props.currentUser?.id],
    };

    // Usar router.post diretamente
    router.post(route('atividades.store'), formData);
}
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
                    <div class="space-y-6">
                        <!-- Descrição -->
                        <div class="space-y-2">
                            <label class="text-sm font-medium">Descrição <span class="text-red-500">*</span></label>
                            <Textarea v-model="descricao" placeholder="Descrição da atividade" rows="4" class="w-full" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Data -->
                            <div class="space-y-2">
                                <label class="text-sm font-medium">Data <span class="text-red-500">*</span></label>
                                <Input v-model="data" type="date" class="w-full" />
                            </div>

                            <!-- Hora -->
                            <div class="space-y-2">
                                <label class="text-sm font-medium">Hora <span class="text-red-500">*</span></label>
                                <Input v-model="hora" type="time" class="w-full" />
                            </div>

                            <!-- Duração -->
                            <div class="space-y-2">
                                <label class="text-sm font-medium">Duração (minutos)</label>
                                <Input v-model="duracao" type="number" min="0" step="15" class="w-full" />
                            </div>
                        </div>

                        <!-- Tipo de Atividade -->
                        <div class="space-y-2">
                            <label class="text-sm font-medium">Tipo de Atividade <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <button 
                                    type="button" 
                                    @click="toggleTipoDropdown" 
                                    class="w-full flex items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background"
                                >
                                    <span>{{ tipo_id ? props.tipos.find(t => t.id === tipo_id)?.nome : 'Selecione um tipo' }}</span>
                                    <span>▼</span>
                                </button>
                                <div 
                                    v-if="tipoDropdownOpen" 
                                    class="absolute left-0 top-full mt-1 w-full z-50 rounded-md border bg-popover p-1 shadow-md max-h-60 overflow-y-auto"
                                >
                                    <!-- Pesquisa -->
                                    <div class="sticky top-0 bg-background p-1 border-b">
                                        <Input 
                                            v-model="tipoPesquisa" 
                                            placeholder="Pesquisar tipo..." 
                                            class="w-full text-sm" 
                                            @click.stop 
                                        />
                                    </div>
                                    
                                    <div v-if="tipoPesquisa">
                                        <!-- Resultados da pesquisa -->
                                        <div class="pt-2 pl-2 pb-1 text-xs font-semibold text-gray-500">Resultados da Pesquisa</div>
                                        <div 
                                            v-for="tipo in tiposFiltrados" 
                                            :key="tipo.id" 
                                            @click="selecionarTipo(tipo.id, tipo.nome)"
                                            class="cursor-pointer relative flex w-full select-none items-center rounded-sm py-1.5 px-2 text-sm outline-none hover:bg-accent hover:text-accent-foreground"
                                        >
                                            {{ tipo.nome }}
                                        </div>
                                    </div>
                                    
                                    <div v-else>
                                        <!-- Reuniões -->
                                        <div class="pt-2 pl-2 pb-1 text-xs font-semibold text-gray-500">Reuniões</div>
                                        <div 
                                            v-for="tipo in props.tipos.filter(t => ['Reunião Presencial', 'Videochamada', 'Conferência Telefónica'].includes(t.nome))" 
                                            :key="tipo.id" 
                                            @click="selecionarTipo(tipo.id, tipo.nome)"
                                            class="cursor-pointer relative flex w-full select-none items-center rounded-sm py-1.5 px-2 text-sm outline-none hover:bg-accent hover:text-accent-foreground"
                                        >
                                            {{ tipo.nome }}
                                        </div>
                                        
                                        <!-- Comunicações -->
                                        <div class="pt-2 pl-2 pb-1 text-xs font-semibold text-gray-500">Comunicações</div>
                                        <div 
                                            v-for="tipo in props.tipos.filter(t => ['Chamada Telefónica', 'Email', 'Mensagem'].includes(t.nome))" 
                                            :key="tipo.id" 
                                            @click="selecionarTipo(tipo.id, tipo.nome)"
                                            class="cursor-pointer relative flex w-full select-none items-center rounded-sm py-1.5 px-2 text-sm outline-none hover:bg-accent hover:text-accent-foreground"
                                        >
                                            {{ tipo.nome }}
                                        </div>
                                        
                                        <!-- Vendas -->
                                        <div class="pt-2 pl-2 pb-1 text-xs font-semibold text-gray-500">Vendas</div>
                                        <div 
                                            v-for="tipo in props.tipos.filter(t => ['Apresentação Comercial', 'Demonstração de Produto', 'Negociação', 'Proposta Comercial', 'Assinatura de Contrato'].includes(t.nome))" 
                                            :key="tipo.id" 
                                            @click="selecionarTipo(tipo.id, tipo.nome)"
                                            class="cursor-pointer relative flex w-full select-none items-center rounded-sm py-1.5 px-2 text-sm outline-none hover:bg-accent hover:text-accent-foreground"
                                        >
                                            {{ tipo.nome }}
                                        </div>
                                        
                                        <!-- Suporte -->
                                        <div class="pt-2 pl-2 pb-1 text-xs font-semibold text-gray-500">Suporte</div>
                                        <div 
                                            v-for="tipo in props.tipos.filter(t => ['Suporte Técnico', 'Resolução de Problemas', 'Atendimento ao Cliente'].includes(t.nome))" 
                                            :key="tipo.id" 
                                            @click="selecionarTipo(tipo.id, tipo.nome)"
                                            class="cursor-pointer relative flex w-full select-none items-center rounded-sm py-1.5 px-2 text-sm outline-none hover:bg-accent hover:text-accent-foreground"
                                        >
                                            {{ tipo.nome }}
                                        </div>
                                        
                                        <!-- Follow-up -->
                                        <div class="pt-2 pl-2 pb-1 text-xs font-semibold text-gray-500">Follow-up</div>
                                        <div 
                                            v-for="tipo in props.tipos.filter(t => ['Follow-up de Venda', 'Follow-up de Evento', 'Follow-up Pós-Venda'].includes(t.nome))" 
                                            :key="tipo.id" 
                                            @click="selecionarTipo(tipo.id, tipo.nome)"
                                            class="cursor-pointer relative flex w-full select-none items-center rounded-sm py-1.5 px-2 text-sm outline-none hover:bg-accent hover:text-accent-foreground"
                                        >
                                            {{ tipo.nome }}
                                        </div>
                                        
                                        <!-- Eventos -->
                                        <div class="pt-2 pl-2 pb-1 text-xs font-semibold text-gray-500">Eventos</div>
                                        <div 
                                            v-for="tipo in props.tipos.filter(t => ['Evento', 'Webinar', 'Workshop', 'Formação', 'Conferência'].includes(t.nome))" 
                                            :key="tipo.id" 
                                            @click="selecionarTipo(tipo.id, tipo.nome)"
                                            class="cursor-pointer relative flex w-full select-none items-center rounded-sm py-1.5 px-2 text-sm outline-none hover:bg-accent hover:text-accent-foreground"
                                        >
                                            {{ tipo.nome }}
                                        </div>
                                        
                                        <!-- Outros -->
                                        <div class="pt-2 pl-2 pb-1 text-xs font-semibold text-gray-500">Outros</div>
                                        <div 
                                            v-for="tipo in props.tipos.filter(t => ['Almoço de Negócios', 'Networking', 'Outro'].includes(t.nome))" 
                                            :key="tipo.id" 
                                            @click="selecionarTipo(tipo.id, tipo.nome)"
                                            class="cursor-pointer relative flex w-full select-none items-center rounded-sm py-1.5 px-2 text-sm outline-none hover:bg-accent hover:text-accent-foreground"
                                        >
                                            {{ tipo.nome }}
                                        </div>
                                    </div>
                                </div>
                            </div>
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

                        <!-- Contacto -->
                        <div class="space-y-2">
                            <label class="text-sm font-medium">Contacto</label>
                            <div class="relative">
                                <button 
                                    type="button" 
                                    @click="toggleContactoDropdown" 
                                    :disabled="!entidade_id"
                                    class="w-full flex items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background"
                                    :class="{ 'opacity-50 cursor-not-allowed': !entidade_id }"
                                >
                                    <span v-if="!entidade_id">Selecione uma entidade primeiro</span>
                                    <span v-else-if="contacto_id">{{ contactos.find(c => c.id === contacto_id)?.nome }} {{ contactos.find(c => c.id === contacto_id)?.apelido }}</span>
                                    <span v-else>Selecione um contacto</span>
                                    <span>▼</span>
                                </button>
                                <div 
                                    v-if="contactoDropdownOpen && entidade_id" 
                                    class="absolute left-0 top-full mt-1 w-full z-50 rounded-md border bg-popover p-1 shadow-md max-h-60 overflow-y-auto"
                                >
                                    <!-- Pesquisa -->
                                    <div class="sticky top-0 bg-background p-1 border-b">
                                        <Input 
                                            v-model="contactoPesquisa" 
                                            placeholder="Pesquisar contacto..." 
                                            class="w-full text-sm" 
                                            @click.stop 
                                        />
                                    </div>
                                    
                                    <!-- Loading state -->
                                    <div v-if="carregandoContactos" class="py-4 text-center text-sm text-gray-500">
                                        Carregando contactos...
                                    </div>
                                    
                                    <!-- No contacts found -->
                                    <div v-else-if="contactos.length === 0" class="py-4 text-center text-sm text-gray-500">
                                        Nenhum contacto encontrado para esta entidade
                                    </div>
                                    
                                    <!-- Empty search results -->
                                    <div v-else-if="contactoPesquisa && contactosFiltrados.length === 0" class="py-4 text-center text-sm text-gray-500">
                                        Nenhum contacto encontrado para "{{ contactoPesquisa }}"
                                    </div>
                                    
                                    <!-- Contacts list -->
                                    <div v-else>
                                        <div 
                                            v-for="contacto in contactosFiltrados" 
                                            :key="contacto.id" 
                                            @click="selecionarContacto(contacto.id, contacto.nome, contacto.apelido)"
                                            class="cursor-pointer relative flex w-full select-none items-center rounded-sm py-1.5 px-2 text-sm outline-none hover:bg-accent hover:text-accent-foreground"
                                        >
                                            {{ contacto.nome }} {{ contacto.apelido }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </CardContent>
                <CardFooter class="flex justify-end gap-2">
                    <Button type="button" variant="outline" @click="$inertia.visit(route('atividades.index'))">
                        Cancelar
                    </Button>
                    <Button type="button" @click="submitForm">
                        Criar Atividade
                    </Button>
                </CardFooter>
            </Card>
        </div>
    </AppLayout>
</template> 