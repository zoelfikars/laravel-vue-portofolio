<script setup>
import { ref, watch } from "vue";
import { UploadCloud, FileText, Image as ImageIcon, X } from "lucide-vue-next";

const props = defineProps({
    modelValue: {
        type: [File, null],
        default: null,
    },
    label: {
        type: String,
        default: "",
    },
    accept: {
        type: String,
        default: "*",
    },
    error: {
        type: String,
        default: "",
    },
    previewUrl: {
        type: String,
        default: "",
    },
    id: {
        type: String,
        default: () => `file-upload-${Math.random().toString(36).substr(2, 9)}`,
    },
});

const emit = defineEmits(["update:modelValue"]);

const isDragging = ref(false);
const fileInput = ref(null);
const localPreview = ref(null);

const handleFileChange = (event) => {
    const file = event.target.files[0];
    processFile(file);
};

const handleDrop = (event) => {
    isDragging.value = false;
    const file = event.dataTransfer.files[0];
    processFile(file);
};

const processFile = (file) => {
    if (!file) return;

    // Create local preview if image
    if (file.type.startsWith("image/")) {
        const reader = new FileReader();
        reader.onload = (e) => {
            localPreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
    } else {
        localPreview.value = null;
    }

    emit("update:modelValue", file);
};

const triggerBrowse = () => {
    fileInput.value.click();
};

const removeFile = () => {
    emit("update:modelValue", null);
    localPreview.value = null;
    if (fileInput.value) {
        fileInput.value.value = "";
    }
};

// Initial setup from previewUrl if no new file selected
watch(
    () => props.previewUrl,
    (newUrl) => {
        if (!props.modelValue && newUrl) {
            // We don't set localPreview here to avoid confusion between "new upload" and "existing file"
            // Logic handles displaying either localPreview OR previewUrl
        }
    },
    { immediate: true }
);
</script>

<template>
    <div>
        <label
            v-if="label"
            class="block text-sm font-medium text-text-muted mb-1"
        >
            {{ label }}
        </label>

        <div
            @dragover.prevent="isDragging = true"
            @dragleave.prevent="isDragging = false"
            @drop.prevent="handleDrop"
            class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-dashed rounded-md transition-colors duration-200 bg-bg-input"
            :class="[
                isDragging
                    ? 'border-primary bg-primary/5'
                    : 'border-border-base hover:border-primary/50',
                error ? 'border-red-500' : '',
            ]"
        >
            <div class="space-y-1 text-center w-full">
                <!-- Preview Area -->
                <div
                    v-if="localPreview || (previewUrl && !modelValue)"
                    class="mb-4 relative inline-block"
                >
                    <img
                        v-if="
                            localPreview ||
                            previewUrl.match(/\.(jpeg|jpg|gif|png)$/i) ||
                            accept.includes('image')
                        "
                        :src="localPreview || previewUrl"
                        class="mx-auto h-32 object-cover rounded-md shadow-sm"
                        alt="Preview"
                    />
                    <div
                        v-else
                        class="flex flex-col items-center justify-center h-32 w-full bg-bg-base rounded-md border border-border-base p-4"
                    >
                        <FileText class="h-12 w-12 text-text-muted" />
                        <span
                            class="text-xs text-text-muted mt-2 truncate max-w-[200px]"
                        >
                            {{ modelValue?.name || "Existing File" }}
                        </span>
                    </div>

                    <button
                        v-if="modelValue"
                        type="button"
                        @click.stop="removeFile"
                        class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600 focus:outline-none shadow-sm"
                    >
                        <X class="h-3 w-3" />
                    </button>

                    <div v-if="previewUrl && !modelValue" class="mt-2">
                        <a
                            :href="previewUrl"
                            target="_blank"
                            class="text-xs text-primary hover:underline"
                        >
                            View Current File
                        </a>
                    </div>
                </div>

                <div v-else>
                    <component
                        :is="accept.includes('image') ? ImageIcon : FileText"
                        class="mx-auto h-12 w-12 text-text-muted"
                    />
                </div>

                <div
                    v-if="!modelValue"
                    class="flex text-sm text-text-muted justify-center"
                >
                    <label
                        :for="id"
                        class="relative cursor-pointer bg-transparent rounded-md font-medium text-primary hover:text-primary-600 focus-within:outline-none"
                    >
                        <span>Upload a file</span>
                        <input
                            :id="id"
                            ref="fileInput"
                            name="file-upload"
                            type="file"
                            class="sr-only"
                            :accept="accept"
                            @change="handleFileChange"
                        />
                    </label>
                    <p class="pl-1">or drag and drop</p>
                </div>
                <p v-if="!modelValue" class="text-xs text-text-muted">
                    {{
                        accept === "image/*"
                            ? "PNG, JPG, GIF up to 2MB"
                            : "PDF up to 5MB"
                    }}
                </p>
                <div
                    v-if="modelValue"
                    class="text-sm text-text-base font-medium"
                >
                    {{ modelValue.name }}
                </div>
                <div v-if="modelValue" class="mt-2">
                    <button
                        type="button"
                        @click="triggerBrowse"
                        class="text-xs text-primary hover:underline"
                    >
                        Change file
                    </button>
                </div>
            </div>
        </div>
        <p v-if="error" class="mt-1 text-xs text-red-500">
            {{ error }}
        </p>
    </div>
</template>
