<script setup>
import { ref, computed } from "vue";
import { useRoute } from "vue-router";
import { ChevronDown, ChevronRight } from "lucide-vue-next";

const props = defineProps({
    item: {
        type: Object,
        required: true,
    },
    depth: {
        type: Number,
        default: 0,
    },
});

const route = useRoute();
const isOpen = ref(false);

const isActive = computed(() => {
    if (props.item.to) {
        return (
            route.path === props.item.to ||
            route.path.startsWith(props.item.to + "/")
        );
    }
    // If it has children, check if any child is active
    if (props.item.children) {
        return props.item.children.some(
            (child) =>
                route.path === child.to || route.path.startsWith(child.to + "/")
        );
    }
    return false;
});

// Auto-open if active on mount or route change
if (isActive.value && props.item.children) {
    isOpen.value = true;
}

const toggle = () => {
    if (props.item.children) {
        isOpen.value = !isOpen.value;
    }
};

const emit = defineEmits(["click"]); // Pass click event up (e.g. for closing mobile sidebar)
</script>

<template>
    <div>
        <!-- Menu Item -->
        <component
            :is="item.children ? 'button' : 'router-link'"
            :to="item.children ? undefined : item.to"
            @click="item.children ? toggle() : emit('click')"
            class="flex items-center justify-between w-full px-4 py-2 text-sm font-medium rounded-md transition-colors duration-200 group"
            :class="[
                isActive
                    ? 'bg-bg-base text-text-base border border-border-base'
                    : 'text-text-muted hover:bg-bg-base hover:text-text-base',
                depth > 0 ? 'pl-8' : '',
            ]"
        >
            <div class="flex items-center">
                <component
                    v-if="item.icon"
                    :is="item.icon"
                    class="mr-3 h-5 w-5 flex-shrink-0 transition-colors duration-200"
                    :class="
                        isActive
                            ? 'text-text-base'
                            : 'text-text-muted group-hover:text-text-base'
                    "
                />
                <span>{{ item.label }}</span>
            </div>

            <component
                v-if="item.children"
                :is="isOpen ? ChevronDown : ChevronRight"
                class="w-4 h-4 text-text-muted"
            />
        </component>

        <!-- Submenu -->
        <div v-if="item.children && isOpen" class="mt-1 space-y-1">
            <SidebarMenu
                v-for="(child, index) in item.children"
                :key="index"
                :item="child"
                :depth="depth + 1"
                @click="emit('click')"
            />
        </div>
    </div>
</template>
