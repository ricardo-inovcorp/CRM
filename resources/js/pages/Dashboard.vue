<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, usePage, router } from '@inertiajs/vue3';
import { onMounted, ref, watch, onUnmounted } from 'vue';
import { 
  Building2, 
  Users, 
  Calendar, 
  Briefcase, 
  TrendingUp, 
  Clock, 
  AlertCircle,
  ChevronRight
} from 'lucide-vue-next';
import {
  Card,
  CardContent,
  CardDescription,
  CardFooter,
  CardHeader,
  CardTitle,
} from '@/components/ui/card';
import {
  Tabs,
  TabsContent,
  TabsList,
  TabsTrigger,
} from '@/components/ui/tabs';
import { Progress } from '@/components/ui/progress';
import { Button } from '@/components/ui/button';
import { Skeleton } from '@/components/ui/skeleton';

// Definição de interfaces para os dados
interface NegocioPorEstado {
  estado: string;
  count: number;
  color: string;
}

interface AtividadeRecente {
  id: number;
  descricao: string;
  data: string;
  tipo: string;
  entidade: string;
}

interface NegocioRecente {
  id: number;
  nome: string;
  estado: string;
  valor: number;
  entidade: string;
}

interface StatsData {
  totalEntidades: number;
  totalContactos: number;
  totalAtividades: number;
  totalNegocios: number;
  valorTotalNegocios: number;
  valorTotalNegociosGanhos: number;
  negociosPorEstado: NegocioPorEstado[];
  atividadesRecentes: AtividadeRecente[];
  negociosRecentes: NegocioRecente[];
}

// Obter dados iniciais da página
const page = usePage();
const initialStats = page.props.initialStats as any;

// Data states
const isLoading = ref(true);
const stats = ref<StatsData>({
  totalEntidades: 0,
  totalContactos: 0,
  totalAtividades: 0,
  totalNegocios: 0,
  negociosPorEstado: [],
  atividadesRecentes: [],
  negociosRecentes: [],
  valorTotalNegocios: 0,
  valorTotalNegociosGanhos: 0
});

// Chart and animation parameters
const chartProgress = ref(0);
const statsVisible = ref(false);
const lastUpdate = ref(new Date());

// Atualizar os dados do dashboard
const atualizarDashboard = () => {
  isLoading.value = true;
  statsVisible.value = false;
  chartProgress.value = 0;
  
  // Recarregar a página para obter dados atualizados
  router.reload({ 
    only: ['initialStats'],
    onSuccess: () => {
      // Após recarregar, iniciar a animação
      let progress = 0;
      const interval = setInterval(() => {
        progress += 10;
        chartProgress.value = progress;
        
        if (progress >= 100) {
          clearInterval(interval);
          processarDadosIniciais();
          isLoading.value = false;
          statsVisible.value = true;
          lastUpdate.value = new Date();
        }
      }, 30);
    }
  });
};

// Processar os dados iniciais
const processarDadosIniciais = () => {
  // Obter dados atualizados da página
  const currentInitialStats = usePage().props.initialStats as any;
  
  if (currentInitialStats) {
    // Processar dados de negócios por estado - apenas estados com negócios
    const negociosPorEstado = currentInitialStats.estadosNegocios.map((item: any) => ({
      estado: item.estado,
      count: item.count,
      color: getStateColor(item.estado)
    }));

    // Verificar consistência dos totais
    const totalNegosPorEstado = negociosPorEstado.reduce((sum: number, item: NegocioPorEstado) => sum + item.count, 0);
    if (totalNegosPorEstado !== currentInitialStats.totalNegocios) {
      console.warn(`Discrepância de totais: Total geral (${currentInitialStats.totalNegocios}) ≠ Soma por estados (${totalNegosPorEstado})`);
    }

    // Configurar os dados para o dashboard
    stats.value = {
      totalEntidades: currentInitialStats.totalEntidades,
      totalContactos: currentInitialStats.totalContactos,
      totalAtividades: currentInitialStats.totalAtividades,
      totalNegocios: currentInitialStats.totalNegocios,
      valorTotalNegocios: currentInitialStats.valorTotalNegocios,
      valorTotalNegociosGanhos: currentInitialStats.valorTotalNegociosGanhos,
      negociosPorEstado,
      atividadesRecentes: currentInitialStats.atividadesRecentes,
      negociosRecentes: currentInitialStats.negociosRecentes
    };
  }
};

