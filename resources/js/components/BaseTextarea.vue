<script setup>
defineProps({
    modelValue: {
        type: String,
        default: "",
    },
    label: {
        type: String,
        default: "",
    },
    placeholder: {
        type: String,
        default: "",
    },
    error: {
        type: String,
        default: "",
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    rows: {
        type: [String, Number],
        default: 3,
    },
    id: {
        type: String,
        default: () => `textarea-${Math.random().toString(36).substr(2, 9)}`,
    },
});

defineEmits(["update:modelValue"]);
</script>

<template>
    <div>
        <label
            v-if="label"
            :for="id"
            class="block text-sm font-medium text-text-muted mb-1"
        >
            {{ label }}
        </label>
        <div class="relative">
            <textarea
                :id="id"
                :value="modelValue"
                @input="$emit('update:modelValue', $event.target.value)"
                :placeholder="placeholder"
                :disabled="disabled"
                :rows="rows"
                class="block w-full px-3 py-2 bg-bg-input border border-border-base rounded-md text-text-base placeholder-text-muted/50 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary sm:text-sm transition-colors duration-200 disabled:opacity-50 disabled:bg-bg-base scrollbar-thin scrollbar-thumb-border-base scrollbar-track-transparent"
                :class="{
                    'border-red-500 focus:ring-red-500 focus:border-red-500':
                        error,
                }"
            ></textarea>
        </div>
        <p v-if="error" class="mt-1 text-xs text-red-500">
            {{ error }}
        </p>
    </div>
</template>
