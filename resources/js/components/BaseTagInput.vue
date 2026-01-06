<script setup>
import { ref, computed, watch, nextTick } from "vue";
import { X, Search, Loader2 } from "lucide-vue-next";
import { useDebounceFn } from "@vueuse/core";
import apiClient from "../lib/axios";

// Props
const props = defineProps({
    modelValue: {
        type: Array,
        default: () => [],
    },
    label: {
        type: String,
        default: "",
    },
    fetchUrl: {
        type: String,
        default: "/api/hobbies",
    },
    error: {
        type: String,
        default: "",
    },
    placeholder: {
        type: String,
        default: "Add hobbies...",
    },
});

const emit = defineEmits(["update:modelValue"]);

// Data
const searchQuery = ref("");
const suggestions = ref([]);
const isFocused = ref(false);
const isLoading = ref(false);
const inputRef = ref(null);
const suggestionIndex = ref(-1);

// Computed
const hasValue = computed(() => props.modelValue.length > 0);

// Methods
const startSearch = async (query) => {
    if (!query) {
        suggestions.value = [];
        return;
    }

    isLoading.value = true;
    try {
        const response = await apiClient.get(props.fetchUrl, {
            params: { search: query },
        });
        // Filter out already selected hobbies
        suggestions.value = response.data.data.filter(
            (item) => !props.modelValue.includes(item.name)
        );
    } catch (e) {
        console.error("Failed to fetch suggestions", e);
    } finally {
        isLoading.value = false;
    }
};

const debouncedSearch = useDebounceFn(startSearch, 300);

const onInput = (e) => {
    searchQuery.value = e.target.value;
    suggestionIndex.value = -1;
    debouncedSearch(searchQuery.value);
};

const toTitleCase = (str) => {
    return str.replace(
        /\w\S*/g,
        (text) => text.charAt(0).toUpperCase() + text.substring(1).toLowerCase()
    );
};

const addTag = (tag) => {
    let cleanTag = tag.trim();
    if (!cleanTag) return;

    cleanTag = toTitleCase(cleanTag);
    const isDuplicate = props.modelValue.some(
        (existingTag) => existingTag.toLowerCase() === cleanTag.toLowerCase()
    );

    if (!isDuplicate) {
        const newValue = [...props.modelValue, cleanTag];
        emit("update:modelValue", newValue);
    }
    searchQuery.value = "";
    suggestions.value = [];
    suggestionIndex.value = -1;
    inputRef.value.focus();
};

const removeTag = (index) => {
    const newValue = [...props.modelValue];
    newValue.splice(index, 1);
    emit("update:modelValue", newValue);
};

const onKeydown = (e) => {
    // Backspace: remove last tag if query is empty
    if (
        e.key === "Backspace" &&
        searchQuery.value === "" &&
        props.modelValue.length > 0
    ) {
        removeTag(props.modelValue.length - 1);
        return;
    }

    // Enter: add tag
    if (e.key === "Enter") {
        e.preventDefault();

        // If suggestion selected via Arrow keys
        if (
            suggestionIndex.value >= 0 &&
            suggestions.value[suggestionIndex.value]
        ) {
            addTag(suggestions.value[suggestionIndex.value].name);
        } else if (searchQuery.value) {
            // Add what user typed
            addTag(searchQuery.value);
        }
        return;
    }

    // Navigation
    if (e.key === "ArrowDown") {
        e.preventDefault();
        if (suggestionIndex.value < suggestions.value.length - 1) {
            suggestionIndex.value++;
        }
    } else if (e.key === "ArrowUp") {
        e.preventDefault();
        if (suggestionIndex.value > -1) {
            suggestionIndex.value--;
        }
    }
};

const selectSuggestion = (suggestion) => {
    addTag(suggestion.name);
};

// Clicks outside to close suggestions handled by Blur + Delay?
// Or better simple focus logic.
// We keep suggestions open if input is focused.
const onFocus = () => {
    isFocused.value = true;
};
const onBlur = () => {
    // Small delay to allow click on suggestion
    setTimeout(() => {
        isFocused.value = false;
        suggestionIndex.value = -1;
    }, 200);
};
</script>

<template>
    <div class="relative w-full">
        <label
            v-if="label"
            class="block text-sm font-medium text-text-muted mb-1"
        >
            {{ label }}
        </label>

        <div
            class="min-h-[42px] relative flex flex-wrap items-center gap-2 p-1.5 bg-bg-input border border-border-base rounded-md transition-colors duration-200 cursor-text group"
            :class="{
                'ring-2 ring-primary border-primary': isFocused,
                'border-red-500 ring-red-500': error,
            }"
            @click="inputRef.focus()"
        >
            <!-- Tags -->
            <span
                v-for="(tag, index) in modelValue"
                :key="index"
                class="inline-flex items-center px-2 py-1 rounded bg-primary/10 text-primary text-sm shadow-sm"
            >
                {{ tag }}
                <button
                    type="button"
                    class="ml-1 hover:text-red-500 focus:outline-none"
                    @click.stop="removeTag(index)"
                >
                    <X class="w-3 h-3" />
                </button>
            </span>

            <!-- Input -->
            <input
                ref="inputRef"
                :value="searchQuery"
                @input="onInput"
                @keydown="onKeydown"
                @focus="onFocus"
                @blur="onBlur"
                :placeholder="hasValue ? '' : placeholder"
                class="flex-1 min-w-[120px] bg-transparent outline-none text-text-base text-sm placeholder-text-muted/50 w-full"
                type="text"
            />

            <!-- Loader -->
            <Loader2
                v-if="isLoading"
                class="animate-spin w-4 h-4 text-text-muted absolute right-3"
            />
        </div>

        <!-- Error Message -->
        <p v-if="error" class="mt-1 text-xs text-red-500">
            {{ error }}
        </p>

        <!-- Search Suggestions Dropdown -->
        <transition
            enter-active-class="transition duration-100 ease-out"
            enter-from-class="transform scale-95 opacity-0"
            enter-to-class="transform scale-100 opacity-100"
            leave-active-class="transition duration-75 ease-in"
            leave-from-class="transform scale-100 opacity-100"
            leave-to-class="transform scale-95 opacity-0"
        >
            <div
                v-if="isFocused && suggestions.length > 0"
                class="absolute z-50 left-0 right-0 mt-1 bg-bg-card border border-border-base rounded-md shadow-lg max-h-60 overflow-y-auto"
            >
                <ul class="py-1">
                    <li
                        v-for="(item, index) in suggestions"
                        :key="item.id"
                        @mousedown.prevent="selectSuggestion(item)"
                        class="px-4 py-2 cursor-pointer text-sm md:text-sm text-text-base hover:bg-bg-base flex items-center justify-between group"
                        :class="{ 'bg-bg-base': suggestionIndex === index }"
                    >
                        <span>{{ item.name }}</span>
                    </li>
                </ul>
            </div>
        </transition>
    </div>
</template>
