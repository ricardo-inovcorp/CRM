<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { onMounted, ref } from 'vue';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { FileText, Download, BarChart2, Calendar, User2, Building2, Download as DownloadIcon } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { RadioGroup } from '@/components/ui/radio-group';
import { computed } from 'vue';

const props = defineProps<{
  estatisticas: {
    total_entidades: number;
    total_contactos: number;
    total_atividades: number;
    entidades_ativas: number;
    contactos_ativos: number;
    atividades_recentes: number;
  };
  tipos_relatorios: Array<{
    id: string;
    titulo: string;
    descricao: string;
  }>;
}>();

// Filtros para relatórios
const filtroEntidades = ref('todos');
const filtroContactos = ref({
  estado: 'todos',
  entidade_id: ''
});
const filtroAtividades = ref({
  data_inicio: new Date(new Date().setDate(new Date().getDate() - 30)).toISOString().split('T')[0],
  data_fim: new Date().toISOString().split('T')[0],
  entidade_id: '',
  tipo_id: ''
});
const filtroAtividadesPorEntidade = ref({
  data_inicio: new Date(new Date().setDate(new Date().getDate() - 30)).toISOString().split('T')[0],
  data_fim: new Date().toISOString().split('T')[0]
});

// Opções para os radio buttons
const estadoOptions = [
  { value: 'todos', label: 'Todos' },
  { value: 'Ativo', label: 'Ativos' },
  { value: 'Inativo', label: 'Inativos' }
];

// URLs para download dos relatórios
const urlRelatorioEntidades = computed(() => {
  return route('relatorios.entidades.pdf', { estado: filtroEntidades.value });
});

const urlRelatorioContactos = computed(() => {
  const params: Record<string, string> = { estado: filtroContactos.value.estado };
  if (filtroContactos.value.entidade_id) {
    params.entidade_id = filtroContactos.value.entidade_id;
  }
  return route('relatorios.contactos.pdf', params);
});

const urlRelatorioAtividades = computed(() => {
  const params: Record<string, string> = {
    data_inicio: filtroAtividades.value.data_inicio,
    data_fim: filtroAtividades.value.data_fim
  };
  if (filtroAtividades.value.entidade_id) {
    params.entidade_id = filtroAtividades.value.entidade_id;
  }
  if (filtroAtividades.value.tipo_id) {
    params.tipo_id = filtroAtividades.value.tipo_id;
  }
  return route('relatorios.atividades.pdf', params);
});

const urlRelatorioAtividadesPorEntidade = computed(() => {
  return route('relatorios.atividades-por-entidade.pdf', {
    data_inicio: filtroAtividadesPorEntidade.value.data_inicio,
    data_fim: filtroAtividadesPorEntidade.value.data_fim
  });
});

// Cálculo de percentuais para os cards
const percentEntidadesAtivas = computed(() => {
  return props.estatisticas.total_entidades > 0
    ? Math.round((props.estatisticas.entidades_ativas / props.estatisticas.total_entidades) * 100)
    : 0;
});

const percentContactosAtivos = computed(() => {
  return props.estatisticas.total_contactos > 0
    ? Math.round((props.estatisticas.contactos_ativos / props.estatisticas.total_contactos) * 100)
    : 0;
});

const percentAtividadesRecentes = computed(() => {
  return props.estatisticas.total_atividades > 0
    ? Math.round((props.estatisticas.atividades_recentes / props.estatisticas.total_atividades) * 100)
    : 0;
});
</script>

