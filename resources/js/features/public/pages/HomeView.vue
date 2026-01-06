<script setup>
import { onMounted } from "vue";
import { usePortfolioStore } from "../../../stores/portfolioStore";
import {
    Github,
    Instagram,
    Linkedin,
    Mail,
    MapPin,
    Briefcase,
    Globe,
} from "lucide-vue-next";
import ExperienceTimeline from "../components/ExperienceTimeline.vue";
import ProjectCard from "../components/ProjectCard.vue";
import BaseSkeleton from "../../../components/BaseSkeleton.vue";

const store = usePortfolioStore();

onMounted(() => {
    store.fetchHome();
});
</script>

<template>
    <div
        class="container mx-auto px-4 py-8 sm:py-12 space-y-16 sm:space-y-24 max-w-5xl"
    >
        <!-- Loading State -->
        <div v-if="store.loading && !store.homeData" class="space-y-12">
            <BaseSkeleton class="h-64 w-full rounded-2xl" />
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-8">
                <BaseSkeleton class="h-48 w-full rounded-xl" />
                <BaseSkeleton class="h-48 w-full rounded-xl" />
            </div>
        </div>

        <div v-else-if="store.homeData">
            <!-- Hero Section -->
            <section
                class="flex flex-col-reverse sm:flex-row items-center gap-8 sm:gap-16"
            >
                <div class="flex-1 space-y-6 text-center sm:text-left">
                    <h1
                        class="text-4xl sm:text-5xl lg:text-6xl font-black text-text-base leading-tight"
                    >
                        Hi, I'm
                        <span class="text-primary">{{
                            store.homeData.full_name
                        }}</span>
                    </h1>
                    <p
                        class="text-lg sm:text-xl text-text-muted leading-relaxed max-w-2xl"
                    >
                        {{ store.homeData.summary }}
                    </p>

                    <div
                        class="flex flex-wrap items-center justify-center sm:justify-start gap-4 text-sm text-text-muted font-medium uppercase tracking-wide"
                    >
                        <div
                            class="flex items-center gap-2"
                            v-if="store.homeData.place_of_birth"
                        >
                            <MapPin class="w-4 h-4 text-primary" />
                            {{ store.homeData.place_of_birth }}
                        </div>
                        <div
                            class="flex items-center gap-2"
                            v-if="store.homeData.work_interest"
                        >
                            <Briefcase class="w-4 h-4 text-primary" />
                            {{ store.homeData.work_interest }}
                        </div>
                    </div>

                    <div
                        class="flex flex-wrap items-center justify-center sm:justify-start gap-4 pt-2"
                    >
                        <!-- Socials -->
                        <a
                            v-for="contact in store.homeData.contacts"
                            :key="contact.id"
                            :href="contact.url"
                            target="_blank"
                            class="p-2 bg-bg-card border border-border-base rounded-full text-text-muted hover:text-primary hover:border-primary transition-all shadow-sm hover:shadow-md"
                        >
                            <!-- Simple icon mapping needed or just generic -->
                            <Globe
                                v-if="contact.platform === 'website'"
                                class="w-5 h-5"
                            />
                            <Github
                                v-else-if="contact.platform === 'github'"
                                class="w-5 h-5"
                            />
                            <Linkedin
                                v-else-if="contact.platform === 'linkedin'"
                                class="w-5 h-5"
                            />
                            <Instagram
                                v-else-if="contact.platform === 'instagram'"
                                class="w-5 h-5"
                            />
                            <Mail
                                v-else-if="contact.platform === 'email'"
                                class="w-5 h-5"
                            />
                            <!-- Fallback -->
                            <span v-else class="text-xs font-bold px-1">{{
                                contact.platform[0]
                            }}</span>
                        </a>
                        <!-- CV Download -->
                        <a
                            v-if="store.homeData.cv_url"
                            :href="store.homeData.cv_url"
                            target="_blank"
                            class="inline-flex items-center justify-center px-6 py-2 border border-transparent text-base font-medium rounded-full text-primary-foreground bg-primary hover:bg-primary/90 transition-colors shadow-lg hover:shadow-xl"
                        >
                            Download CV
                        </a>
                    </div>
                </div>

                <!-- Profile Photo -->
                <div class="relative w-48 h-48 sm:w-64 sm:h-64 flex-shrink-0">
                    <div
                        class="absolute inset-0 bg-primary/20 rounded-full blur-2xl transform translate-x-4 translate-y-4"
                    ></div>
                    <img
                        v-if="store.homeData.photo_url"
                        :src="store.homeData.photo_url"
                        alt="Profile Photo"
                        class="relative w-full h-full object-cover rounded-full border-4 border-bg-card shadow-2xl"
                    />
                    <div
                        v-else
                        class="relative w-full h-full bg-bg-input rounded-full flex items-center justify-center text-text-muted border-4 border-bg-card shadow-2xl"
                    >
                        No Photo
                    </div>
                </div>
            </section>

            <!-- Skills & Hobbies -->
            <section class="grid grid-cols-1 sm:grid-cols-2 gap-12 mt-4">
                <div v-if="store.homeData.skills?.length">
                    <h2 class="text-2xl font-bold text-text-base mb-6">
                        Skills
                    </h2>
                    <div class="flex flex-wrap gap-3">
                        <span
                            v-for="skill in store.homeData.skills"
                            :key="skill"
                            class="px-4 py-2 bg-bg-card border border-border-base rounded-lg text-text-base text-sm font-medium hover:border-primary transition-colors cursor-default"
                        >
                            {{ skill }}
                        </span>
                    </div>
                </div>
                <div v-if="store.homeData.hobbies?.length">
                    <h2 class="text-2xl font-bold text-text-base mb-6">
                        Hobbies
                    </h2>
                    <div class="flex flex-wrap gap-3">
                        <span
                            v-for="hobby in store.homeData.hobbies"
                            :key="hobby"
                            class="px-4 py-2 bg-bg-input border border-border-base rounded-full text-text-muted text-sm"
                        >
                            {{ hobby }}
                        </span>
                    </div>
                </div>
            </section>

            <!-- Latest Experience -->
            <section class="mt-4" v-if="store.homeData.experiences?.length">
                <div class="flex items-center justify-between mb-8">
                    <h2 class="text-3xl font-bold text-text-base">
                        Experience
                    </h2>
                    <RouterLink
                        to="/experiences"
                        class="text-primary font-medium hover:underline decoration-2 underline-offset-4"
                        >View All</RouterLink
                    >
                </div>
                <ExperienceTimeline
                    :experiences="store.homeData.experiences.slice(0, 3)"
                />
            </section>

            <!-- Recent Projects -->
            <section class="mt-4" v-if="store.homeData.projects?.length">
                <div class="flex items-center justify-between mb-8">
                    <h2 class="text-3xl font-bold text-text-base">
                        Recent Projects
                    </h2>
                    <RouterLink
                        to="/projects"
                        class="text-primary font-medium hover:underline decoration-2 underline-offset-4"
                        >View All</RouterLink
                    >
                </div>
                <div
                    class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8"
                >
                    <ProjectCard
                        v-for="project in store.homeData.projects.slice(0, 3)"
                        :key="project.id"
                        :project="project"
                    />
                </div>
            </section>
        </div>
    </div>
</template>
