<script setup>
defineProps({
    value: {
        type: String,
    },
    for: {
        type: String,
        default: '',
    },
    required: {
        type: Boolean,
        default: false,
    }
});
</script>

<template>
    <label :for="for" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
        <span v-if="value">{{ value }}</span>
        <span v-else><slot /></span>
        <span v-if="required" class="text-red-500 ml-1">*</span>
    </label>
</template> 