<template>
  <Head title="Relatórios" />

  <AppLayout>
    <div class="p-6">
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Relatórios</h1>
      </div>

      <!-- Dashboard de Estatísticas -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <Card>
          <CardHeader class="pb-2">
            <CardTitle class="text-lg font-medium flex items-center">
              <Building2 class="w-5 h-5 mr-2 text-primary" />
              Entidades
            </CardTitle>
          </CardHeader>
          <CardContent>
            <div class="text-3xl font-bold">{{ estatisticas.total_entidades }}</div>
            <div class="text-sm text-muted-foreground mt-1">
              {{ estatisticas.entidades_ativas }} ativas ({{ percentEntidadesAtivas }}%)
            </div>
          </CardContent>
        </Card>

        <Card>
          <CardHeader class="pb-2">
            <CardTitle class="text-lg font-medium flex items-center">
              <User2 class="w-5 h-5 mr-2 text-primary" />
              Contactos
            </CardTitle>
          </CardHeader>
          <CardContent>
            <div class="text-3xl font-bold">{{ estatisticas.total_contactos }}</div>
            <div class="text-sm text-muted-foreground mt-1">
              {{ estatisticas.contactos_ativos }} ativos ({{ percentContactosAtivos }}%)
            </div>
          </CardContent>
        </Card>

        <Card>
          <CardHeader class="pb-2">
            <CardTitle class="text-lg font-medium flex items-center">
              <Calendar class="w-5 h-5 mr-2 text-primary" />
              Atividades
            </CardTitle>
          </CardHeader>
          <CardContent>
            <div class="text-3xl font-bold">{{ estatisticas.total_atividades }}</div>
            <div class="text-sm text-muted-foreground mt-1">
              {{ estatisticas.atividades_recentes }} últimos 30 dias ({{ percentAtividadesRecentes }}%)
            </div>
          </CardContent>
        </Card>
      </div>

      <!-- Seção de Relatórios -->
      <div class="bg-card rounded-lg border shadow-sm">
        <div class="p-6">
          <h2 class="text-xl font-semibold mb-4 flex items-center">
            <FileText class="w-5 h-5 mr-2 text-primary" />
            Relatórios Disponíveis
          </h2>

          <Tabs default-value="entidades" class="w-full">
            <TabsList class="mb-4">
              <TabsTrigger value="entidades">Entidades</TabsTrigger>
              <TabsTrigger value="contactos">Contactos</TabsTrigger>
              <TabsTrigger value="atividades">Atividades</TabsTrigger>
              <TabsTrigger value="atividades_por_entidade">Atividades por Entidade</TabsTrigger>
            </TabsList>
            
            <!-- Relatório de Entidades -->
            <TabsContent value="entidades">
              <Card>
                <CardHeader>
                  <CardTitle>Relatório de Entidades</CardTitle>
                  <CardDescription>Lista detalhada de todas as entidades com seus dados principais e estatísticas.</CardDescription>
                </CardHeader>
                <CardContent>
                  <div class="grid gap-4 py-2">
                    <div class="grid grid-cols-4 items-center gap-4">
                      <Label class="text-right">Estado</Label>
                      <div class="col-span-3">
                        <RadioGroup 
                          v-model="filtroEntidades" 
                          name="estado-entidade"
                          :options="estadoOptions"
                        />
                      </div>
                    </div>
                  </div>
                </CardContent>
                <CardFooter class="flex justify-end">
                  <a :href="urlRelatorioEntidades" target="_blank" class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2">
                    <Download class="mr-2 h-4 w-4" />
                    Download PDF
                  </a>
                </CardFooter>
              </Card>
            </TabsContent>

            <!-- Relatório de Contactos -->
            <TabsContent value="contactos">
              <Card>
                <CardHeader>
                  <CardTitle>Relatório de Contactos</CardTitle>
                  <CardDescription>Lista detalhada de contactos com informações da entidade associada.</CardDescription>
                </CardHeader>
                <CardContent>
                  <div class="grid gap-4 py-2">
                    <div class="grid grid-cols-4 items-center gap-4">
                      <Label class="text-right">Estado</Label>
                      <div class="col-span-3">
                        <RadioGroup 
                          v-model="filtroContactos.estado" 
                          name="estado-contacto"
                          :options="estadoOptions"
                        />
                      </div>
                    </div>
                    <div class="grid grid-cols-4 items-center gap-4">
                      <Label htmlFor="entidade-contacto" class="text-right">ID da Entidade (opcional)</Label>
                      <Input
                        id="entidade-contacto"
                        v-model="filtroContactos.entidade_id"
                        placeholder="ID da entidade"
                        class="col-span-3"
                      />
                    </div>
                  </div>
                </CardContent>
                <CardFooter class="flex justify-end">
                  <a :href="urlRelatorioContactos" target="_blank" class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2">
                    <Download class="mr-2 h-4 w-4" />
                    Download PDF
                  </a>
                </CardFooter>
              </Card>
            </TabsContent>

            <!-- Relatório de Atividades -->
            <TabsContent value="atividades">
              <Card>
                <CardHeader>
                  <CardTitle>Relatório de Atividades</CardTitle>
                  <CardDescription>Histórico de todas as atividades realizadas em um determinado período.</CardDescription>
                </CardHeader>
                <CardContent>
                  <div class="grid gap-4 py-2">
                    <div class="grid grid-cols-4 items-center gap-4">
                      <Label htmlFor="data-inicio" class="text-right">Data Início</Label>
                      <Input
                        id="data-inicio"
                        v-model="filtroAtividades.data_inicio"
                        type="date"
                        class="col-span-3"
                      />
                    </div>
                    <div class="grid grid-cols-4 items-center gap-4">
                      <Label htmlFor="data-fim" class="text-right">Data Fim</Label>
                      <Input
                        id="data-fim"
                        v-model="filtroAtividades.data_fim"
                        type="date"
                        class="col-span-3"
                      />
                    </div>
                    <div class="grid grid-cols-4 items-center gap-4">
                      <Label htmlFor="entidade-atividade" class="text-right">ID da Entidade (opcional)</Label>
                      <Input
                        id="entidade-atividade"
                        v-model="filtroAtividades.entidade_id"
                        placeholder="ID da entidade"
                        class="col-span-3"
                      />
                    </div>
                    <div class="grid grid-cols-4 items-center gap-4">
                      <Label htmlFor="tipo-atividade" class="text-right">ID do Tipo (opcional)</Label>
                      <Input
                        id="tipo-atividade"
                        v-model="filtroAtividades.tipo_id"
                        placeholder="ID do tipo de atividade"
                        class="col-span-3"
                      />
                    </div>
                  </div>
                </CardContent>
                <CardFooter class="flex justify-end">
                  <a :href="urlRelatorioAtividades" target="_blank" class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2">
                    <Download class="mr-2 h-4 w-4" />
                    Download PDF
                  </a>
                </CardFooter>
              </Card>
            </TabsContent>

            <!-- Relatório de Atividades por Entidade -->
            <TabsContent value="atividades_por_entidade">
              <Card>
                <CardHeader>
                  <CardTitle>Relatório de Atividades por Entidade</CardTitle>
                  <CardDescription>Análise comparativa de atividades agrupadas por entidade.</CardDescription>
                </CardHeader>
                <CardContent>
                  <div class="grid gap-4 py-2">
                    <div class="grid grid-cols-4 items-center gap-4">
                      <Label htmlFor="data-inicio-entidade" class="text-right">Data Início</Label>
                      <Input
                        id="data-inicio-entidade"
                        v-model="filtroAtividadesPorEntidade.data_inicio"
                        type="date"
                        class="col-span-3"
                      />
                    </div>
                    <div class="grid grid-cols-4 items-center gap-4">
                      <Label htmlFor="data-fim-entidade" class="text-right">Data Fim</Label>
                      <Input
                        id="data-fim-entidade"
                        v-model="filtroAtividadesPorEntidade.data_fim"
                        type="date"
                        class="col-span-3"
                      />
                    </div>
                  </div>
                </CardContent>
                <CardFooter class="flex justify-end">
                  <a :href="urlRelatorioAtividadesPorEntidade" target="_blank" class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2">
                    <Download class="mr-2 h-4 w-4" />
                    Download PDF
                  </a>
                </CardFooter>
              </Card>
            </TabsContent>
          </Tabs>
        </div>
      </div>
    </div>
  </AppLayout>
</template> 