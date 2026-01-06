<script setup>
import { Briefcase, MapPin, Calendar } from "lucide-vue-next";

defineProps({
    experiences: {
        type: Array,
        required: true,
    },
});

const formatDate = (dateString, isCurrent) => {
    if (isCurrent) return "Present";
    if (!dateString) return "";
    const options = { year: "numeric", month: "short" };
    return new Date(dateString).toLocaleDateString(undefined, options);
};
</script>

<template>
    <div class="relative space-y-8 pl-8 sm:pl-0 sm:space-y-12">
        <!-- Timeline Line (vertical) -->
        <div
            class="absolute left-3 sm:left-1/2 top-0 bottom-0 w-0.5 bg-border-base transform sm:-translate-x-1/2"
        ></div>

        <div
            v-for="(exp, index) in experiences"
            :key="exp.id"
            class="relative flex flex-col sm:flex-row items-start sm:items-center group"
            :class="{ 'sm:flex-row-reverse': index % 2 !== 0 }"
        >
            <!-- Timeline Dot -->
            <div
                class="absolute left-3 sm:left-1/2 w-4 h-4 rounded-full border-2 border-primary bg-bg-base transform -translate-x-1/2 mt-1.5 sm:mt-0 z-10 group-hover:scale-125 transition-transform duration-300"
            ></div>

            <!-- Content Card -->
            <div
                class="w-full sm:w-[calc(50%-2rem)] pl-8 sm:pl-0"
                :class="{
                    'sm:pr-8 sm:text-right': index % 2 === 0,
                    'sm:pl-8': index % 2 !== 0,
                }"
            >
                <div
                    class="bg-bg-card p-5 rounded-lg border border-border-base shadow-sm hover:shadow-md hover:border-primary/50 transition-all duration-300"
                >
                    <h3 class="text-lg font-bold text-text-base">
                        {{ exp.position }}
                    </h3>
                    <div
                        class="text-primary font-medium mb-2 flex items-center gap-1"
                        :class="{ 'sm:justify-end': index % 2 === 0 }"
                    >
                        <Briefcase class="w-4 h-4" />
                        {{ exp.company }}
                    </div>

                    <div
                        class="flex flex-wrap gap-x-4 gap-y-1 text-sm text-text-muted mb-3"
                        :class="{ 'sm:justify-end': index % 2 === 0 }"
                    >
                        <div class="flex items-center gap-1">
                            <Calendar class="w-3.5 h-3.5" />
                            <span>
                                {{ formatDate(exp.start_date) }} -
                                {{ formatDate(exp.end_date, exp.is_current) }}
                            </span>
                        </div>
                        <div
                            class="flex items-center gap-1"
                            v-if="exp.location"
                        >
                            <MapPin class="w-3.5 h-3.5 shrink-0" />
                            <span>{{ exp.location }}</span>
                        </div>
                    </div>

                    <p class="text-sm text-text-muted whitespace-pre-line">
                        {{ exp.description }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>
