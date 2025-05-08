<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { ref, computed, onMounted, reactive, watch } from 'vue';
import { Calendar } from 'lucide-vue-next';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter } from '@/components/ui/dialog';
import { format } from 'date-fns';
import { ptBR } from 'date-fns/locale';
import axios from 'axios';
import { router } from '@inertiajs/vue3';

// FullCalendar imports
import FullCalendar from '@fullcalendar/vue3';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import interactionPlugin from '@fullcalendar/interaction';

// Referência ao componente FullCalendar
const calendarRef = ref(null);

const props = defineProps<{
  atividades?: Array<{
    id: number;
    data: string;
    hora: string;
    duracao: number;
    descricao: string;
    entidade: {
      id: number;
      nome: string;
    } | null;
    tipo: {
      id: number;
      nome: string;
    } | null;
    contacto: {
      id: number;
      nome: string;
      apelido: string | null;
    } | null;
    entidade_id?: number;
    contacto_id?: number;
    tipo_id?: number;
  }>;
  entidades?: Array<{
    id: number;
    nome: string;
  }>;
  tipos?: Array<{
    id: number;
    nome: string;
  }>;
}>();

// Estado local para atividades
const localAtividades = ref(props.atividades || []);

// Observar mudanças no estado local de atividades
watch(localAtividades, () => {
  // Quando as atividades mudam, atualizar o calendário
  refreshCalendar();
}, { deep: true });

const calendarOptions = ref({
  plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
  initialView: 'dayGridMonth',
  headerToolbar: {
    left: 'prev,next today',
    center: 'title',
    right: 'dayGridMonth,timeGridWeek,timeGridDay'
  },
  locale: 'pt-br',
  buttonText: {
    today: 'Hoje',
    month: 'Mês',
    week: 'Semana',
    day: 'Dia',
    list: 'Lista'
  },
  firstDay: 1, // Segunda-feira como primeiro dia da semana
  timeZone: 'local',
  selectable: true,
  editable: false,
  eventTimeFormat: {
    hour: '2-digit',
    minute: '2-digit',
    meridiem: false,
    hour12: false
  },
  eventClick: handleEventClick,
  dateClick: handleDateClick,
  events: []
});

// Eventos do calendário
const events = computed(() => {
  if (!localAtividades.value || localAtividades.value.length === 0) return [];
  
  return localAtividades.value.map(atividade => {
    // Combinar data e hora
    const dataHora = `${atividade.data}T${atividade.hora}`;
    
    // Calcular hora de fim baseado na duração (minutos)
    const start = new Date(dataHora);
    const end = new Date(start.getTime() + (atividade.duracao * 60 * 1000));
    
    return {
      id: atividade.id,
      title: atividade.descricao,
      start: start,
      end: end,
      extendedProps: {
        tipoNome: atividade.tipo?.nome || 'Sem tipo',
        entidadeNome: atividade.entidade?.nome || 'Sem entidade',
        contactoNome: atividade.contacto ? 
          (atividade.contacto.apelido ? 
            `${atividade.contacto.nome} ${atividade.contacto.apelido}` : 
            atividade.contacto.nome) : 
          'Sem contacto'
      },
      backgroundColor: getEventColor(atividade.tipo?.nome || '')
    };
  });
});

// Dialog para detalhes da atividade
const detailsOpen = ref(false);
const selectedEvent = ref<any>(null);

// Edit modal related variables
const showEditModal = ref(false);
const atividadeEdit = ref<any>(null);
const saving = ref(false);
const feedback = ref('');
const editForm = ref({
  data: '',
  hora: '',
  duracao: '',
  entidade_id: '',
  contacto_id: '',
  tipo_id: '',
  descricao: ''
});

function handleEventClick(info: any) {
  selectedEvent.value = info.event;
  detailsOpen.value = true;
}

