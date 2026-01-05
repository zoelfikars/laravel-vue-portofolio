import { defineStore } from "pinia";
import { ref, watch, onUnmounted } from "vue";

export const useThemeStore = defineStore("theme", () => {
    // State
    const theme = ref(localStorage.getItem("theme") || "system");
    const primaryColor = ref(localStorage.getItem("primary-color") || "blue");
    const isDark = ref(false);

    // Media Query Listener
    const mediaQuery = window.matchMedia("(prefers-color-scheme: dark)");

    const applyTheme = () => {
        if (theme.value === "dark") {
            isDark.value = true;
        } else if (theme.value === "light") {
            isDark.value = false;
        } else {
            // System
            isDark.value = mediaQuery.matches;
        }
        updateDom();
    };

    const updateDom = () => {
        if (isDark.value) {
            document.documentElement.classList.add("dark");
        } else {
            document.documentElement.classList.remove("dark");
        }
    };

    // Listener handler
    const handleSystemChange = (e) => {
        if (theme.value === "system") {
            isDark.value = e.matches;
            updateDom();
        }
    };

    // Actions
    const setTheme = (newTheme) => {
        theme.value = newTheme;
        localStorage.setItem("theme", newTheme);
        applyTheme();
    };

    const setPrimaryColor = (color) => {
        primaryColor.value = color;
        localStorage.setItem("primary-color", color);
        document.documentElement.setAttribute("data-primary", color);
    };

    const initTheme = () => {
        setPrimaryColor(primaryColor.value);
        applyTheme();
        mediaQuery.addEventListener("change", handleSystemChange);
    };

    // Cleanup (optional if store is destroyed, but usually global)
    onUnmounted(() => {
        mediaQuery.removeEventListener("change", handleSystemChange);
    });

    return {
        theme,
        isDark, // Exposed for other components if needed
        primaryColor,
        setTheme,
        setPrimaryColor,
        initTheme,
    };
});
