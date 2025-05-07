<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { computed } from 'vue';

interface Props {
  entidade: any;
}

const props = defineProps<Props>();

const paisNome = computed(() => props.entidade?.pais?.nome || '—');
</script>

<template>
  <Head :title="props.entidade?.nome ? `Entidade: ${props.entidade.nome}` : 'Entidade'" />
  <AppLayout>
    <div class="p-6">
      <template v-if="props.entidade && props.entidade.nome">
        <h1 class="text-2xl font-bold mb-4">Detalhes da Entidade</h1>
        <div class="bg-zinc-800 rounded-lg shadow-lg p-6 mb-8">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <div class="mb-2"><span class="font-semibold">Nome:</span> {{ props.entidade.nome }}</div>
              <div class="mb-2"><span class="font-semibold">Email:</span> {{ props.entidade.email || '—' }}</div>
              <div class="mb-2"><span class="font-semibold">Telefone:</span> {{ props.entidade.telefone || '—' }}</div>
              <div class="mb-2"><span class="font-semibold">Estado:</span> {{ props.entidade.estado }}</div>
              <div class="mb-2"><span class="font-semibold">País:</span> {{ paisNome }}</div>
              <div class="mb-2"><span class="font-semibold">Morada:</span> {{ props.entidade.morada || '—' }}</div>
              <div class="mb-2"><span class="font-semibold">Código Postal:</span> {{ props.entidade.codigo_postal || '—' }}</div>
              <div class="mb-2"><span class="font-semibold">Localidade:</span> {{ props.entidade.localidade || '—' }}</div>
              <div class="mb-2"><span class="font-semibold">Website:</span> <a v-if="props.entidade.website" :href="props.entidade.website" style="all: unset; color: inherit; text-decoration: none;" target="_blank">{{ props.entidade.website }}</a><span v-else>—</span></div>
            </div>
            <div>
              <div class="mb-2"><span class="font-semibold">Observações:</span> {{ props.entidade.observacoes || '—' }}</div>
            </div>
          </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
          <div>
            <h2 class="text-xl font-semibold mb-2">Contactos</h2>
            <div v-if="props.entidade.contactos && props.entidade.contactos.length">
              <ul class="divide-y divide-zinc-700">
                <li v-for="c in props.entidade.contactos" :key="c.id" class="py-2 flex justify-between items-center">
                  <span>{{ c.nome }} {{ c.apelido }}</span>
                  <a :href="route('contactos.show', c.id)" style="all: unset; cursor: pointer; color: inherit; text-decoration: none;" @click.stop>Ver detalhes</a>
                </li>
              </ul>
            </div>
            <div v-else class="text-gray-400">Nenhum contacto cadastrado.</div>
          </div>
          <div>
            <h2 class="text-xl font-semibold mb-2">Atividades</h2>
            <div v-if="props.entidade.atividades && props.entidade.atividades.length">
              <ul class="divide-y divide-zinc-700">
                <li v-for="a in props.entidade.atividades" :key="a.id" class="py-2 flex justify-between items-center">
                  <span>{{ a.data }} - {{ a.tipo?.nome || '—' }} - {{ a.descricao?.slice(0, 40) }}</span>
                  <a :href="route('atividades.show', a.id)" style="all: unset; cursor: pointer; color: inherit; text-decoration: none;" @click.stop>Ver detalhes</a>
                </li>
              </ul>
            </div>
            <div v-else class="text-gray-400">Nenhuma atividade cadastrada.</div>
          </div>
        </div>
      </template>
      <template v-else>
        <div class="text-center text-gray-400 text-xl py-20">Entidade não encontrada ou dados não carregados.</div>
      </template>
    </div>
  </AppLayout>
</template> 