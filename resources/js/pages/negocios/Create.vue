<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { ref } from 'vue';
import { ArrowLeft, Save } from 'lucide-vue-next';

defineProps({
  tipos: Array,
  entidades: Array,
  contactos: Array,
  estados: Array,
});

const nome = ref('');
const tipo_id = ref('');
const entidade_id = ref('');
const valor = ref('');
const estado = ref('');
const contactosSelecionados = ref<string[]>([]);

const tipoDropdownOpen = ref(false);
const entidadeDropdownOpen = ref(false);
const contactosDropdownOpen = ref(false);
const contactoPesquisa = ref('');

function toggleTipoDropdown() {
  tipoDropdownOpen.value = !tipoDropdownOpen.value;
  if (tipoDropdownOpen.value) {
    entidadeDropdownOpen.value = false;
    contactosDropdownOpen.value = false;
  }
}
function toggleEntidadeDropdown() {
  entidadeDropdownOpen.value = !entidadeDropdownOpen.value;
  if (entidadeDropdownOpen.value) {
    tipoDropdownOpen.value = false;
    contactosDropdownOpen.value = false;
  }
}
function toggleContactosDropdown() {
  contactosDropdownOpen.value = !contactosDropdownOpen.value;
  if (contactosDropdownOpen.value) {
    tipoDropdownOpen.value = false;
    entidadeDropdownOpen.value = false;
  }
}

function selecionarTipo(id: string) {
  tipo_id.value = id;
  tipoDropdownOpen.value = false;
}
function selecionarEntidade(id: string) {
  entidade_id.value = id;
  entidadeDropdownOpen.value = false;
}
function selecionarContacto(id: string) {
  if (contactosSelecionados.value.includes(id)) {
    contactosSelecionados.value = contactosSelecionados.value.filter(cid => cid !== id);
  } else {
    contactosSelecionados.value.push(id);
  }
  contactosDropdownOpen.value = false;
}

function submitForm() {
  if (!nome.value || !tipo_id.value || !entidade_id.value || !estado.value || !valor.value) {
    alert('Por favor, preencha todos os campos obrigatórios');
    return;
  }
  const formData = {
    nome: nome.value,
    tipo_id: tipo_id.value,
    entidade_id: entidade_id.value,
    valor: valor.value,
    estado: estado.value,
    contactos: contactosSelecionados.value,
  };
  router.post(route('negocios.store'), formData);
}
</script>

