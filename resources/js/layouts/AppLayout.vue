<script setup lang="ts">
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import type { BreadcrumbItemType } from '@/types';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});

// Direct navigation functions
function goToUsersTest() {
    window.location.href = '/users-test';
}

function goToUsersDebug() {
    window.location.href = '/users-debug';
}

function goToRoutesDebug() {
    window.location.href = '/routes-debug';
}

function goToUsers() {
    window.location.href = '/users';
}

function goToSimpleDebug() {
    window.location.href = '/simple-debug';
}

function goToSimpleDebugLowercase() {
    window.location.href = '/simple-debug-lowercase';
}

function goToDirectTest() {
    window.location.href = '/direct-test';
}

function goToUsersDirect() {
    window.location.href = '/users-direct';
}

function goToUsersBypass() {
    window.location.href = '/users-bypass';
}
</script>

<template>
    <AppSidebarLayout :breadcrumbs="breadcrumbs">
        <slot />
    </AppSidebarLayout>
</template>
