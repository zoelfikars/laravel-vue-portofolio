<script setup>
import { RouterLink, RouterView } from "vue-router";
import ThemeToggle from "../components/ThemeToggle.vue";
import PrimaryColorSelector from "../components/PrimaryColorSelector.vue";
import { ref } from "vue";
import { Menu, X } from "lucide-vue-next";
import { useThemeStore } from "../stores/theme";
import { onMounted } from "vue";

const isMenuOpen = ref(false);
const themeStore = useThemeStore();
onMounted(() => {
    themeStore.initTheme();
});
</script>

<template>
    <div
        class="min-h-screen bg-bg-base text-text-base font-sans transition-colors duration-300 flex flex-col"
    >
        <header
            class="sticky top-4 z-50 w-full flex justify-center px-4 pointer-events-none"
        >
            <div
                class="pointer-events-auto w-full max-w-5xl flex items-center justify-between gap-4 bg-bg-card/80 backdrop-blur-md border border-border-base rounded-full shadow-lg pl-6 pr-4 h-16 transition-all duration-300 relative"
            >
                <div class="flex items-center gap-8">
                    <!-- Logo -->
                    <RouterLink
                        to="/"
                        class="font-bold text-xl tracking-tight hover:text-primary transition-colors shrink-0"
                    >
                        Portfolio.
                    </RouterLink>

                    <!-- Desktop Nav -->
                    <nav class="hidden md:flex gap-1">
                        <RouterLink
                            to="/"
                            active-class="bg-bg-base text-primary font-semibold"
                            exact-active-class="bg-bg-base text-primary font-semibold"
                            class="px-4 py-2 rounded-full text-sm font-medium hover:text-primary hover:bg-bg-base/50 transition-all duration-200"
                        >
                            Home
                        </RouterLink>
                        <RouterLink
                            to="/experiences"
                            active-class="bg-bg-base text-primary font-semibold"
                            class="px-4 py-2 rounded-full text-sm font-medium hover:text-primary hover:bg-bg-base/50 transition-all duration-200"
                        >
                            Experience
                        </RouterLink>
                        <RouterLink
                            to="/projects"
                            active-class="bg-bg-base text-primary font-semibold"
                            class="px-4 py-2 rounded-full text-sm font-medium hover:text-primary hover:bg-bg-base/50 transition-all duration-200"
                        >
                            Projects
                        </RouterLink>
                    </nav>
                </div>

                <div class="flex items-center gap-2">
                    <PrimaryColorSelector />
                    <ThemeToggle />

                    <!-- Mobile Menu Button -->
                    <button
                        @click="isMenuOpen = !isMenuOpen"
                        class="md:hidden p-2 rounded-full hover:bg-bg-base focus:outline-none transition-colors"
                    >
                        <Menu v-if="!isMenuOpen" class="w-5 h-5" />
                        <X v-else class="w-5 h-5" />
                    </button>
                </div>

                <!-- Mobile Menu -->
                <div
                    v-if="isMenuOpen"
                    class="md:hidden absolute top-full left-0 right-0 mt-3 p-4 bg-bg-card/95 backdrop-blur-md border border-border-base rounded-2xl shadow-xl flex flex-col gap-2 mx-auto w-full origin-top animate-in fade-in slide-in-from-top-4 duration-200"
                >
                    <RouterLink
                        to="/"
                        @click="isMenuOpen = false"
                        active-class="bg-primary/10 text-primary"
                        exact-active-class="bg-primary/10 text-primary"
                        class="px-4 py-3 rounded-xl hover:bg-bg-base transition-colors font-medium text-center"
                    >
                        Home
                    </RouterLink>
                    <RouterLink
                        to="/experiences"
                        @click="isMenuOpen = false"
                        active-class="bg-primary/10 text-primary"
                        class="px-4 py-3 rounded-xl hover:bg-bg-base transition-colors font-medium text-center"
                    >
                        Experience
                    </RouterLink>
                    <RouterLink
                        to="/projects"
                        @click="isMenuOpen = false"
                        active-class="bg-primary/10 text-primary"
                        class="px-4 py-3 rounded-xl hover:bg-bg-base transition-colors font-medium text-center"
                    >
                        Projects
                    </RouterLink>
                </div>
            </div>
        </header>

        <main class="flex-1">
            <RouterView />
        </main>

        <footer
            class="border-t border-border-base py-8 text-center text-sm text-text-muted bg-bg-card mt-auto"
        >
            <div class="container mx-auto px-4">
                <p>
                    &copy; {{ new Date().getFullYear() }} Zoelfikars. All rights
                    reserved.
                </p>
            </div>
        </footer>
    </div>
</template>