// Atribuir cores aos estados de negócio
const getStateColor = (estado: string): string => {
  const colors: Record<string, string> = {
    'novo': 'bg-blue-500',
    'contactado': 'bg-indigo-500',
    'negociacao': 'bg-purple-500',
    'proposta': 'bg-yellow-500',
    'ganho': 'bg-green-500',
    'perdido': 'bg-red-500'
  };
  return colors[estado] || 'bg-gray-500';
};

// Monitorar eventos para atualizar o dashboard
watch(() => page.url, (newUrl, oldUrl) => {
  // Verificar se o usuário está voltando ao dashboard após editar um negócio
  if (newUrl.endsWith('/dashboard') && 
      (oldUrl.includes('/negocios') || oldUrl.includes('/kanban'))) {
    console.log('Voltando ao dashboard após edição de negócio, atualizando dados...');
    atualizarDashboard();
  }
}, { deep: true });

// Configuração inicial do componente
onMounted(() => {
  // Se o url atual é do dashboard, recarregar os dados
  if (window.location.pathname.endsWith('/dashboard')) {
    // Verificar se há um localStorage flag indicando que um negócio foi atualizado
    const negocioAtualizado = localStorage.getItem('negocio_atualizado');
    if (negocioAtualizado) {
      console.log('Detectado flag de negócio atualizado, recarregando dashboard');
      localStorage.removeItem('negocio_atualizado');
      atualizarDashboard();
      return;
    }
  }

  // Animar barra de progresso durante o carregamento
  let progress = 0;
  const interval = setInterval(() => {
    progress += 5;
    chartProgress.value = progress;
    
    // Quando a barra de progresso chegar a 100%, mostrar os dados
    if (progress >= 100) {
      clearInterval(interval);
      processarDadosIniciais();
      isLoading.value = false;
      statsVisible.value = true;
    }
  }, 50);

  // Configurar atualização automática a cada 5 minutos (opcional)
  const autoRefreshInterval = setInterval(() => {
    atualizarDashboard();
  }, 5 * 60 * 1000); // 5 minutos

  // Limpeza ao desmontar o componente
  onUnmounted(() => {
    clearInterval(autoRefreshInterval);
  });
});

const formatCurrency = (value: number): string => {
  return new Intl.NumberFormat('pt-PT', { style: 'currency', currency: 'EUR' }).format(value);
};

