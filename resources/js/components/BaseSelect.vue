<script setup>
defineProps({
    modelValue: {
        type: [String, Number],
        default: "",
    },
    label: {
        type: String,
        default: "",
    },
    options: {
        type: Array,
        required: true,
        // Accept array of strings or objects { label: '...', value: '...' }
    },
    placeholder: {
        type: String,
        default: "Select an option",
    },
    error: {
        type: String,
        default: "",
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    id: {
        type: String,
        default: () => `select-${Math.random().toString(36).substr(2, 9)}`,
    },
});

defineEmits(["update:modelValue"]);

const getLabel = (option) => {
    return typeof option === "object" ? option.label : option;
};

const getValue = (option) => {
    return typeof option === "object" ? option.value : option;
};
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
            <select
                :id="id"
                :value="modelValue"
                @change="$emit('update:modelValue', $event.target.value)"
                :disabled="disabled"
                class="block w-full px-3 py-2 bg-bg-input border border-border-base rounded-md text-text-base focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary sm:text-sm transition-colors duration-200 disabled:opacity-50 disabled:bg-bg-base appearance-none bg-no-repeat pr-10"
                :class="{
                    'border-red-500 focus:ring-red-500 focus:border-red-500':
                        error,
                    'text-text-muted': !modelValue,
                }"
                style="
                    background-position: right 0.7rem top 50%;
                    background-size: 0.65em auto;
                "
            >
                <option value="" disabled selected>{{ placeholder }}</option>
                <option
                    v-for="(option, index) in options"
                    :key="index"
                    :value="getValue(option)"
                >
                    {{ getLabel(option) }}
                </option>
            </select>
        </div>
        <p v-if="error" class="mt-1 text-xs text-red-500">
            {{ error }}
        </p>
    </div>
</template>
