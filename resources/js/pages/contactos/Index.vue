<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip';
import { Head, Link, router } from '@inertiajs/vue3';
import { Eye, PenLine, Plus, Search, Trash2 } from 'lucide-vue-next';
import { Badge } from '@/components/ui/badge';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { debounce } from 'lodash';
import { watch } from 'vue';

interface Props {
    contactos: {
        data: Array<{
            id: number;
            nome: string;
            apelido: string;
            email: string;
            telefone: string;
            telemovel: string;
            estado: string;
            entidade: {
                id: number;
                nome: string;
            };
            funcao: {
                id: number;
                nome: string;
            } | null;
        }>;
        meta: {
            current_page: number;
            last_page: number;
            per_page: number;
            total: number;
        };
    };
    entidades: Array<{
        id: number;
        nome: string;
    }>;
    filters: {
        search: string;
        estado: string;
        entidade_id: string;
    };
}

const props = defineProps<Props>();

// Busca debounce
const search = (e: Event) => {
    const value = (e.target as HTMLInputElement).value;
    
    router.get(
        route('contactos.index'),
        { 
            search: value, 
            estado: props.filters.estado,
            entidade_id: props.filters.entidade_id
        },
        { preserveState: true, replace: true }
    );
};

const debouncedSearch = debounce(search, 300);

watch(
    () => props.filters.search,
    (value) => {
        if (value === '') {
            router.get(
                route('contactos.index'),
                { 
                    estado: props.filters.estado,
                    entidade_id: props.filters.entidade_id
                },
                { preserveState: true, replace: true }
            );
        }
    }
);

// Filtro de estado
const filtroEstado = (valor: string) => {
    router.get(
        route('contactos.index'),
        { 
            search: props.filters.search, 
            estado: valor,
            entidade_id: props.filters.entidade_id
        },
        { preserveState: true, replace: true }
    );
};

// Filtro de entidade
const filtroEntidade = (valor: string) => {
    router.get(
        route('contactos.index'),
        { 
            search: props.filters.search, 
            estado: props.filters.estado,
            entidade_id: valor
        },
        { preserveState: true, replace: true }
    );
};

const nomeCompleto = (contacto: any) => {
    if (contacto.apelido) {
        return `${contacto.nome} ${contacto.apelido}`;
    }
    return contacto.nome;
};
</script>

