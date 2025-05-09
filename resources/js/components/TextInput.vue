<script setup>
import { onMounted, ref } from 'vue';

defineProps({
    modelValue: {
        type: [String, Number],
        required: true,
    },
    type: {
        type: String,
        default: 'text',
    },
    id: {
        type: String,
        default: '',
    },
    class: {
        type: String,
        default: '',
    },
    required: {
        type: Boolean,
        default: false,
    },
    autofocus: {
        type: Boolean,
        default: false,
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    autocomplete: {
        type: String,
        default: '',
    },
});

defineEmits(['update:modelValue']);

const input = ref(null);

onMounted(() => {
    if (input.value && input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <input
        :id="id"
        ref="input"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
        :type="type"
        :class="class"
        :required="required"
        :disabled="disabled"
        :autocomplete="autocomplete"
    />
</template> 