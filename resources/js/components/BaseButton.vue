<script setup>
import { computed } from "vue";
import { Loader2 } from "lucide-vue-next";

const props = defineProps({
    variant: {
        type: String,
        default: "primary", // 'primary' | 'secondary' | 'danger' | 'ghost'
    },
    type: {
        type: String,
        default: "button",
    },
    isLoading: {
        type: Boolean,
        default: false,
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    block: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(["click"]);

const classes = computed(() => {
    const base =
        "inline-flex items-center justify-center px-4 py-2 border text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed";

    const variants = {
        primary:
            "border-transparent shadow-sm text-primary-foreground bg-primary hover:opacity-90 focus:ring-primary",
        secondary:
            "border-border-base shadow-sm text-text-base bg-bg-card hover:bg-bg-base focus:ring-primary",
        danger: "border-transparent shadow-sm text-white bg-red-600 hover:bg-red-700 focus:ring-red-500",
        ghost: "border-transparent text-text-muted hover:text-text-base hover:bg-bg-base focus:ring-gray-500",
    };

    return [
        base,
        variants[props.variant] || variants.primary,
        props.block ? "w-full" : "",
    ].join(" ");
});
</script>

<template>
    <button
        :type="type"
        :class="classes"
        :disabled="disabled || isLoading"
        @click="emit('click', $event)"
    >
        <Loader2 v-if="isLoading" class="w-4 h-4 mr-2 animate-spin" />
        <slot></slot>
    </button>
</template>