<template>
    <Head title="Contactos" />

    <AppLayout :breadcrumbs="[{ label: 'Contactos', active: true }]">
        <div class="p-6">
            <div class="mb-6 flex items-center justify-between">
                <h1 class="text-2xl font-bold">Contactos</h1>
                <Link :href="route('contactos.create')">
                    <Button>
                        <Plus class="mr-2 h-4 w-4" />
                        Novo Contacto
                    </Button>
                </Link>
            </div>

            <div class="mb-6 flex flex-wrap gap-4">
                <!-- Filtro de busca -->
                <div class="relative flex-grow">
                    <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                    <Input
                        id="search"
                        type="search"
                        placeholder="Buscar contactos..."
                        class="pl-10"
                        :value="filters.search"
                        @input="debouncedSearch"
                    />
                </div>

                <!-- Filtro de entidade -->
                <div>
                    <Select v-model="filters.entidade_id" @update:modelValue="filtroEntidade">
                        <SelectTrigger class="w-[200px]">
                            <SelectValue placeholder="Todas as entidades" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="">Todas as entidades</SelectItem>
                            <SelectItem
                                v-for="entidade in entidades"
                                :key="entidade.id"
                                :value="entidade.id.toString()"
                            >
                                {{ entidade.nome }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                </div>

                <!-- Filtro de estado -->
                <div class="flex flex-wrap gap-2">
                    <Button
                        :variant="filters.estado === '' ? 'default' : 'outline'"
                        @click="filtroEstado('')"
                    >
                        Todos
                    </Button>
                    <Button
                        :variant="filters.estado === 'Ativo' ? 'default' : 'outline'"
                        @click="filtroEstado('Ativo')"
                    >
                        Ativos
                    </Button>
                    <Button
                        :variant="filters.estado === 'Inativo' ? 'default' : 'outline'"
                        @click="filtroEstado('Inativo')"
                    >
                        Inativos
                    </Button>
                </div>
            </div>

            <div class="rounded-md border">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Nome</TableHead>
                            <TableHead>Entidade</TableHead>
                            <TableHead>Função</TableHead>
                            <TableHead>Telefone/Telemóvel</TableHead>
                            <TableHead>Email</TableHead>
                            <TableHead>Estado</TableHead>
                            <TableHead class="w-32">Ações</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="contacto in contactos.data" :key="contacto.id">
                            <TableCell class="font-medium">{{ nomeCompleto(contacto) }}</TableCell>
                            <TableCell>{{ contacto.entidade.nome }}</TableCell>
                            <TableCell>{{ contacto.funcao?.nome || '—' }}</TableCell>
                            <TableCell>
                                <div v-if="contacto.telefone" class="text-sm">Tel: {{ contacto.telefone }}</div>
                                <div v-if="contacto.telemovel" class="text-sm">Telm: {{ contacto.telemovel }}</div>
                                <span v-if="!contacto.telefone && !contacto.telemovel">—</span>
                            </TableCell>
                            <TableCell>{{ contacto.email || '—' }}</TableCell>
                            <TableCell>
                                <Badge :variant="contacto.estado === 'Ativo' ? 'success' : 'destructive'">
                                    {{ contacto.estado }}
                                </Badge>
                            </TableCell>
                            <TableCell>
                                <div class="flex space-x-2">
                                    <TooltipProvider>
                                        <Tooltip>
                                            <TooltipTrigger asChild>
                                                <Link :href="route('contactos.show', contacto.id)">
                                                    <Button variant="outline" size="icon">
                                                        <Eye class="h-4 w-4" />
                                                    </Button>
                                                </Link>
                                            </TooltipTrigger>
                                            <TooltipContent>
                                                <p>Visualizar</p>
                                            </TooltipContent>
                                        </Tooltip>
                                    </TooltipProvider>

                                    <TooltipProvider>
                                        <Tooltip>
                                            <TooltipTrigger asChild>
                                                <Link :href="route('contactos.edit', contacto.id)">
                                                    <Button variant="outline" size="icon">
                                                        <PenLine class="h-4 w-4" />
                                                    </Button>
                                                </Link>
                                            </TooltipTrigger>
                                            <TooltipContent>
                                                <p>Editar</p>
                                            </TooltipContent>
                                        </Tooltip>
                                    </TooltipProvider>

                                    <TooltipProvider>
                                        <Tooltip>
                                            <TooltipTrigger asChild>
                                                <Link
                                                    :href="route('contactos.destroy', contacto.id)"
                                                    method="delete"
                                                    as="button"
                                                    @click.prevent="
                                                        if (confirm('Tem certeza que deseja excluir este contacto?')) {
                                                            router.delete(route('contactos.destroy', contacto.id));
                                                        }
                                                    "
                                                >
                                                    <Button variant="outline" size="icon">
                                                        <Trash2 class="h-4 w-4" />
                                                    </Button>
                                                </Link>
                                            </TooltipTrigger>
                                            <TooltipContent>
                                                <p>Excluir</p>
                                            </TooltipContent>
                                        </Tooltip>
                                    </TooltipProvider>
                                </div>
                            </TableCell>
                        </TableRow>
                        <TableRow v-if="contactos.data.length === 0">
                            <TableCell colspan="7" class="h-24 text-center">
                                Nenhum contacto encontrado.
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>

            <!-- Paginação -->
            <div v-if="contactos.meta.last_page > 1" class="mt-6 flex items-center justify-between">
                <div class="text-sm text-muted-foreground">
                    Mostrando {{ (contactos.meta.current_page - 1) * contactos.meta.per_page + 1 }}
                    a {{ Math.min(contactos.meta.current_page * contactos.meta.per_page, contactos.meta.total) }}
                    de {{ contactos.meta.total }} resultados
                </div>

                <div class="flex gap-2">
                    <Link
                        v-if="contactos.meta.current_page > 1"
                        :href="`?page=${contactos.meta.current_page - 1}`"
                        preserve-scroll
                    >
                        <Button variant="outline" size="sm">Anterior</Button>
                    </Link>

                    <Link
                        v-if="contactos.meta.current_page < contactos.meta.last_page"
                        :href="`?page=${contactos.meta.current_page + 1}`"
                        preserve-scroll
                    >
                        <Button variant="outline" size="sm">Próxima</Button>
                    </Link>
                </div>
            </div>
        </div>
    </AppLayout>
</template> 