<template>
  <Head title="Novo Negócio" />
  <AppLayout :breadcrumbs="[
    { label: 'Negócios', href: route('negocios.index') },
    { label: 'Novo Negócio', active: true }
  ]">
    <div class="p-6">
      <div class="mb-6 flex items-center">
        <Button variant="ghost" size="sm" :href="route('negocios.index')" as-child>
          <a class="flex items-center">
            <ArrowLeft class="mr-2 h-4 w-4" />
            Voltar
          </a>
        </Button>
        <h1 class="ml-4 text-2xl font-bold">Novo Negócio</h1>
      </div>
      <Card>
        <CardHeader>
          <CardTitle>Informações do Negócio</CardTitle>
          <CardDescription>
            Preencha os dados do novo negócio. Campos marcados com * são obrigatórios.
          </CardDescription>
        </CardHeader>
        <CardContent>
          <form @submit.prevent="submitForm" class="space-y-6">
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
              <!-- Nome -->
              <div class="space-y-2">
                <label class="text-sm font-medium">Nome <span class="text-red-500">*</span></label>
                <Input v-model="nome" placeholder="Nome do negócio" required />
              </div>
              <!-- Valor -->
              <div class="space-y-2">
                <label class="text-sm font-medium">Valor <span class="text-red-500">*</span></label>
                <Input v-model="valor" type="number" step="0.01" placeholder="Valor (€)" required />
              </div>
              <!-- Tipo -->
              <div class="space-y-2">
                <label class="text-sm font-medium">Tipo <span class="text-red-500">*</span></label>
                <div class="relative">
                  <button type="button" @click="toggleTipoDropdown" class="w-full flex items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background">
                    <span>{{ tipo_id ? tipos.find(t => String(t.id) === String(tipo_id))?.nome : 'Selecione um tipo' }}</span>
                    <span>▼</span>
                  </button>
                  <div v-if="tipoDropdownOpen" class="absolute left-0 top-full mt-1 w-full z-50 rounded-md border bg-popover p-1 shadow-md max-h-60 overflow-y-auto">
                    <div v-for="tipo in tipos" :key="tipo.id" @click="selecionarTipo(String(tipo.id))" class="cursor-pointer relative flex w-full select-none items-center rounded-sm py-1.5 px-2 text-sm outline-none hover:bg-accent hover:text-accent-foreground">
                      {{ tipo.nome }}
                    </div>
                  </div>
                </div>
              </div>
              <!-- Entidade -->
              <div class="space-y-2">
                <label class="text-sm font-medium">Entidade <span class="text-red-500">*</span></label>
                <div class="relative">
                  <button type="button" @click="toggleEntidadeDropdown" class="w-full flex items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background">
                    <span>{{ entidade_id ? entidades.find(e => String(e.id) === String(entidade_id))?.nome : 'Selecione uma entidade' }}</span>
                    <span>▼</span>
                  </button>
                  <div v-if="entidadeDropdownOpen" class="absolute left-0 top-full mt-1 w-full z-50 rounded-md border bg-popover p-1 shadow-md max-h-60 overflow-y-auto">
                    <div v-for="entidade in entidades" :key="entidade.id" @click="selecionarEntidade(String(entidade.id))" class="cursor-pointer relative flex w-full select-none items-center rounded-sm py-1.5 px-2 text-sm outline-none hover:bg-accent hover:text-accent-foreground">
                      {{ entidade.nome }}
                    </div>
                  </div>
                </div>
              </div>
              <!-- Estado -->
              <div class="space-y-2">
                <label class="text-sm font-medium">Estado <span class="text-red-500">*</span></label>
                <select v-model="estado" class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background">
                  <option value="">Selecione...</option>
                  <option v-for="e in estados" :key="e" :value="e">{{ e }}</option>
                </select>
              </div>
              <!-- Contactos -->
              <div class="space-y-2 md:col-span-2">
                <label class="text-sm font-medium">Contactos</label>
                <div class="relative">
                  <button type="button" @click="toggleContactosDropdown" class="w-full flex items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background">
                    <span>
                      <template v-if="!entidade_id">
                        Selecione uma entidade primeiro
                      </template>
                      <template v-else-if="contactosSelecionados.length">
                        {{ contactos.filter(c => contactosSelecionados.includes(String(c.id)) && String(c.entidade_id) === String(entidade_id)).map(c => c.nome + (c.apelido ? ' ' + c.apelido : '')).join(', ') }}
                      </template>
                      <template v-else>
                        Selecione contactos (opcional)
                      </template>
                    </span>
                    <span>▼</span>
                  </button>
                  <div v-if="contactosDropdownOpen && entidade_id" class="absolute left-0 top-full mt-1 w-full z-50 rounded-md border bg-popover p-1 shadow-md max-h-60 overflow-y-auto">
                    <div class="sticky top-0 bg-background p-1 border-b">
                      <Input v-model="contactoPesquisa" placeholder="Pesquisar contacto..." class="w-full text-sm" @click.stop />
                    </div>
                    <div v-for="contacto in contactos.filter(c => String(c.entidade_id) === String(entidade_id) && (contactoPesquisa ? (c.nome + ' ' + (c.apelido || '')).toLowerCase().includes(contactoPesquisa.toLowerCase()) : true))" :key="contacto.id" @click="selecionarContacto(String(contacto.id))" class="cursor-pointer relative flex w-full select-none items-center rounded-sm py-1.5 px-2 text-sm outline-none hover:bg-accent hover:text-accent-foreground" :class="{ 'bg-primary text-primary-foreground': contactosSelecionados.includes(String(contacto.id)) }">
                      {{ contacto.nome }} {{ contacto.apelido }}
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </form>
        </CardContent>
        <CardFooter class="flex justify-end gap-2">
          <Button type="button" variant="outline" @click="$inertia.visit(route('negocios.index'))">
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