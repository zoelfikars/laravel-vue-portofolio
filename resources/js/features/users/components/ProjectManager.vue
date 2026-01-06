<script setup>
import { ref, onMounted, watch } from "vue";
import { useUserStore } from "../stores/userStore";
import {
    Plus,
    Edit2,
    Trash2,
    Globe,
    Github,
    Box,
    AlertCircle,
} from "lucide-vue-next";
import BaseButton from "../../../components/BaseButton.vue";
import BaseSkeleton from "../../../components/BaseSkeleton.vue";
import BaseModal from "../../../components/BaseModal.vue";
import BaseInput from "../../../components/BaseInput.vue";
import BaseSearchInput from "../../../components/BaseSearchInput.vue";
import ProjectForm from "./ProjectForm.vue";

const props = defineProps({
    userId: { type: String, required: true },
});

const userStore = useUserStore();
const projects = ref([]);
const isLoading = ref(false);
const viewMode = ref("list"); // 'list' | 'form'
const editingProject = ref(null);
const search = ref("");
let searchTimeout = null;

// Delete Modal State
const isDeleteModalOpen = ref(false);
const projectToDelete = ref(null);

const fetchProjects = async () => {
    isLoading.value = true;
    projects.value = await userStore.fetchProjects(props.userId, {
        search: search.value,
    });
    isLoading.value = false;
};

// Debounce search
watch(search, () => {
    if (searchTimeout) clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        fetchProjects();
    }, 500);
});

const openAddForm = () => {
    editingProject.value = null;
    userStore.errors = {};
    viewMode.value = "form";
};

const openEditForm = (project) => {
    editingProject.value = project;
    userStore.errors = {};
    viewMode.value = "form";
};

const closeForm = () => {
    viewMode.value = "list";
    editingProject.value = null;
};

const handleSuccess = async () => {
    await fetchProjects();
    closeForm();
};

const confirmDelete = (project) => {
    projectToDelete.value = project;
    isDeleteModalOpen.value = true;
};

const handleDelete = async () => {
    if (!projectToDelete.value) return;

    const success = await userStore.deleteProject(projectToDelete.value.id);
    if (success) {
        await fetchProjects();
        isDeleteModalOpen.value = false;
        projectToDelete.value = null;
    }
};

const cancelDelete = () => {
    isDeleteModalOpen.value = false;
    projectToDelete.value = null;
};

onMounted(() => {
    fetchProjects();
});
</script>

