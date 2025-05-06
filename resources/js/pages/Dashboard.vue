<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout>
        <div class="p-6">
            <h1 class="text-2xl font-bold mb-6">Dashboard</h1>
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <p>Bem-vindo ao painel de controle do seu CRM!</p>
            </div>
        </div>
    </AppLayout>
</template> 