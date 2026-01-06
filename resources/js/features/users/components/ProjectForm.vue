<script setup>
import { reactive, ref, onMounted } from "vue";
import { useUserStore } from "../stores/userStore";
import BaseInput from "../../../components/BaseInput.vue";
import BaseMarkdownEditor from "../../../components/BaseMarkdownEditor.vue"; // Adjust path as needed
import BaseFileUpload from "../../../components/BaseFileUpload.vue";
import BaseTagInput from "../../../components/BaseTagInput.vue";
import BaseDateInput from "../../../components/BaseDateInput.vue";
import BaseButton from "../../../components/BaseButton.vue";
import BaseTextarea from "../../../components/BaseTextarea.vue";

const props = defineProps({
    userId: { type: String, required: true },
    project: { type: Object, default: null },
});

const emit = defineEmits(["success", "cancel"]);

const userStore = useUserStore();
const form = reactive({
    title: "",
    thumbnail: null,
    tech_stack: [],
    demo_url: "",
    repository_url: "",
    published_at: "",
    content: "",
});

const existingThumbnailUrl = ref("");

onMounted(() => {
    if (props.project) {
        form.title = props.project.title;
        // Thumbnail handling: store usually doesn't return file object back, just URL
        existingThumbnailUrl.value = props.project.thumbnail_url;
        // Tech stack: backend returns array of strings (names) via Resource
        form.tech_stack = props.project.tech_stack || [];
        form.demo_url = props.project.demo_url;
        form.description = props.project.description;
        form.repository_url = props.project.repository_url;
        // published_at might need formatting if BaseDateInput expects YYYY-MM-DD
        form.published_at = props.project.published_at
            ? props.project.published_at.split("T")[0]
            : "";
        form.content = props.project.content;
    }
});

const submit = async () => {
    const formData = new FormData();
    formData.append("title", form.title);
    if (form.thumbnail) {
        formData.append("thumbnail", form.thumbnail);
    }

    // Append tech_stack array
    form.tech_stack.forEach((tech, index) => {
        formData.append(`tech_stack[${index}]`, tech);
    });

    if (form.demo_url) formData.append("demo_url", form.demo_url);
    if (form.repository_url)
        formData.append("repository_url", form.repository_url);

    if (form.published_at) {
        formData.append("published_at", form.published_at);
    } // If empty, it's draft (null), creating service handles missing key as null or we should explicitly send null?
    // Usually Laravel validation 'nullable' handles empty string as null if using ConvertEmptyStringsToNull middleware,
    // but explicitly:
    // Since FormData processes everything as strings, empty string usually becomes null on server if configured.

    if (form.content) formData.append("content", form.content);
    if (form.description) formData.append("description", form.description);

    let success;
    if (props.project) {
        success = await userStore.updateProject(props.project.id, formData);
    } else {
        success = await userStore.createProject(props.userId, formData);
    }

    if (success) {
        emit("success");
    }
};
</script>

<template>
    <form @submit.prevent="submit" class="space-y-6">
        <h3 class="text-lg font-medium text-text-base mb-4">
            {{ project ? "Edit Project" : "Add New Project" }}
        </h3>

        <!-- Title -->
        <BaseInput
            v-model="form.title"
            label="Project Title"
            placeholder="e.g. E-Commerce Platform"
            :error="userStore.errors.title?.[0]"
        />

        <!-- Thumbnail -->
        <BaseFileUpload
            v-model="form.thumbnail"
            label="Project Thumbnail"
            accept="image/*"
            :previewUrl="existingThumbnailUrl"
            :error="userStore.errors.thumbnail?.[0]"
        />

        <!-- Tech Stack -->
        <BaseTagInput
            v-model="form.tech_stack"
            label="Tech Stack (Skills)"
            fetchUrl="/api/skills"
            placeholder="e.g. Laravel, Vue.js, MySQL"
            :error="userStore.errors.tech_stack?.[0]"
        />

        <!-- URLs -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <BaseInput
                v-model="form.demo_url"
                label="Demo URL"
                placeholder="https://..."
                :error="userStore.errors.demo_url?.[0]"
            />
            <BaseInput
                v-model="form.repository_url"
                label="Repository URL"
                placeholder="https://github.com/..."
                :error="userStore.errors.repository_url?.[0]"
            />
        </div>

        <!-- Published Date -->
        <BaseDateInput
            v-model="form.published_at"
            label="Published At (Leave empty for Draft)"
            :error="userStore.errors.published_at?.[0]"
        />

        <!-- Description -->
        <BaseTextarea
            v-model="form.description"
            label="Project Description"
            :error="userStore.errors.description?.[0]"
        />

        <!-- Content (Markdown) -->
        <div class="space-y-1">
            <BaseMarkdownEditor
                v-model="form.content"
                label="Case Study Content"
                placeholder="# Project Overview..."
                :rows="15"
                :error="userStore.errors.content?.[0]"
            />
            <p class="text-xs text-text-muted">
                Use the toolbar to format your markdown content.
            </p>
        </div>

        <!-- Actions -->
        <div class="flex justify-end gap-3 pt-4 border-t border-border-base">
            <BaseButton
                type="button"
                variant="secondary"
                @click="$emit('cancel')"
            >
                Cancel
            </BaseButton>
            <BaseButton
                type="submit"
                variant="primary"
                :isLoading="userStore.isLoading"
            >
                Save Project
            </BaseButton>
        </div>
    </form>
</template>
