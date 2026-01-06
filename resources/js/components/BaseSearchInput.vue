<script setup>
import { Search } from "lucide-vue-next";

defineProps({
    modelValue: {
        type: String,
        default: "",
    },
    placeholder: {
        type: String,
        default: "Search...",
    },
    disabled: {
        type: Boolean,
        default: false,
    },
});

defineEmits(["update:modelValue"]);
</script>

<template>
    <div class="relative w-full sm:w-64">
        <div
            class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
        >
            <Search
                class="h-4 w-4 text-text-muted"
                :class="{ 'opacity-50': disabled }"
            />
        </div>
        <input
            :value="modelValue"
            @input="$emit('update:modelValue', $event.target.value)"
            type="text"
            class="block w-full pl-10 pr-3 py-2 bg-bg-input border border-border-base rounded-md text-text-base placeholder-text-muted/50 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary sm:text-sm transition-colors duration-200 disabled:opacity-50 disabled:bg-bg-base"
            :placeholder="placeholder"
            :disabled="disabled"
        />
    </div>
</template>
