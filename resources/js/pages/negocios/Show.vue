<script setup lang="ts">
import { Link, Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

defineProps({
  negocio: Object,
});
</script>

<template>
  <Head :title="`Negócio: ${negocio.nome}`" />
  <AppLayout>
    <div class="p-6">
      <h1 class="text-2xl font-bold mb-4">Detalhes do Negócio</h1>
      <div class="bg-zinc-800 rounded-lg shadow-lg p-6 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <div class="mb-2"><span class="font-semibold">Nome:</span> {{ negocio.nome }}</div>
            <div class="mb-2"><span class="font-semibold">Tipo:</span> {{ negocio.tipo?.nome || '—' }}</div>
            <div class="mb-2"><span class="font-semibold">Entidade:</span> {{ negocio.entidade?.nome || '—' }}</div>
            <div class="mb-2"><span class="font-semibold">Valor:</span> {{ negocio.valor ? negocio.valor.toLocaleString('pt-PT', { style: 'currency', currency: 'EUR' }) : '—' }}</div>
            <div class="mb-2"><span class="font-semibold">Estado:</span> {{ negocio.estado }}</div>
          </div>
          <div>
            <div class="mb-2"><span class="font-semibold">Contactos:</span>
              <ul class="list-disc ml-6">
                <li v-for="c in negocio.contactos" :key="c.id">{{ c.nome }} {{ c.apelido }}</li>
              </ul>
            </div>
          </div>
        </div>
      </div>
      <div>
        <Link :href="route('negocios.index')" class="px-4 py-2 rounded border">Voltar</Link>
      </div>
    </div>
  </AppLayout>
</template> 