<script setup>
import { useNotificationStore } from "../stores/notification";
import {
    X,
    CheckCircle,
    AlertCircle,
    Info,
    AlertTriangle,
} from "lucide-vue-next";

const notificationStore = useNotificationStore();

const icons = {
    success: CheckCircle,
    error: AlertCircle,
    info: Info,
    warning: AlertTriangle,
};

const styles = {
    success: "border-l-4 border-l-green-500",
    error: "border-l-4 border-l-red-500",
    info: "border-l-4 border-l-blue-500",
    warning: "border-l-4 border-l-orange-500",
};

const iconColors = {
    success: "text-green-500",
    error: "text-red-500",
    info: "text-blue-500",
    warning: "text-orange-500",
};
</script>

<template>
    <div
        class="fixed top-4 right-4 z-50 flex flex-col gap-2 w-full max-w-sm pointer-events-none"
    >
        <TransitionGroup
            enter-active-class="transform ease-out duration-300 transition"
            enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
            enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
            leave-active-class="transition ease-in duration-100"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-for="notification in notificationStore.notifications"
                :key="notification.id"
                class="pointer-events-auto w-full max-w-sm overflow-hidden rounded-lg shadow-lg bg-bg-card border border-border-base ring-1 ring-black ring-opacity-5 transition-all duration-300"
                :class="styles[notification.type]"
            >
                <div class="p-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <component
                                :is="icons[notification.type]"
                                class="h-6 w-6"
                                :class="iconColors[notification.type]"
                                aria-hidden="true"
                            />
                        </div>
                        <div class="ml-3 w-0 flex-1 pt-0.5">
                            <p class="text-sm font-medium text-text-base">
                                {{ notification.message }}
                            </p>
                        </div>
                        <div class="ml-4 flex flex-shrink-0">
                            <button
                                type="button"
                                @click="
                                    notificationStore.remove(notification.id)
                                "
                                class="inline-flex rounded-md bg-transparent text-text-muted hover:text-text-base focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2"
                            >
                                <span class="sr-only">Close</span>
                                <X class="h-5 w-5" aria-hidden="true" />
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </TransitionGroup>
    </div>
</template>
