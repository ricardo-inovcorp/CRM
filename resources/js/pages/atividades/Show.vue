<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

interface Props {
  atividade: any;
}

defineProps<Props>();
</script>

<template>
  <Head :title="`Atividade: ${atividade.tipo?.nome || ''}`" />
  <AppLayout>
    <div class="p-6">
      <h1 class="text-2xl font-bold mb-4">Detalhes da Atividade</h1>
      <div class="bg-zinc-800 rounded-lg shadow-lg p-6 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <div class="mb-2"><span class="font-semibold">Data:</span> {{ atividade.data }}</div>
            <div class="mb-2"><span class="font-semibold">Hora:</span> {{ atividade.hora }}</div>
            <div class="mb-2"><span class="font-semibold">Duração:</span> {{ atividade.duracao ? atividade.duracao + ' min' : '—' }}</div>
            <div class="mb-2"><span class="font-semibold">Entidade:</span> <a v-if="atividade.entidade" :href="route('entidades.show', atividade.entidade.id)" style="all: unset; cursor: pointer; color: inherit; text-decoration: none;" @click.stop>{{ atividade.entidade.nome }}</a><span v-else>—</span></div>
            <div class="mb-2"><span class="font-semibold">Contacto:</span> <a v-if="atividade.contacto" :href="route('contactos.show', atividade.contacto.id)" style="all: unset; cursor: pointer; color: inherit; text-decoration: none;" @click.stop>{{ atividade.contacto.nome }} {{ atividade.contacto.apelido }}</a><span v-else>—</span></div>
            <div class="mb-2"><span class="font-semibold">Tipo:</span> {{ atividade.tipo?.nome || '—' }}</div>
          </div>
          <div>
            <div class="mb-2"><span class="font-semibold">Descrição:</span> {{ atividade.descricao || '—' }}</div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template> 