function handleDateClick(info: any) {
  // Pode ser usado para adicionar nova atividade
  console.log('Clicou na data:', info.dateStr);
  // Implementação futura: abrir modal para criar nova atividade
}

function getEventColor(tipoNome: string): string {
  // Mapa de cores baseado no tipo de atividade
  const colorMap: Record<string, string> = {
    'Reunião': '#4f46e5', // indigo
    'Ligação': '#3b82f6', // blue
    'Email': '#06b6d4',   // cyan
    'Visita': '#10b981',  // emerald
    'Tarefa': '#f59e0b',  // amber
    'Outro': '#6b7280',   // gray
  };
  
  return colorMap[tipoNome] || '#6b7280'; // gray é a cor padrão
}

// Atualizar calendário quando as atividades mudarem
onMounted(() => {
  // Inicializar o estado local das atividades
  localAtividades.value = props.atividades || [];
  
  if (!props.atividades) {
    // Se não recebemos atividades como props, buscar da API
    fetchAtividades();
  } else {
    // Atualizar eventos do calendário
    calendarOptions.value.events = events.value;
  }
});

// Função para atualizar manualmente o calendário
function refreshCalendar() {
  // Atualizar os eventos do calendário com base no estado local
  calendarOptions.value.events = events.value;
  
  // Se temos uma referência ao calendário, forçar uma atualização completa
  if (calendarRef.value) {
    const calendarApi = calendarRef.value.getApi();
    
    // Remover todos os eventos atuais
    calendarApi.removeAllEvents();
    
    // Adicionar os eventos atualizados
    calendarApi.addEventSource(events.value);
    
    // Forçar re-renderização
    calendarApi.refetchEvents();
    calendarApi.render();
  }
}

// Buscar atividades da API se não forem fornecidas como props
async function fetchAtividades() {
  try {
    const response = await axios.get(route('api.atividades.calendar'));
    // Atualizar o estado local das atividades
    localAtividades.value = response.data;
    // Atualizar eventos do calendário
    calendarOptions.value.events = events.value;
  } catch (error) {
    console.error('Erro ao buscar atividades:', error);
  }
}

// Formatar data e hora para exibição
function formatDateTime(date: Date): string {
  return format(date, "dd 'de' MMMM 'de' yyyy 'às' HH:mm", { locale: ptBR });
}

// Função para abrir o modal de edição
function openEditModal() {
  if (!selectedEvent.value) return;
  
  const atividade = props.atividades?.find(a => a.id.toString() === selectedEvent.value.id);
  if (!atividade) return;
  
  atividadeEdit.value = atividade;
  editForm.value = {
    data: atividade.data || '',
    hora: atividade.hora || '',
    duracao: atividade.duracao?.toString() || '',
    entidade_id: atividade.entidade_id?.toString() || '',
    contacto_id: atividade.contacto_id?.toString() || '',
    tipo_id: atividade.tipo_id?.toString() || '',
    descricao: atividade.descricao || ''
  };
  
  detailsOpen.value = false; // Fecha o modal de detalhes
  showEditModal.value = true; // Abre o modal de edição
}

function closeModals() {
  showEditModal.value = false;
  detailsOpen.value = false;
  atividadeEdit.value = null;
  selectedEvent.value = null;
  feedback.value = '';
}

function submitEdit() {
  if (!atividadeEdit.value) return;
  
  saving.value = true;
  router.put(route('atividades.update', atividadeEdit.value.id), editForm.value, {
    preserveScroll: true,
    onSuccess: () => {
      feedback.value = 'Atividade atualizada com sucesso!';
      saving.value = false;
      
      // Buscar os dados atualizados e atualizar o calendário
      axios.get(route('api.atividades.calendar'))
        .then(response => {
          // Atualizar o estado local com os dados atualizados
          localAtividades.value = response.data;
          
          // Forçar atualização do calendário
          refreshCalendar();
          
          // Fechar modal depois de atualizar
          setTimeout(() => {
            closeModals();
          }, 1000);
        })
        .catch(error => {
          console.error('Erro ao buscar atividades atualizadas:', error);
        });
    },
    onError: (errors) => {
      feedback.value = Object.values(errors).flat().join(', ') || 'Erro ao atualizar atividade.';
      saving.value = false;
    }
  });
}
</script>

