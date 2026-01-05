<script setup>
import { X } from "lucide-vue-next";
import { onMounted, onUnmounted } from "vue";

defineProps({
    isOpen: {
        type: Boolean,
        required: true,
    },
    title: {
        type: String,
        default: "",
    },
    maxWidth: {
        type: String,
        default: "sm:max-w-lg", // Tailiwnd class
    },
});

const emit = defineEmits(["close"]);

// Prevent scrolling when modal is open
const preventScroll = () => {
    document.body.style.overflow = "hidden";
};

const allowScroll = () => {
    document.body.style.overflow = "";
};

// Hooks are handled by parent usage mostly, but watcher might be better if simple
// For now, simpler implementation relying on transition
</script>

<template>
    <Teleport to="body">
        <Transition
            enter-active-class="ease-out duration-300"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="ease-in duration-200"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="isOpen"
                class="fixed inset-0 z-50 overflow-y-auto"
                aria-labelledby="modal-title"
                role="dialog"
                aria-modal="true"
            >
                <div
                    class="flex min-h-screen items-center justify-center p-4 text-center sm:p-0"
                >
                    <!-- Backdrop -->
                    <div
                        class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity"
                        aria-hidden="true"
                        @click="$emit('close')"
                    ></div>

                    <Transition
                        enter-active-class="ease-out duration-300"
                        enter-from-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        enter-to-class="opacity-100 translate-y-0 sm:scale-100"
                        leave-active-class="ease-in duration-200"
                        leave-from-class="opacity-100 translate-y-0 sm:scale-100"
                        leave-to-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    >
                        <div
                            class="relative transform overflow-hidden rounded-lg bg-bg-card text-left shadow-xl transition-all sm:my-8 w-full border border-border-base"
                            :class="maxWidth"
                        >
                            <!-- Header -->
                            <div
                                class="px-4 py-4 sm:px-6 bg-bg-card border-b border-border-base flex justify-between items-center transition-colors duration-200"
                            >
                                <h3
                                    class="text-lg leading-6 font-medium text-text-base"
                                    id="modal-title"
                                >
                                    {{ title }}
                                </h3>
                                <button
                                    @click="$emit('close')"
                                    class="text-text-muted hover:text-text-base transition-colors focus:outline-none"
                                >
                                    <X class="w-5 h-5" />
                                </button>
                            </div>

                            <!-- Body -->
                            <div
                                class="px-4 pt-5 pb-4 sm:p-6 bg-bg-card transition-colors duration-200"
                            >
                                <slot></slot>
                            </div>

                            <!-- Footer -->
                            <div
                                v-if="$slots.footer"
                                class="bg-bg-base px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-border-base transition-colors duration-200"
                            >
                                <slot name="footer"></slot>
                            </div>
                        </div>
                    </Transition>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>
