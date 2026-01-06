<script setup>
import { onMounted, ref, watch } from "vue";
import { usePortfolioStore } from "../../../stores/portfolioStore";
import ProjectCard from "../components/ProjectCard.vue";
import BaseSearchInput from "../../../components/BaseSearchInput.vue";
import BaseSkeleton from "../../../components/BaseSkeleton.vue";
import BaseButton from "../../../components/BaseButton.vue";
import { ChevronLeft, ChevronRight } from "lucide-vue-next";

const store = usePortfolioStore();
const search = ref("");
const page = ref(1);
let searchTimeout = null;

const fetchProjects = () => {
    store.fetchProjects({
        search: search.value,
        page: page.value,
    });
};

onMounted(() => {
    fetchProjects();
});

watch(search, () => {
    if (searchTimeout) clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        page.value = 1; // Reset to page 1 on search
        fetchProjects();
    }, 500); // Debounce 500ms
});

const changePage = (newPage) => {
    if (newPage < 1 || newPage > (store.projectsPagination?.last_page || 1))
        return;
    page.value = newPage;
    fetchProjects();
    window.scrollTo({ top: 0, behavior: "smooth" });
};
</script>

<template>
    <div class="container mx-auto px-4 py-8 sm:py-12 max-w-5xl">
        <header class="mb-12 text-center sm:text-left">
            <h1 class="text-4xl font-bold text-text-base mb-4">Portfolio</h1>
            <p class="text-text-muted mb-8 max-w-2xl">
                A showcase of my recent projects, experiments, and open-source
                contributions.
            </p>

            <div class="w-full sm:w-72">
                <BaseSearchInput
                    v-model="search"
                    placeholder="Search projects..."
                />
            </div>
        </header>

        <!-- Loading -->
        <div
            v-if="store.loading && !store.projects.length"
            class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8"
        >
            <BaseSkeleton
                v-for="i in 6"
                :key="i"
                class="h-80 w-full rounded-lg"
            />
        </div>

        <!-- Projects Grid -->
        <div
            v-else-if="store.projects.length"
            class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8"
        >
            <ProjectCard
                v-for="project in store.projects"
                :key="project.id"
                :project="project"
            />
        </div>

        <!-- Empty State -->
        <div v-else class="text-center py-24 text-text-muted">
            <p class="text-xl">No projects found.</p>
        </div>

        <!-- Pagination -->
        <div
            v-if="
                store.projectsPagination &&
                store.projectsPagination.last_page > 1
            "
            class="flex justify-center items-center gap-4 mt-12"
        >
            <BaseButton
                variant="neutral"
                :disabled="page === 1"
                @click="changePage(page - 1)"
            >
                <ChevronLeft class="w-4 h-4 mr-1" /> Previous
            </BaseButton>

            <span class="text-text-muted text-sm">
                Page {{ page }} of {{ store.projectsPagination.last_page }}
            </span>

            <BaseButton
                variant="neutral"
                :disabled="page === store.projectsPagination.last_page"
                @click="changePage(page + 1)"
            >
                Next <ChevronRight class="w-4 h-4 ml-1" />
            </BaseButton>
        </div>
    </div>
</template>
