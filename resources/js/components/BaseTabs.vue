<script setup>
defineProps({
    tabs: {
        type: Array,
        required: true,
        // Expected format: [{ label: 'General', value: 'general' }, ...]
    },
    modelValue: {
        type: String,
        required: true,
    },
});

defineEmits(["update:modelValue"]);
</script>

<template>
    <div class="border-b border-border-base">
        <nav class="-mb-px flex space-x-6 overflow-x-auto" aria-label="Tabs">
            <button
                v-for="tab in tabs"
                :key="tab.value"
                @click="$emit('update:modelValue', tab.value)"
                type="button"
                class="whitespace-nowrap pb-3 border-b-2 font-medium text-sm transition-colors duration-200 px-1"
                :class="[
                    modelValue === tab.value
                        ? 'border-primary text-primary'
                        : 'border-transparent text-text-muted hover:text-text-base hover:border-border-base',
                ]"
                :aria-current="modelValue === tab.value ? 'page' : undefined"
            >
                {{ tab.label }}
            </button>
        </nav>
    </div>
</template>
