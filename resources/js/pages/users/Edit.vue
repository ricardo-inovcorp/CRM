<template>
    <Head title="Editar Utilizador" />
</template>

<script setup>
import { Head, router } from '@inertiajs/vue3';

// Redirect to index page
router.visit(route('users.index'));
</script> 