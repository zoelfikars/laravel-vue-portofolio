<script setup>
import { ref, watch, nextTick } from "vue";
import {
    Bold,
    Italic,
    List,
    Link,
    Code,
    Heading2,
    Heading3,
} from "lucide-vue-next";

const props = defineProps({
    modelValue: {
        type: String,
        default: "",
    },
    label: {
        type: String,
        default: "",
    },
    error: {
        type: String,
        default: "",
    },
    rows: {
        type: Number,
        default: 10,
    },
    placeholder: {
        type: String,
        default: "",
    },
});

const emit = defineEmits(["update:modelValue"]);
const textareaRef = ref(null);

const insertFormat = (format) => {
    const textarea = textareaRef.value;
    if (!textarea) return;

    const start = textarea.selectionStart;
    const end = textarea.selectionEnd;
    const text = textarea.value;
    const selectedText = text.substring(start, end);

    let insertion = "";
    let newCursorPos = start;

    switch (format) {
        case "h2":
            insertion = `## ${selectedText || "Heading 2"}`;
            newCursorPos = start + insertion.length;
            break;
        case "h3":
            insertion = `### ${selectedText || "Heading 3"}`;
            newCursorPos = start + insertion.length;
            break;
        case "bold":
            insertion = `**${selectedText || "bold text"}**`;
            // If text started empty, cursor inside tags
            newCursorPos = selectedText ? start + insertion.length : start + 2;
            break;
        case "italic":
            insertion = `_${selectedText || "italic text"}_`;
            newCursorPos = selectedText ? start + insertion.length : start + 1;
            break;
        case "list":
            insertion = `\n- ${selectedText || "List item"}`;
            newCursorPos = start + insertion.length;
            break;
        case "code":
            // Handle multiline selection for code block or inline code
            if (selectedText.includes("\n")) {
                insertion = `\n\`\`\`\n${
                    selectedText || "code block"
                }\n\`\`\`\n`;
                newCursorPos = start + insertion.length - 5; // roughly inside
            } else {
                insertion = `\`${selectedText || "code"}\``;
                newCursorPos = selectedText
                    ? start + insertion.length
                    : start + 1;
            }
            break;
        case "link":
            insertion = `[${selectedText || "link text"}](url)`;
            newCursorPos = selectedText ? start + insertion.length : start + 1;
            break;
    }

    // Use setRangeText to preserve undo history if supported browser mostly do
    textarea.setRangeText(insertion, start, end, "end");

    // Update model
    emit("update:modelValue", textarea.value);

    // Restore focus and set cursor logic (setRangeText 'end' puts it at end, but we might want precise positioning)
    textarea.focus();

    // For placeholders (when no selection), we might want to select the placeholder text so user can type over it immediately
    // Ideally we could improve this, but putting cursor at end of insertion is standard MVP behavior.
};

const updateValue = (e) => {
    emit("update:modelValue", e.target.value);
};
</script>

<template>
    <div class="flex flex-col gap-1">
        <label v-if="label" class="text-sm font-medium text-text-base">
            {{ label }}
        </label>

        <div
            class="border rounded-md shadow-sm transition-colors duration-200 overflow-hidden bg-bg-card focus-within:border-primary focus-within:ring-1 focus-within:ring-primary"
            :class="[error ? 'border-red-500' : 'border-border-base']"
        >
            <!-- Toolbar -->
            <div
                class="flex items-center gap-1 p-2 bg-bg-base border-b border-border-base overflow-x-auto"
            >
                <button
                    type="button"
                    @click="insertFormat('h2')"
                    class="p-1.5 rounded hover:bg-bg-card text-text-muted hover:text-text-base transition-colors"
                    title="Heading 2"
                >
                    <Heading2 class="w-4 h-4" />
                </button>
                <button
                    type="button"
                    @click="insertFormat('h3')"
                    class="p-1.5 rounded hover:bg-bg-card text-text-muted hover:text-text-base transition-colors"
                    title="Heading 3"
                >
                    <Heading3 class="w-4 h-4" />
                </button>
                <div class="w-px h-4 bg-border-base mx-1"></div>
                <button
                    type="button"
                    @click="insertFormat('bold')"
                    class="p-1.5 rounded hover:bg-bg-card text-text-muted hover:text-text-base transition-colors"
                    title="Bold"
                >
                    <Bold class="w-4 h-4" />
                </button>
                <button
                    type="button"
                    @click="insertFormat('italic')"
                    class="p-1.5 rounded hover:bg-bg-card text-text-muted hover:text-text-base transition-colors"
                    title="Italic"
                >
                    <Italic class="w-4 h-4" />
                </button>
                <div class="w-px h-4 bg-border-base mx-1"></div>
                <button
                    type="button"
                    @click="insertFormat('list')"
                    class="p-1.5 rounded hover:bg-bg-card text-text-muted hover:text-text-base transition-colors"
                    title="List"
                >
                    <List class="w-4 h-4" />
                </button>
                <button
                    type="button"
                    @click="insertFormat('code')"
                    class="p-1.5 rounded hover:bg-bg-card text-text-muted hover:text-text-base transition-colors"
                    title="Code Block"
                >
                    <Code class="w-4 h-4" />
                </button>
                <div class="w-px h-4 bg-border-base mx-1"></div>
                <button
                    type="button"
                    @click="insertFormat('link')"
                    class="p-1.5 rounded hover:bg-bg-card text-text-muted hover:text-text-base transition-colors"
                    title="Link"
                >
                    <Link class="w-4 h-4" />
                </button>
            </div>

            <!-- Textarea -->
            <textarea
                ref="textareaRef"
                :value="modelValue"
                @input="updateValue"
                :rows="rows"
                :placeholder="placeholder"
                class="w-full p-3 bg-bg-card text-text-base placeholder-text-muted focus:outline-none font-mono text-sm resize-y block"
            ></textarea>
        </div>

        <p v-if="error" class="text-xs text-red-500">{{ error }}</p>
    </div>
</template>
