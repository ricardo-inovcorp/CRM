<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import type { SharedData } from '@/types';

const page = usePage<SharedData>();
const tenant = computed(() => page.props.auth.user?.tenant);
</script>

<template>
    <div class="flex items-center">
        <div class="rounded-full bg-white flex items-center justify-center p-1 overflow-hidden h-10 w-10">
            <img src="/storage/logo/GO CRM Logo.png" alt="GO CRM" class="h-8 object-contain" />
        </div>
        <div class="ml-2 grid flex-1 text-left text-sm">
            <span class="mb-0.5 truncate font-semibold leading-none">Go Rocket CRM</span>
            <span v-if="tenant" class="text-xs text-gray-500 truncate">{{ tenant.nome }}</span>
    </div>
    </div>
</template>