<template>
  <Head title="Calendário" />

  <AppLayout>
    <div class="p-6">
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold flex items-center">
          <Calendar class="w-6 h-6 mr-2" />
          Calendário de Atividades
        </h1>
        <Link :href="route('atividades.create')">
          <Button class="inline-flex items-center gap-1">
            Nova Atividade
          </Button>
        </Link>
      </div>

      <!-- Calendário -->
      <Card>
        <CardHeader>
          <CardTitle>Agenda</CardTitle>
          <CardDescription>Todas as atividades agendadas no sistema</CardDescription>
        </CardHeader>
        <CardContent>
          <FullCalendar ref="calendarRef" :options="calendarOptions" />
        </CardContent>
      </Card>
    </div>

    <!-- Dialog para detalhes da atividade -->
    <Dialog v-model:open="detailsOpen">
      <DialogContent class="sm:max-w-lg">
        <DialogHeader>
          <DialogTitle>{{ selectedEvent?.title }}</DialogTitle>
        </DialogHeader>
        <div v-if="selectedEvent" class="space-y-4 mt-3">
          <div class="grid grid-cols-2 gap-4">
            <div>
              <p class="text-sm font-semibold text-gray-500">Data e hora:</p>
              <p>{{ formatDateTime(selectedEvent.start) }}</p>
            </div>
            <div>
              <p class="text-sm font-semibold text-gray-500">Duração:</p>
              <p>{{ Math.round((selectedEvent.end - selectedEvent.start) / (1000 * 60)) }} minutos</p>
            </div>
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <p class="text-sm font-semibold text-gray-500">Tipo:</p>
              <p>{{ selectedEvent.extendedProps.tipoNome }}</p>
            </div>
            <div>
              <p class="text-sm font-semibold text-gray-500">Entidade:</p>
              <p>{{ selectedEvent.extendedProps.entidadeNome }}</p>
            </div>
          </div>
          <div>
            <p class="text-sm font-semibold text-gray-500">Contacto:</p>
            <p>{{ selectedEvent.extendedProps.contactoNome }}</p>
          </div>
        </div>
        <DialogFooter>
          <Button 
            @click="openEditModal"
            class="inline-flex items-center gap-1"
            variant="outline"
          >
            Editar Atividade
          </Button>
          <Button @click="detailsOpen = false">Fechar</Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>

    <!-- Modal de Edição -->
    <Dialog v-model:open="showEditModal">
      <DialogContent class="max-w-md w-full">
        <DialogHeader>
          <DialogTitle>Editar Atividade</DialogTitle>
        </DialogHeader>
        <form @submit.prevent="submitEdit" class="space-y-4 mt-4">
          <div>
            <label class="block mb-1 text-sm font-medium text-foreground">Data</label>
            <input v-model="editForm.data" type="date" class="w-full border border-input bg-background rounded-lg px-3 py-2 text-foreground focus:outline-none focus:ring-2 focus:ring-primary dark:bg-zinc-800 dark:text-white" required />
          </div>
          <div>
            <label class="block mb-1 text-sm font-medium text-foreground">Hora</label>
            <input v-model="editForm.hora" type="time" class="w-full border border-input bg-background rounded-lg px-3 py-2 text-foreground focus:outline-none focus:ring-2 focus:ring-primary dark:bg-zinc-800 dark:text-white" required />
          </div>
          <div>
            <label class="block mb-1 text-sm font-medium text-foreground">Duração (minutos)</label>
            <input v-model="editForm.duracao" type="number" min="1" class="w-full border border-input bg-background rounded-lg px-3 py-2 text-foreground focus:outline-none focus:ring-2 focus:ring-primary dark:bg-zinc-800 dark:text-white" />
          </div>
          <div>
            <label class="block mb-1 text-sm font-medium text-foreground">Entidade</label>
            <select v-model="editForm.entidade_id" class="w-full border border-input bg-background rounded-lg px-3 py-2 text-foreground focus:outline-none focus:ring-2 focus:ring-primary dark:bg-zinc-800 dark:text-white">
              <option value="">Selecione uma entidade</option>
              <option v-for="entidade in props.entidades" :key="entidade.id" :value="entidade.id">
                {{ entidade.nome }}
              </option>
            </select>
          </div>
          <div>
            <label class="block mb-1 text-sm font-medium text-foreground">Contacto</label>
            <input v-model="editForm.contacto_id" type="text" class="w-full border border-input bg-background rounded-lg px-3 py-2 text-foreground focus:outline-none focus:ring-2 focus:ring-primary dark:bg-zinc-800 dark:text-white" />
          </div>
          <div>
            <label class="block mb-1 text-sm font-medium text-foreground">Tipo</label>
            <select v-model="editForm.tipo_id" class="w-full border border-input bg-background rounded-lg px-3 py-2 text-foreground focus:outline-none focus:ring-2 focus:ring-primary dark:bg-zinc-800 dark:text-white">
              <option value="">Selecione um tipo</option>
              <option v-for="tipo in props.tipos" :key="tipo.id" :value="tipo.id">
                {{ tipo.nome }}
              </option>
            </select>
          </div>
          <div>
            <label class="block mb-1 text-sm font-medium text-foreground">Descrição</label>
            <textarea v-model="editForm.descricao" class="w-full border border-input bg-background rounded-lg px-3 py-2 text-foreground focus:outline-none focus:ring-2 focus:ring-primary dark:bg-zinc-800 dark:text-white"></textarea>
          </div>
          <DialogFooter class="flex justify-end gap-2 mt-4">
            <button type="button" @click="closeModals" class="px-4 py-2 rounded bg-zinc-700 text-white hover:bg-zinc-600">Cancelar</button>
            <button type="submit" class="px-4 py-2 rounded bg-zinc-900 text-grey-900 hover:bg-zinc-800 font-semibold" :disabled="saving">
              <span v-if="saving">Salvando...</span>
              <span v-else class="text-white font-normal" >Salvar</span>
            </button>
          </DialogFooter>
          <div v-if="feedback" class="text-center mt-2" :class="feedback.includes('Erro') ? 'text-red-500' : 'text-green-500'">{{ feedback }}</div>
        </form>
      </DialogContent>
    </Dialog>
  </AppLayout>
</template>

<style scoped>
/* Estilos personalizados do calendário */
:deep(.fc) {
  /* Usar as cores do tema do site */
  --fc-border-color: #e5e7eb;
  --fc-button-bg-color: #f3f4f6;
  --fc-button-border-color: #d1d5db;
  --fc-button-text-color: #374151;
  --fc-button-hover-bg-color: #d1d5db;
  --fc-button-hover-border-color: #9ca3af;
  --fc-button-active-bg-color: #9ca3af;
  --fc-button-active-border-color: #6b7280;
  --fc-today-bg-color: #dbeafe; 
  --fc-event-border-color: transparent;
}

:deep(.fc-button) {
  font-weight: 500;
  text-transform: none;
  padding: 0.375rem 1rem;
  height: auto;
  box-shadow: none;
}

:deep(.fc-button-primary:focus) {
  box-shadow: none;
}

:deep(.fc-daygrid-event) {
  border-radius: 4px;
  padding: 2px 4px;
}

:deep(.fc-event-title) {
  font-size: 0.75rem;
  font-weight: 500;
}

/* Estilo específico para o dia atual com maior contraste */
:deep(.fc-day-today) {
  background-color: #5ee5da !important; 
  font-weight: bold;
}
</style> 