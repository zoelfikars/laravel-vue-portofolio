<script setup>
import { onMounted, ref, computed } from "vue";
import { usePortfolioStore } from "../../../stores/portfolioStore";
import ExperienceTimeline from "../components/ExperienceTimeline.vue";
import BaseSearchInput from "../../../components/BaseSearchInput.vue";
import BaseSkeleton from "../../../components/BaseSkeleton.vue";

const store = usePortfolioStore();
const search = ref("");

onMounted(() => {
    store.fetchExperiences();
});

const filteredExperiences = computed(() => {
    if (!search.value) return store.experiences;
    const q = search.value.toLowerCase();
    return store.experiences.filter(
        (exp) =>
            exp.company.toLowerCase().includes(q) ||
            exp.position.toLowerCase().includes(q) ||
            exp.description?.toLowerCase().includes(q)
    );
});
</script>

<template>
    <div class="container mx-auto px-4 py-8 sm:py-12 max-w-5xl">
        <header class="mb-12 text-center sm:text-left">
            <h1 class="text-4xl font-bold text-text-base mb-4">
                Work Experience
            </h1>
            <p class="text-text-muted mb-8 max-w-2xl">
                My professional journey and career milestones.
            </p>

            <div class="w-full sm:w-72">
                <BaseSearchInput
                    v-model="search"
                    placeholder="Filter company, position..."
                />
            </div>
        </header>

        <div
            v-if="store.loading && !store.experiences.length"
            class="space-y-8"
        >
            <div v-for="i in 3" :key="i" class="flex gap-4">
                <BaseSkeleton class="w-4 h-full rounded-full" />
                <BaseSkeleton class="h-32 w-full rounded-xl" />
            </div>
        </div>

        <div v-else-if="filteredExperiences.length">
            <ExperienceTimeline :experiences="filteredExperiences" />
        </div>

        <div v-else class="text-center py-12 text-text-muted">
            No experiences found matching your search.
        </div>
    </div>
</template>