<template>
    <div class="space-y-4">
        <!-- List Mode -->
        <div v-if="viewMode === 'list'">
            <div
                class="flex flex-col sm:flex-row justify-between items-center mb-4 gap-4"
            >
                <h3 class="text-lg font-medium text-text-base">
                    Portfolio Projects
                </h3>
                <div class="flex gap-2 w-full sm:w-auto">
                    <BaseSearchInput
                        v-model="search"
                        placeholder="Search projects..."
                    />

                    <BaseButton size="sm" @click="openAddForm" class="shrink-0">
                        <Plus class="w-4 h-4 sm:mr-1" />
                        <span class="hidden sm:inline">Add Project</span>
                    </BaseButton>
                </div>
            </div>

            <div v-if="isLoading" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <BaseSkeleton height="12rem" />
                <BaseSkeleton height="12rem" />
            </div>

            <div
                v-else-if="projects.length === 0"
                class="text-center py-12 text-text-muted bg-bg-base/50 rounded-lg border border-border-base border-dashed"
            >
                <Box class="w-10 h-10 mx-auto mb-2 opacity-50" />
                <p>No projects showcased yet.</p>
            </div>

            <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div
                    v-for="project in projects"
                    :key="project.id"
                    class="bg-bg-card border border-border-base rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-shadow group"
                >
                    <!-- Header / Thumbnail -->
                    <div class="h-32 bg-bg-base/50 relative overflow-hidden">
                        <img
                            v-if="project.thumbnail_url"
                            :src="project.thumbnail_url"
                            alt="Thumbnail"
                            class="w-full h-full object-cover"
                        />
                        <div
                            v-else
                            class="w-full h-full flex items-center justify-center text-text-muted"
                        >
                            <Box class="w-8 h-8 opacity-20" />
                        </div>

                        <div class="absolute top-2 right-2">
                            <span
                                class="px-2 py-0.5 text-xs rounded-full border shadow-sm font-medium"
                                :class="
                                    project.is_published
                                        ? 'bg-green-100 text-green-700 border-green-200'
                                        : 'bg-gray-100 text-gray-600 border-gray-200'
                                "
                            >
                                {{
                                    project.is_published ? "Published" : "Draft"
                                }}
                            </span>
                        </div>
                    </div>

                    <!-- Body -->
                    <div class="p-4">
                        <h4
                            class="font-semibold text-text-base truncate"
                            :title="project.title"
                        >
                            {{ project.title }}
                        </h4>

                        <!-- Tech Stack Badges -->
                        <div
                            class="flex flex-wrap gap-1 mt-2 h-6 overflow-hidden"
                        >
                            <span
                                v-for="tech in project.tech_stack.slice(0, 3)"
                                :key="tech"
                                class="px-1.5 py-0.5 text-[10px] rounded bg-bg-base text-text-muted border border-border-base"
                            >
                                {{ tech }}
                            </span>
                            <span
                                v-if="project.tech_stack.length > 3"
                                class="text-[10px] text-text-muted pt-0.5"
                            >
                                +{{ project.tech_stack.length - 3 }} more
                            </span>
                        </div>

                        <!-- Actions -->
                        <div
                            class="flex justify-between items-center mt-4 pt-3 border-t border-border-base"
                        >
                            <div class="flex space-x-2">
                                <a
                                    v-if="project.demo_url"
                                    :href="project.demo_url"
                                    target="_blank"
                                    class="text-text-muted hover:text-primary transition-colors"
                                    title="Demo"
                                >
                                    <Globe class="w-4 h-4" />
                                </a>
                                <a
                                    v-if="project.repository_url"
                                    :href="project.repository_url"
                                    target="_blank"
                                    class="text-text-muted hover:text-primary transition-colors"
                                    title="Repository"
                                >
                                    <Github class="w-4 h-4" />
                                </a>
                            </div>

                            <div
                                class="flex space-x-1 opacity-0 group-hover:opacity-100 transition-opacity"
                            >
                                <button
                                    type="button"
                                    @click="openEditForm(project)"
                                    class="p-1.5 text-text-muted hover:text-primary hover:bg-bg-base rounded transition-colors"
                                >
                                    <Edit2 class="w-4 h-4" />
                                </button>
                                <button
                                    type="button"
                                    @click="confirmDelete(project)"
                                    class="p-1.5 text-text-muted hover:text-red-500 hover:bg-bg-base rounded transition-colors"
                                >
                                    <Trash2 class="w-4 h-4" />
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Mode -->
        <div v-else class="bg-bg-card p-4 rounded-lg border border-border-base">
            <ProjectForm
                :userId="userId"
                :project="editingProject"
                @success="handleSuccess"
                @cancel="closeForm"
            />
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <BaseModal
        :isOpen="isDeleteModalOpen"
        title="Delete Project"
        maxWidth="sm:max-w-md"
        @close="cancelDelete"
    >
        <div class="flex flex-col items-center text-center p-4">
            <div
                class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center mb-4 text-red-600"
            >
                <AlertCircle class="w-6 h-6" />
            </div>
            <h3 class="text-lg font-medium text-text-base mb-2">
                Are you sure?
            </h3>
            <p class="text-text-muted mb-6">
                Do you really want to delete this project? This process cannot
                be undone and will delete all associated data including the
                thumbnail.
            </p>
            <div
                v-if="projectToDelete"
                class="flex items-center gap-2 p-2 bg-bg-base rounded-md mb-6 w-full justify-center text-sm font-medium text-text-base border border-border-base"
            >
                {{ projectToDelete.title }}
            </div>
        </div>

        <template #footer>
            <div class="flex w-full gap-3">
                <BaseButton
                    type="button"
                    variant="secondary"
                    class="flex-1"
                    @click="cancelDelete"
                >
                    Cancel
                </BaseButton>
                <BaseButton
                    type="button"
                    variant="danger"
                    class="flex-1"
                    @click="handleDelete"
                    :isLoading="userStore.isLoading"
                >
                    Delete
                </BaseButton>
            </div>
        </template>
    </BaseModal>
</template>
