<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

interface Props {
  contacto: any;
}

defineProps<Props>();
</script>

<template>
  <Head :title="`Contacto: ${contacto.nome} ${contacto.apelido}`" />
  <AppLayout>
    <div class="p-6">
      <h1 class="text-2xl font-bold mb-4">Detalhes do Contacto</h1>
      <div class="bg-zinc-800 rounded-lg shadow-lg p-6 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <div class="mb-2"><span class="font-semibold">Nome:</span> {{ contacto.nome }} {{ contacto.apelido }}</div>
            <div class="mb-2"><span class="font-semibold">Email:</span> {{ contacto.email || '—' }}</div>
            <div class="mb-2"><span class="font-semibold">Telefone:</span> {{ contacto.telefone || '—' }}</div>
            <div class="mb-2"><span class="font-semibold">Telemóvel:</span> {{ contacto.telemovel || '—' }}</div>
            <div class="mb-2"><span class="font-semibold">Estado:</span> {{ contacto.estado }}</div>
            <div class="mb-2"><span class="font-semibold">Entidade:</span> <a v-if="contacto.entidade" :href="route('entidades.show', contacto.entidade.id)" style="all: unset; cursor: pointer; color: inherit; text-decoration: none;" @click.stop>{{ contacto.entidade.nome }}</a></div>
          </div>
          <div>
            <div class="mb-2"><span class="font-semibold">Observações:</span> {{ contacto.observacoes || '—' }}</div>
          </div>
        </div>
      </div>
      <div>
        <h2 class="text-xl font-semibold mb-2">Atividades</h2>
        <div v-if="contacto.atividades && contacto.atividades.length">
          <ul class="divide-y divide-zinc-700">
            <li v-for="a in contacto.atividades" :key="a.id" class="py-2 flex justify-between items-center">
              <span>{{ a.data }} - {{ a.tipo?.nome || '—' }} - {{ a.descricao?.slice(0, 40) }}</span>
              <a :href="route('atividades.show', a.id)" class="text-indigo-400 hover:underline text-sm">Ver detalhes</a>
            </li>
          </ul>
        </div>
        <div v-else class="text-gray-400">Nenhuma atividade cadastrada.</div>
      </div>
    </div>
  </AppLayout>
</template> 