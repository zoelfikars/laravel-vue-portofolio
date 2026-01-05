<script setup>
defineProps({
    width: {
        type: String,
        default: "100%",
    },
    height: {
        type: String,
        default: "auto",
    },
    shape: {
        type: String,
        default: "rectangle", // 'rectangle' | 'circle'
        validator: (value) => ["rectangle", "circle"].includes(value),
    },
    className: {
        type: String,
        default: "",
    },
});
</script>

<template>
    <div
        class="animate-pulse bg-gray-200 dark:bg-gray-700/50"
        :class="[shape === 'circle' ? 'rounded-full' : 'rounded-md', className]"
        :style="{ width, height }"
    ></div>
</template>
