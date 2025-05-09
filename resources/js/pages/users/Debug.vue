<template>
    <div class="p-6">
        <h1 class="text-2xl font-bold">Debug Component (capital P path)</h1>
        <div class="bg-white p-4 mt-4 shadow rounded">
            <pre>{{ JSON.stringify(users, null, 2) }}</pre>
        </div>
    </div>
</template>

<script setup>
defineProps({
    users: Array,
    roles: Array
});
</script> 