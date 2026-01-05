<script setup>
import { useThemeStore } from "../stores/theme";
import { Sun, Moon, Monitor } from "lucide-vue-next";
import { computed, ref } from "vue";

const themeStore = useThemeStore();
const isOpen = ref(false);

const currentIcon = computed(() => {
    switch (themeStore.theme) {
        case "dark":
            return Moon;
        case "light":
            return Sun;
        default:
            return Monitor;
    }
});

const options = [
    { value: "light", label: "Light", icon: Sun },
    { value: "dark", label: "Dark", icon: Moon },
    { value: "system", label: "System", icon: Monitor },
];

const selectTheme = (value) => {
    themeStore.setTheme(value);
    isOpen.value = false;
};
</script>

<template>
    <div class="relative">
        <button
            @click="isOpen = !isOpen"
            class="p-2 rounded-lg hover:bg-bg-input text-text-muted hover:text-text-base transition-colors focus:outline-none focus:ring-2 focus:ring-primary/50"
            title="Switch Theme"
        >
            <component :is="currentIcon" class="w-5 h-5" />
        </button>

        <!-- Dropdown -->
        <div
            v-if="isOpen"
            class="absolute right-0 mt-2 w-36 bg-bg-card border border-border-base rounded-lg shadow-lg py-1 z-50"
        >
            <button
                v-for="option in options"
                :key="option.value"
                @click="selectTheme(option.value)"
                class="w-full px-4 py-2 text-sm flex items-center gap-3 hover:bg-bg-base transition-colors"
                :class="
                    themeStore.theme === option.value
                        ? 'text-primary font-medium'
                        : 'text-text-base'
                "
            >
                <component :is="option.icon" class="w-4 h-4" />
                {{ option.label }}
            </button>
        </div>

        <!-- Backdrop -->
        <div
            v-if="isOpen"
            @click="isOpen = false"
            class="fixed inset-0 z-40 bg-transparent"
        ></div>
    </div>
</template>
