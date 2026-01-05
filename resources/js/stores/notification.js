import { defineStore } from "pinia";
import { ref } from "vue";

export const useNotificationStore = defineStore("notification", () => {
    const notifications = ref([]);

    const add = (notification) => {
        const id = Date.now() + Math.random();
        const duration = notification.duration || 3000;

        notifications.value.push({
            id,
            ...notification,
        });

        if (duration > 0) {
            setTimeout(() => {
                remove(id);
            }, duration);
        }
    };

    const remove = (id) => {
        notifications.value = notifications.value.filter((n) => n.id !== id);
    };

    const success = (message, duration = 3000) => {
        add({
            type: "success",
            message,
            duration,
        });
    };

    const error = (message, duration = 4000) => {
        add({
            type: "error",
            message,
            duration,
        });
    };

    const info = (message, duration = 3000) => {
        add({
            type: "info",
            message,
            duration,
        });
    };

    const warning = (message, duration = 3000) => {
        add({
            type: "warning",
            message,
            duration,
        });
    };

    return {
        notifications,
        add,
        remove,
        success,
        error,
        info,
        warning,
    };
});
