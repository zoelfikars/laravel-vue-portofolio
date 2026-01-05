<script setup>
import { ref } from "vue";
import { useThemeStore } from "../stores/theme";
import { Palette } from "lucide-vue-next";

const themeStore = useThemeStore();
const isOpen = ref(false);

const colors = [
    { name: "Blue", value: "blue", class: "bg-blue-600" },
    { name: "Green", value: "green", class: "bg-[#238636]" },
    { name: "Purple", value: "purple", class: "bg-purple-600" },
    { name: "Red", value: "red", class: "bg-red-600" },
    { name: "Orange", value: "orange", class: "bg-orange-600" },
    { name: "Teal", value: "teal", class: "bg-teal-600" },
    { name: "Cyan", value: "cyan", class: "bg-cyan-600" },
    { name: "Rose", value: "rose", class: "bg-rose-600" },
];

const selectColor = (color) => {
    themeStore.setPrimaryColor(color);
    isOpen.value = false; // Optional: keep open if user wants to preview
};
</script>

<template>
    <div class="relative">
        <button
            @click="isOpen = !isOpen"
            class="p-2 rounded-lg hover:bg-bg-input text-text-muted hover:text-text-base transition-colors focus:outline-none focus:ring-2 focus:ring-primary/50 flex items-center gap-2"
            title="Change Primary Color"
        >
            <Palette class="w-5 h-5" />
            <!-- Optional: Show current color dot -->
            <span class="w-2 h-2 rounded-full bg-primary block"></span>
        </button>

        <!-- Dropdown -->
        <div
            v-if="isOpen"
            class="absolute right-0 mt-2 w-48 bg-bg-card border border-border-base rounded-lg shadow-lg p-3 z-50"
        >
            <div
                class="text-xs font-semibold text-text-muted mb-2 uppercase tracking-wider"
            >
                Primary Color
            </div>
            <div class="grid grid-cols-4 gap-2">
                <button
                    v-for="color in colors"
                    :key="color.value"
                    @click="selectColor(color.value)"
                    class="w-8 h-8 rounded-full border-2 transition-transform hover:scale-110 focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-primary"
                    :class="[
                        color.class,
                        themeStore.primaryColor === color.value
                            ? 'border-text-base scale-110'
                            : 'border-transparent',
                    ]"
                    :title="color.name"
                    type="button"
                ></button>
            </div>
        </div>

        <!-- Backdrop -->
        <div
            v-if="isOpen"
            @click="isOpen = false"
            class="fixed inset-0 z-40 bg-transparent"
        ></div>
    </div>
</template>
