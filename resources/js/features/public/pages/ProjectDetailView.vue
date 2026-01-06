<script setup>
import { onMounted, ref, watch } from "vue";
import { useRoute } from "vue-router";
import { usePortfolioStore } from "../../../stores/portfolioStore";
import { marked } from "marked";
import hljs from "highlight.js";
import "highlight.js/styles/atom-one-dark.css"; // Dark theme that looks good in both modes
import { Github, Globe, Calendar, ArrowLeft } from "lucide-vue-next";
import TableOfContents from "../components/TableOfContents.vue";
import BaseSkeleton from "../../../components/BaseSkeleton.vue";

const route = useRoute();
const store = usePortfolioStore();
const contentHtml = ref("");
const headings = ref([]);

const props = defineProps(["slug"]);

const processContent = (markdown) => {
    if (!markdown) return "";

    const renderer = new marked.Renderer();

    renderer.heading = ({ tokens, depth }) => {
        const text = tokens.map((t) => t.raw).join("");
        const id = text
            .toLowerCase()
            .replace(/[^\w\s-]/g, "")
            .replace(/\s+/g, "-");

        if (depth === 2 || depth === 3) {
            headings.value.push({ id, text, level: depth });
        }

        return `<h${depth} id="${id}">${text}</h${depth}>`;
    };

    // Configure highlight.js
    marked.setOptions({
        highlight: function (code, lang) {
            const language = hljs.getLanguage(lang) ? lang : "plaintext";
            return hljs.highlight(code, { language }).value;
        },
        langPrefix: "hljs language-",
    });

    marked.use({ renderer });

    // Reset headings for new content
    headings.value = [];

    return marked.parse(markdown);
};

const fetchAndProcess = async () => {
    const slug = route.params.slug;
    if (slug) {
        await store.fetchProjectDetail(slug);
        if (store.currentProject) {
            contentHtml.value = processContent(store.currentProject.content);
        }
    }
};

onMounted(() => {
    fetchAndProcess();
});

watch(() => route.params.slug, fetchAndProcess);

const formatDate = (dateString) => {
    if (!dateString) return "";
    return new Date(dateString).toLocaleDateString(undefined, {
        year: "numeric",
        month: "long",
        day: "numeric",
    });
};
</script>

<template>
    <div class="container mx-auto px-4 py-8 sm:py-12 max-w-5xl">
        <div v-if="store.loading && !store.currentProject">
            <BaseSkeleton class="h-10 w-1/3 mb-8" />
            <BaseSkeleton class="h-96 w-full rounded-xl" />
        </div>

        <div v-else-if="store.currentProject" class="max-w-6xl mx-auto">
            <!-- Back Button -->
            <RouterLink
                to="/projects"
                class="inline-flex items-center text-sm text-text-muted hover:text-primary mb-8 transition-colors"
            >
                <ArrowLeft class="w-4 h-4 mr-1" /> Back to Projects
            </RouterLink>

            <!-- Header -->
            <header class="mb-12 text-center sm:text-left">
                <h1
                    class="text-4xl sm:text-5xl font-black text-text-base mb-6 leading-tight"
                >
                    {{ store.currentProject.title }}
                </h1>
                <p class="text-text-muted mb-8 max-w-2xl">
                    {{ store.currentProject.description }}
                </p>

                <div
                    class="flex flex-wrap items-center gap-6 text-sm text-text-muted mb-8"
                >
                    <span
                        v-if="store.currentProject.published_at"
                        class="flex items-center gap-1.5 bg-bg-card px-3 py-1.5 rounded-full border border-border-base"
                    >
                        <Calendar class="w-4 h-4" />
                        {{ formatDate(store.currentProject.published_at) }}
                    </span>
                    <!-- Tech Stack -->
                    <div class="flex flex-wrap gap-2">
                        <span
                            v-for="tech in store.currentProject.tech_stack"
                            :key="tech"
                            class="px-2.5 py-1 rounded-full bg-primary/10 text-primary font-medium"
                        >
                            {{ tech }}
                        </span>
                    </div>
                </div>

                <!-- Links/Buttons -->
                <div class="flex flex-wrap gap-4">
                    <a
                        v-if="store.currentProject.demo_url"
                        :href="store.currentProject.demo_url"
                        target="_blank"
                        class="inline-flex items-center px-5 py-2.5 rounded-full bg-primary text-primary-foreground font-medium hover:bg-primary/90 transition-all shadow-lg hover:shadow-xl"
                    >
                        <Globe class="w-5 h-5 mr-2" /> Live Demo
                    </a>
                    <a
                        v-if="store.currentProject.repository_url"
                        :href="store.currentProject.repository_url"
                        target="_blank"
                        class="inline-flex items-center px-5 py-2.5 rounded-full bg-bg-card border border-border-base text-text-base font-medium hover:border-primary hover:text-primary transition-all shadow-sm hover:shadow-md"
                    >
                        <Github class="w-5 h-5 mr-2" /> Repository
                    </a>
                </div>
            </header>

            <!-- Main Layout -->
            <div class="lg:grid lg:grid-cols-12 lg:gap-12">
                <!-- Content (Left) -->
                <div class="lg:col-span-8">
                    <!-- Thumbnail -->
                    <div
                        class="mb-10 rounded-xl overflow-hidden shadow-2xl border border-border-base bg-bg-base aspect-video relative"
                    >
                        <img
                            v-if="store.currentProject.thumbnail_url"
                            :src="store.currentProject.thumbnail_url"
                            :alt="store.currentProject.title"
                            class="w-full h-full object-cover"
                        />
                        <div
                            v-else
                            class="w-full h-full flex items-center justify-center text-text-muted"
                        >
                            No Thumbnail
                        </div>
                    </div>

                    <!-- Markdown Content -->
                    <article
                        class="prose prose-lg max-w-none prose-headings:scroll-mt-24 prose-headings:text-text-base prose-p:text-text-muted prose-lead:text-text-muted prose-strong:text-text-base prose-li:text-text-muted prose-ul:marker:text-primary prose-ol:marker:text-primary prose-blockquote:border-l-primary prose-blockquote:text-text-muted prose-blockquote:bg-bg-base/50 prose-blockquote:px-4 prose-blockquote:py-1 prose-blockquote:rounded-r-lg prose-a:text-primary hover:prose-a:underline prose-img:rounded-xl prose-pre:bg-[#282c34] prose-pre:border prose-pre:border-border-base prose-pre:p-0 prose-code:text-primary prose-code:font-mono prose-code:bg-primary/10 prose-code:px-1.5 prose-code:py-0.5 prose-code:rounded-md prose-code:before:content-none prose-code:after:content-none dark:prose-invert"
                        v-html="contentHtml"
                    ></article>
                </div>

                <!-- Sidebar (Right) -->
                <div class="lg:col-span-4 mt-12 lg:mt-0">
                    <div class="sticky top-24">
                        <TableOfContents
                            :headings="headings"
                            v-if="headings.length"
                        />
                    </div>
                </div>
            </div>
        </div>

        <div v-else class="text-center py-24">
            <h2 class="text-2xl font-bold text-text-base">Project Not Found</h2>
            <RouterLink to="/projects" class="text-primary mt-4 inline-block"
                >Return to Projects</RouterLink
            >
        </div>
    </div>
</template>
