<script setup>
import { ref, onMounted, onUnmounted } from "vue";

const props = defineProps({
    headings: {
        type: Array, // { id, text, level }
        required: true,
    },
});

const activeId = ref("");

// Intersection Observer to track active section
let observer = null;

const onIntersect = (entries) => {
    entries.forEach((entry) => {
        if (entry.isIntersecting && entry.intersectionRatio >= 0.1) {
            activeId.value = entry.target.id;
        }
    });
};

onMounted(() => {
    // Wait for DOM
    setTimeout(() => {
        const elements = props.headings.map((h) =>
            document.getElementById(h.id)
        );
        const validElements = elements.filter((el) => el);

        if (validElements.length) {
            observer = new IntersectionObserver(onIntersect, {
                rootMargin: "-10% 0px -80% 0px", // adjust trigger zone
                threshold: 0.1,
            });
            validElements.forEach((el) => observer.observe(el));
        }
    }, 500);
});

onUnmounted(() => {
    if (observer) observer.disconnect();
});

const handleClick = (id, event) => {
    event.preventDefault();
    const element = document.getElementById(id);
    if (element) {
        // Adjust standard scroll
        const y = element.getBoundingClientRect().top + window.scrollY - 80; // Offset for sticky header if any
        window.scrollTo({ top: y, behavior: "smooth" });
        activeId.value = id;
    }
};
</script>

<template>
    <nav class="space-y-1">
        <p
            class="font-bold text-text-base mb-3 uppercase text-sm tracking-wider"
        >
            On this page
        </p>
        <ul class="space-y-2 text-sm border-l border-border-base pl-4">
            <li v-for="heading in headings" :key="heading.id">
                <a
                    :href="`#${heading.id}`"
                    @click="handleClick(heading.id, $event)"
                    class="block transition-colors duration-200"
                    :class="[
                        activeId === heading.id
                            ? 'text-primary font-medium -ml-[17px] border-l-2 border-primary pl-4'
                            : 'text-text-muted hover:text-text-base',
                        heading.level === 3 ? 'pl-4' : '',
                    ]"
                >
                    {{ heading.text }}
                </a>
            </li>
        </ul>
    </nav>
</template>
