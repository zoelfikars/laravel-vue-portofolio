<script setup>
import { Github, Globe, Calendar } from "lucide-vue-next";
import { computed } from "vue";
import { RouterLink } from "vue-router";

const props = defineProps({
    project: {
        type: Object,
        required: true,
    },
});

const formatDate = (dateString) => {
    if (!dateString) return "";
    const options = { year: "numeric", month: "long", day: "numeric" };
    return new Date(dateString).toLocaleDateString(undefined, options);
};

// Map tech stack to colors if needed, or simple badges
</script>

<template>
    <div
        class="bg-bg-card border border-border-base rounded-lg overflow-hidden flex flex-col hover:border-primary transition-colors duration-300 group shadow-sm hover:shadow-md"
    >
        <!-- Thumbnail -->
        <RouterLink
            :to="`/projects/${project.slug}`"
            class="relative aspect-video bg-bg-base overflow-hidden block"
        >
            <img
                v-if="project.thumbnail_url"
                :src="project.thumbnail_url"
                :alt="project.title"
                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
            />
            <div
                v-else
                class="w-full h-full flex items-center justify-center text-text-muted"
            >
                No Image
            </div>
        </RouterLink>

        <!-- Content -->
        <div class="p-5 flex flex-col flex-1">
            <div class="mb-3 flex flex-wrap gap-2" v-if="project.tech_stack">
                <span
                    v-for="tech in project.tech_stack"
                    :key="tech"
                    class="px-2 py-0.5 rounded-full bg-primary/10 text-primary text-xs font-medium"
                >
                    {{ tech }}
                </span>
            </div>

            <RouterLink :to="`/projects/${project.slug}`">
                <h3
                    class="text-xl font-bold text-text-base mb-2 group-hover:text-primary transition-colors"
                >
                    {{ project.title }}
                </h3>
            </RouterLink>

            <p class="text-text-muted text-sm line-clamp-3 mb-4 flex-1">
                {{ project.description }}
            </p>

            <div
                class="flex items-center justify-between text-sm text-text-muted mt-auto pt-4 border-t border-border-base"
            >
                <div
                    class="flex items-center gap-1.5"
                    v-if="project.published_at"
                >
                    <Calendar class="w-4 h-4" />
                    <span>{{ formatDate(project.published_at) }}</span>
                </div>

                <div class="flex gap-3">
                    <a
                        v-if="project.repository_url"
                        :href="project.repository_url"
                        target="_blank"
                        class="hover:text-primary transition-colors"
                        title="Repository"
                    >
                        <Github class="w-4 h-4" />
                    </a>
                    <a
                        v-if="project.demo_url"
                        :href="project.demo_url"
                        target="_blank"
                        class="hover:text-primary transition-colors"
                        title="Live Demo"
                    >
                        <Globe class="w-4 h-4" />
                    </a>
                </div>
            </div>
        </div>
    </div>
</template>
