<template>
    <div
        class="bg-bg-card border border-border-base rounded-lg shadow-sm overflow-hidden transition-colors duration-200"
    >
        <div v-if="$slots.header" class="px-6 py-4 border-b border-border-base">
            <slot name="header"></slot>
        </div>

        <div class="p-6">
            <slot></slot>
        </div>

        <div
            v-if="$slots.footer"
            class="px-6 py-4 bg-bg-base border-t border-border-base"
        >
            <slot name="footer"></slot>
        </div>
    </div>
</template>
