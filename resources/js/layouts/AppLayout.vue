<script setup>
import { computed, onMounted, ref } from "vue";
import { useAuthStore } from "../stores/auth";
import { useThemeStore } from "../stores/theme";
import { useRouter } from "vue-router";
import ThemeToggle from "../components/ThemeToggle.vue";
import PrimaryColorSelector from "../components/PrimaryColorSelector.vue";

const authStore = useAuthStore();
const themeStore = useThemeStore();
const router = useRouter();

const user = computed(() => authStore.user);

const handleLogout = async () => {
    await authStore.logout();
    router.push("/login");
};

// Colors moved to PrimaryColorSelector component

onMounted(() => {
    themeStore.initTheme();
});
</script>

<template>
    <div
        class="flex h-screen bg-bg-base text-text-base font-sans transition-colors duration-200"
    >
        <!-- Sidebar -->
        <aside
            class="w-64 bg-bg-card border-r border-border-base flex flex-col fixed inset-y-0 left-0 z-30 transition-colors duration-200"
        >
            <div
                class="p-6 border-b border-border-base flex items-center gap-3"
            >
                <svg
                    height="24"
                    viewBox="0 0 16 16"
                    width="24"
                    class="fill-text-base"
                >
                    <path
                        d="M8 0c4.42 0 8 3.58 8 8a8.013 8.013 0 0 1-5.45 7.59c-.4.08-.55-.17-.55-.38 0-.27.01-1.13.01-2.2 0-.75-.25-1.23-.54-1.48 1.78-.2 3.65-.88 3.65-3.95 0-.88-.31-1.59-.82-2.15.08-.2.36-1.02-.08-2.12 0 0-.67-.22-2.2.82-.64-.18-1.32-.27-2-.27-.68 0-1.36.09-2 .27-1.53-1.03-2.2-.82-2.2-.82-.44 1.1-.16 1.92-.08 2.12-.51.56-.82 1.28-.82 2.15 0 3.06 1.86 3.75 3.64 3.95-.23.2-.44.55-.51 1.07-.46.21-1.61.55-2.33-.66-.15-.24-.6-.83-1.23-.82-.67.01-.27.38.01.53.34.19.73.9.82 1.13.16.45.68 1.31 2.69.94 0 .67.01 1.3.01 1.49 0 .21-.15.45-.55.38A7.995 7.995 0 0 1 0 8c0-4.42 3.58-8 8-8Z"
                    ></path>
                </svg>
                <h1 class="font-bold text-lg tracking-tight">Portfolio</h1>
            </div>

            <nav class="flex-1 p-4 space-y-2">
                <router-link
                    to="/admin"
                    class="flex items-center px-4 py-2 text-sm font-medium rounded-md hover:bg-bg-base text-text-muted hover:text-text-base transition-colors"
                    active-class="bg-bg-base text-text-base border border-border-base"
                >
                    <svg
                        class="mr-3 h-5 w-5"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"
                        />
                    </svg>
                    Dashboard
                </router-link>

                <router-link
                    to="/users"
                    class="flex items-center px-4 py-2 text-sm font-medium rounded-md hover:bg-bg-base text-text-muted hover:text-text-base transition-colors"
                    active-class="bg-bg-base text-text-base border border-border-base"
                >
                    <svg
                        class="mr-3 h-5 w-5"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"
                        />
                    </svg>
                    Users
                </router-link>
            </nav>

            <div class="p-4 border-t border-border-base">
                <p class="text-xs text-text-muted text-center">
                    &copy; 2026 Laravue Portfolio
                </p>
            </div>
        </aside>

        <!-- Main Content Wrapper -->
        <div class="flex-1 flex flex-col ml-64 min-w-0 bg-bg-base">
            <!-- Header -->
            <header
                class="bg-bg-card border-b border-border-base h-16 flex items-center justify-between px-8 z-20 transition-colors duration-200"
            >
                <h2 class="text-xl font-semibold text-text-base">Dashboard</h2>

                <div class="flex items-center gap-4">
                    <ThemeToggle />
                    <PrimaryColorSelector />

                    <div class="h-6 w-px bg-border-base mx-2"></div>

                    <!-- User Info -->
                    <div class="text-right hidden sm:block">
                        <p class="text-sm font-medium text-text-base">
                            {{ user?.name || "User" }}
                        </p>
                        <p class="text-xs text-text-muted">
                            {{ user?.roles?.[0] || "Member" }}
                        </p>
                    </div>

                    <button
                        @click="handleLogout"
                        class="px-3 py-1.5 text-xs font-medium text-red-500 border border-red-500/30 rounded hover:bg-red-500/10 transition-colors"
                    >
                        Sign out
                    </button>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-auto p-8">
                <slot></slot>
            </main>
        </div>
    </div>
</template>
