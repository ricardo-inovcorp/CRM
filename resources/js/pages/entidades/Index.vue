<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip';
import { Head, Link, router } from '@inertiajs/vue3';
import { Eye, PenLine, Plus, Search, Trash2 } from 'lucide-vue-next';
import { Badge } from '@/components/ui/badge';
import { debounce } from 'lodash';
import { watch } from 'vue';

interface Props {
    entidades: {
        data: Array<{
            id: number;
            nome: string;
            email: string;
            telefone: string;
            website: string;
            estado: string;
            pais: {
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
    filters: {
        search: string;
        estado: string;
    };
}

const props = defineProps<Props>();

// Busca debounce
const search = (e: Event) => {
    const value = (e.target as HTMLInputElement).value;
    
    router.get(
        route('entidades.index'),
        { search: value, estado: props.filters.estado },
        { preserveState: true, replace: true }
    );
};

const debouncedSearch = debounce(search, 300);

watch(
    () => props.filters.search,
    (value) => {
        if (value === '') {
            router.get(
                route('entidades.index'),
                { estado: props.filters.estado },
                { preserveState: true, replace: true }
            );
        }
    }
);

// Filtro de estado
const filtroEstado = (valor: string) => {
    router.get(
        route('entidades.index'),
        { search: props.filters.search, estado: valor },
        { preserveState: true, replace: true }
    );
};
</script>

<template>
    <Head title="Entidades" />

    <AppLayout :breadcrumbs="[{ label: 'Entidades', active: true }]">
        <div class="p-6">
            <div class="mb-6 flex items-center justify-between">
                <h1 class="text-2xl font-bold">Entidades</h1>
                <Link :href="route('entidades.create')">
                    <Button>
                        <Plus class="mr-2 h-4 w-4" />
                        Nova Entidade
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
                        placeholder="Buscar entidades..."
                        class="pl-10"
                        :value="filters.search"
                        @input="debouncedSearch"
                    />
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
                            <TableHead>País</TableHead>
                            <TableHead>Telefone</TableHead>
                            <TableHead>Email</TableHead>
                            <TableHead>Estado</TableHead>
                            <TableHead class="w-32">Ações</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="entidade in entidades.data" :key="entidade.id">
                            <TableCell class="font-medium">{{ entidade.nome }}</TableCell>
                            <TableCell>{{ entidade.pais?.nome || '—' }}</TableCell>
                            <TableCell>{{ entidade.telefone || '—' }}</TableCell>
                            <TableCell>{{ entidade.email || '—' }}</TableCell>
                            <TableCell>
                                <Badge :variant="entidade.estado === 'Ativo' ? 'success' : 'destructive'">
                                    {{ entidade.estado }}
                                </Badge>
                            </TableCell>
                            <TableCell>
                                <div class="flex space-x-2">
                                    <TooltipProvider>
                                        <Tooltip>
                                            <TooltipTrigger asChild>
                                                <Link :href="route('entidades.show', entidade.id)">
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
                                                <Link :href="route('entidades.edit', entidade.id)">
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
                                                    :href="route('entidades.destroy', entidade.id)"
                                                    method="delete"
                                                    as="button"
                                                    @click.prevent="
                                                        if (confirm('Tem certeza que deseja excluir esta entidade?')) {
                                                            router.delete(route('entidades.destroy', entidade.id));
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
                        <TableRow v-if="entidades.data.length === 0">
                            <TableCell colspan="6" class="h-24 text-center">
                                Nenhuma entidade encontrada.
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>

            <!-- Paginação -->
            <div v-if="entidades.meta.last_page > 1" class="mt-6 flex items-center justify-between">
                <div class="text-sm text-muted-foreground">
                    Mostrando {{ (entidades.meta.current_page - 1) * entidades.meta.per_page + 1 }}
                    a {{ Math.min(entidades.meta.current_page * entidades.meta.per_page, entidades.meta.total) }}
                    de {{ entidades.meta.total }} resultados
                </div>

                <div class="flex gap-2">
                    <Link
                        v-if="entidades.meta.current_page > 1"
                        :href="`?page=${entidades.meta.current_page - 1}`"
                        preserve-scroll
                    >
                        <Button variant="outline" size="sm">Anterior</Button>
                    </Link>

                    <Link
                        v-if="entidades.meta.current_page < entidades.meta.last_page"
                        :href="`?page=${entidades.meta.current_page + 1}`"
                        preserve-scroll
                    >
                        <Button variant="outline" size="sm">Próxima</Button>
                    </Link>
                </div>
            </div>
        </div>
    </AppLayout>
</template> 