<script setup>
import { LayoutDashboard, Users, ChevronLeft } from "lucide-vue-next";
import SidebarMenu from "./SidebarMenu.vue";

defineProps({
    isOpen: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(["close"]);

const menuItems = [
    {
        label: "Dashboard",
        to: "/admin",
        icon: LayoutDashboard,
    },
    {
        label: "Users",
        icon: Users,
        children: [
            {
                label: "All Users",
                to: "/users",
            },
            // Example of future submenu
            // {
            //     label: 'Roles',
            //     to: '/roles',
            // },
        ],
    },
];
</script>

<template>
    <aside
        class="w-64 bg-bg-card border-r border-border-base flex flex-col fixed inset-y-0 left-0 z-30 transition-transform duration-300 lg:translate-x-0 lg:static"
        :class="isOpen ? 'translate-x-0' : '-translate-x-full'"
    >
        <!-- Header -->
        <div
            class="p-6 border-b border-border-base flex items-center justify-between gap-3 h-16"
        >
            <div class="flex items-center gap-3">
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

            <button
                @click="emit('close')"
                class="lg:hidden text-text-muted hover:text-text-base focus:outline-none"
            >
                <ChevronLeft class="w-6 h-6" />
            </button>
        </div>

        <!-- Menu -->
        <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
            <SidebarMenu
                v-for="(item, index) in menuItems"
                :key="index"
                :item="item"
                @click="emit('close')"
            />
        </nav>

        <!-- Footer -->
        <div class="p-4 border-t border-border-base">
            <p class="text-xs text-text-muted text-center">
                &copy; 2026 Laravue Portfolio
            </p>
        </div>
    </aside>
</template>
