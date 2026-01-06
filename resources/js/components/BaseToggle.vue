<script setup>
import { computed } from "vue";

const props = defineProps({
    modelValue: {
        type: Boolean,
        default: false,
    },
    label: {
        type: String,
        default: "",
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    error: {
        type: String,
        default: "",
    },
});

const emit = defineEmits(["update:modelValue"]);

const toggle = () => {
    if (!props.disabled) {
        emit("update:modelValue", !props.modelValue);
    }
};
</script>

<template>
    <div>
        <div class="flex items-center justify-between">
            <span v-if="label" class="flex-grow flex flex-col">
                <span class="text-sm font-medium text-text-base">{{
                    label
                }}</span>
            </span>
            <button
                type="button"
                role="switch"
                :aria-checked="modelValue"
                :disabled="disabled"
                @click="toggle"
                class="relative inline-flex flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary"
                :class="
                    modelValue ? 'bg-primary' : 'bg-gray-200 dark:bg-gray-700'
                "
            >
                <span
                    aria-hidden="true"
                    class="pointer-events-none inline-block h-5 w-5 rounded-full bg-white shadow transform ring-0 transition ease-in-out duration-200"
                    :class="modelValue ? 'translate-x-5' : 'translate-x-0'"
                ></span>
            </button>
        </div>
        <p v-if="error" class="mt-1 text-xs text-red-500">
            {{ error }}
        </p>
    </div>
</template>