const getEstadoBadgeClass = (estado: string): string => {
  const classes: Record<string, string> = {
    'novo': 'bg-blue-100 text-blue-800',
    'contactado': 'bg-indigo-100 text-indigo-800',
    'negociacao': 'bg-purple-100 text-purple-800',
    'proposta': 'bg-yellow-100 text-yellow-800',
    'ganho': 'bg-green-100 text-green-800',
    'perdido': 'bg-red-100 text-red-800'
  };
  return classes[estado] || 'bg-gray-100 text-gray-800';
};
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout>
    <div class="p-6 space-y-6">
      <header class="flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-bold tracking-tight">Dashboard</h1>
          <p class="text-gray-500 dark:text-gray-400">Visão geral do seu CRM e métricas em tempo real</p>
        </div>
        <div>
          <div class="text-sm text-gray-500 dark:text-gray-400 flex items-center space-x-2">
            <span>Última atualização: {{ lastUpdate.toLocaleString('pt-PT') }}</span>
            <button 
              @click="atualizarDashboard" 
              class="ml-2 p-1 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors"
              title="Atualizar dashboard"
            >
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8"></path>
                <path d="M21 3v5h-5"></path>
                <path d="M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16"></path>
                <path d="M8 16H3v5"></path>
              </svg>
            </button>
          </div>
        </div>
      </header>

      <!-- Loading state -->
      <div v-if="isLoading" class="space-y-6">
        <div class="space-y-2">
          <Skeleton class="h-4 w-32" />
          <Progress :value="chartProgress" class="w-full" />
          <p class="text-sm text-gray-500 dark:text-gray-400 text-center">Carregando dados do dashboard...</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <Skeleton class="h-32 rounded-lg" v-for="i in 4" :key="i" />
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <Skeleton class="h-64 rounded-lg" v-for="i in 3" :key="i" />
        </div>
      </div>

      <!-- Dashboard Content -->
      <div v-else class="space-y-6">
        <!-- KPI Cards -->
        <div 
          class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6" 
          :class="{ 'animate-fade-in-up': statsVisible }"
        >
          <!-- Entidades Card -->
          <Card class="relative overflow-hidden transition-all hover:shadow-lg hover:-translate-y-1">
            <CardHeader class="pb-2">
              <CardTitle class="text-sm font-medium">Total de Entidades</CardTitle>
            </CardHeader>
            <CardContent>
              <div class="flex justify-between items-center">
                <div class="text-3xl font-bold">{{ stats.totalEntidades }}</div>
                <Building2 class="h-8 w-8 text-blue-500" />
              </div>
              <div class="absolute bottom-0 left-0 w-full h-1 bg-blue-500"></div>
            </CardContent>
          </Card>

          <!-- Contactos Card -->
          <Card class="relative overflow-hidden transition-all hover:shadow-lg hover:-translate-y-1">
            <CardHeader class="pb-2">
              <CardTitle class="text-sm font-medium">Total de Contactos</CardTitle>
            </CardHeader>
            <CardContent>
              <div class="flex justify-between items-center">
                <div class="text-3xl font-bold">{{ stats.totalContactos }}</div>
                <Users class="h-8 w-8 text-indigo-500" />
              </div>
              <div class="absolute bottom-0 left-0 w-full h-1 bg-indigo-500"></div>
            </CardContent>
          </Card>

          <!-- Atividades Card -->
          <Card class="relative overflow-hidden transition-all hover:shadow-lg hover:-translate-y-1">
            <CardHeader class="pb-2">
              <CardTitle class="text-sm font-medium">Total de Atividades</CardTitle>
            </CardHeader>
            <CardContent>
              <div class="flex justify-between items-center">
                <div class="text-3xl font-bold">{{ stats.totalAtividades }}</div>
                <Calendar class="h-8 w-8 text-purple-500" />
              </div>
              <div class="absolute bottom-0 left-0 w-full h-1 bg-purple-500"></div>
            </CardContent>
          </Card>

          <!-- Negócios Card -->
          <Card class="relative overflow-hidden transition-all hover:shadow-lg hover:-translate-y-1">
            <CardHeader class="pb-2">
              <CardTitle class="text-sm font-medium">Total de Negócios</CardTitle>
            </CardHeader>
            <CardContent>
              <div class="flex justify-between items-center">
                <div class="text-3xl font-bold">{{ stats.totalNegocios }}</div>
                <Briefcase class="h-8 w-8 text-green-500" />
              </div>
              <div class="absolute bottom-0 left-0 w-full h-1 bg-green-500"></div>
            </CardContent>
          </Card>
        </div>

        <!-- Middle Section -->
        <div 
          class="grid grid-cols-1 lg:grid-cols-3 gap-6" 
          :class="{ 'animate-fade-in-up-delayed': statsVisible }"
        >
          <!-- Valor Total de Negócios -->
          <Card class="lg:col-span-1 transition-all hover:shadow-lg">
            <CardHeader>
              <CardTitle>Valor Total de Negócios</CardTitle>
            </CardHeader>
            <CardContent class="space-y-6">
              <div class="flex flex-col items-center space-y-2">
                <TrendingUp class="h-12 w-12 text-green-500" />
                <div class="text-3xl font-bold">{{ formatCurrency(stats.valorTotalNegocios) }}</div>
                
                <div class="w-full mt-4">
                  <div class="flex justify-between text-sm mb-1">
                    <span>Ganhos: {{ formatCurrency(stats.valorTotalNegociosGanhos) }}</span>
                    <span>{{ Math.round((stats.valorTotalNegociosGanhos / stats.valorTotalNegocios) * 100) }}%</span>
                  </div>
                  <Progress :value="(stats.valorTotalNegociosGanhos / stats.valorTotalNegocios) * 100" class="w-full" />
                </div>
              </div>
            </CardContent>
          </Card>

          <!-- Estado dos Negócios -->
          <Card class="lg:col-span-2 transition-all hover:shadow-lg">
            <CardHeader>
              <CardTitle>Estado dos Negócios</CardTitle>
            </CardHeader>
            <CardContent>
              <div class="space-y-4">
                <div v-for="item in stats.negociosPorEstado" :key="item.estado" class="space-y-1">
                  <div class="flex items-center justify-between">
                    <div class="capitalize font-medium">{{ item.estado }}</div>
                    <div>{{ item.count }} ({{ Math.round((item.count / stats.totalNegocios) * 100) }}%)</div>
                  </div>
                  <div class="w-full bg-gray-200 h-2 rounded-full overflow-hidden">
                    <div 
                      :class="item.color" 
                      class="h-full rounded-full animate-expand-width" 
                      :style="`width: ${(item.count / stats.totalNegocios) * 100}%`"
                    ></div>
                  </div>
                </div>
              </div>
            </CardContent>
          </Card>
        </div>

        <!-- Bottom Tabs Section -->
        <Tabs defaultValue="atividades" class="animate-fade-in-up-more-delayed">
          <TabsList class="grid w-full grid-cols-2">
            <TabsTrigger value="atividades">Atividades Recentes</TabsTrigger>
            <TabsTrigger value="negocios">Negócios Recentes</TabsTrigger>
          </TabsList>
          
          <!-- Atividades Recentes Tab -->
          <TabsContent value="atividades">
            <Card>
              <CardHeader>
                <CardTitle>Atividades Recentes</CardTitle>
                <CardDescription>Acompanhe suas atividades mais recentes</CardDescription>
              </CardHeader>
              <CardContent>
                <div class="space-y-4">
                  <div v-for="atividade in stats.atividadesRecentes" :key="atividade.id" 
                       class="p-4 border rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition cursor-pointer flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                      <Clock class="h-5 w-5 text-purple-500" />
                      <div>
                        <div class="font-medium">{{ atividade.descricao }}</div>
                        <div class="text-sm text-gray-500">{{ atividade.entidade }} • {{ atividade.tipo }}</div>
                      </div>
                    </div>
                    <div class="flex items-center">
                      <div class="text-sm text-gray-500">{{ atividade.data }}</div>
                      <ChevronRight class="h-5 w-5 ml-2" />
                    </div>
                  </div>
                </div>
              </CardContent>
              <CardFooter>
                <Button variant="outline" class="w-full">Ver Todas as Atividades</Button>
              </CardFooter>
            </Card>
          </TabsContent>
          
          <!-- Negócios Recentes Tab -->
          <TabsContent value="negocios">
            <Card>
              <CardHeader>
                <CardTitle>Negócios Recentes</CardTitle>
                <CardDescription>Acompanhe seus negócios mais recentes</CardDescription>
              </CardHeader>
              <CardContent>
                <div class="space-y-4">
                  <div v-for="negocio in stats.negociosRecentes" :key="negocio.id" 
                       class="p-4 border rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition cursor-pointer flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                      <Briefcase class="h-5 w-5 text-green-500" />
                      <div>
                        <div class="font-medium">{{ negocio.nome }}</div>
                        <div class="text-sm text-gray-500">{{ negocio.entidade }}</div>
                      </div>
                    </div>
                    <div class="flex flex-col items-end">
                      <div class="font-medium">{{ formatCurrency(negocio.valor) }}</div>
                      <span class="text-xs px-2 py-0.5 rounded-full mt-1" :class="getEstadoBadgeClass(negocio.estado)">
                        {{ negocio.estado }}
                      </span>
                    </div>
                  </div>
                </div>
              </CardContent>
              <CardFooter>
                <Button variant="outline" class="w-full">Ver Todos os Negócios</Button>
              </CardFooter>
            </Card>
          </TabsContent>
        </Tabs>
            </div>
        </div>
    </AppLayout>
</template> 

<style scoped>
.animate-fade-in-up {
  animation: fadeInUp 0.5s ease-out forwards;
}

.animate-fade-in-up-delayed {
  animation: fadeInUp 0.5s ease-out 0.2s forwards;
  opacity: 0;
}

.animate-fade-in-up-more-delayed {
  animation: fadeInUp 0.5s ease-out 0.4s forwards;
  opacity: 0;
}

.animate-expand-width {
  animation: expandWidth 1s ease-out forwards;
}

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes expandWidth {
  from {
    width: 0;
  }
}
</style> 