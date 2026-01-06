<script setup>
import { computed, onMounted, ref } from "vue";
import { useAuthStore } from "../stores/auth";
import { useThemeStore } from "../stores/theme";
import { useRouter } from "vue-router";
import AppSidebar from "./partials/AppSidebar.vue";

const authStore = useAuthStore();
const themeStore = useThemeStore();
const router = useRouter();

const user = computed(() => authStore.user);
const isSidebarOpen = ref(false);

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
        class="flex h-screen bg-bg-base text-text-base font-sans transition-colors duration-200 overflow-hidden"
    >
        <!-- Mobile Backdrop -->
        <div
            v-if="isSidebarOpen"
            @click="isSidebarOpen = false"
            class="fixed inset-0 bg-black/50 z-20 lg:hidden transition-opacity"
        ></div>

        <!-- Sidebar -->
        <AppSidebar :isOpen="isSidebarOpen" @close="isSidebarOpen = false" />

        <!-- Main Content Wrapper -->
        <div
            class="flex-1 flex flex-col min-w-0 bg-bg-base transition-all duration-300"
        >
            <!-- Header -->
            <header
                class="bg-bg-card border-b border-border-base h-16 flex items-center justify-between px-4 sm:px-8 z-20 transition-colors duration-200 sticky top-0"
            >
                <div class="flex items-center gap-4">
                    <!-- Hamburger Menu -->
                    <button
                        @click="isSidebarOpen = true"
                        class="lg:hidden text-text-muted hover:text-text-base focus:outline-none"
                    >
                        <svg
                            class="h-6 w-6"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"
                            ></path>
                        </svg>
                    </button>

                    <h2 class="text-xl font-semibold text-text-base">
                        {{
                            $route.name === "AdminDashboard" ? "Dashboard" : ""
                        }}
                        {{
                            $route.name === "UserList" ? "User Management" : ""
                        }}
                    </h2>
                </div>

                <div class="flex items-center gap-2 sm:gap-4">
                    <ThemeToggle />
                    <PrimaryColorSelector />

                    <div
                        class="h-6 w-px bg-border-base mx-2 hidden sm:block"
                    ></div>

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
            <main class="flex-1 overflow-auto p-4 sm:p-8">
                <slot></slot>
            </main>
        </div>
    </div>
</template>
