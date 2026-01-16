<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from "vue";
import {
    Calendar,
    ChevronLeft,
    ChevronRight,
    ChevronDown,
} from "lucide-vue-next";

const props = defineProps({
    modelValue: {
        type: String,
        default: "",
    },
    label: String,
    startYear: {
        type: Number,
        default: 1950,
    },
    endYear: {
        type: Number,
        default: new Date().getFullYear(),
    },
    error: String,
});

const emit = defineEmits(["update:modelValue"]);

// Parse initial Date or Default
const currentDate = ref(new Date());
const selectedDate = ref(null);

const isOpen = ref(false);
const containerRef = ref(null);

const months = [
    "January",
    "February",
    "March",
    "April",
    "May",
    "June",
    "July",
    "August",
    "September",
    "October",
    "November",
    "December",
];

const years = computed(() => {
    let y = [];
    for (let i = props.endYear; i >= props.startYear; i--) {
        y.push(i);
    }
    return y;
});

// View States
const currentMonth = ref(new Date().getMonth());
const currentYear = ref(new Date().getFullYear());

// Helpers
const daysInMonth = (month, year) => new Date(year, month + 1, 0).getDate();
const firstDayOfMonth = (month, year) => new Date(year, month, 1).getDay();

const calendarDays = computed(() => {
    const days = [];
    const totalDays = daysInMonth(currentMonth.value, currentYear.value);
    const firstDay = firstDayOfMonth(currentMonth.value, currentYear.value);

    // Padding for empty start days
    for (let i = 0; i < firstDay; i++) {
        days.push({ num: "", empty: true });
    }

    // Actual days
    for (let i = 1; i <= totalDays; i++) {
        const d = new Date(currentYear.value, currentMonth.value, i);
        // Compare with selected
        const isSelected =
            selectedDate.value &&
            d.getDate() === selectedDate.value.getDate() &&
            d.getMonth() === selectedDate.value.getMonth() &&
            d.getFullYear() === selectedDate.value.getFullYear();

        days.push({
            num: i,
            date: d,
            isSelected,
        });
    }
    return days;
});

const formattedDate = computed(() => {
    if (!selectedDate.value) return "";
    const y = selectedDate.value.getFullYear();
    const m = String(selectedDate.value.getMonth() + 1).padStart(2, "0");
    const d = String(selectedDate.value.getDate()).padStart(2, "0");
    return `${y}-${m}-${d}`;
});

const displayValue = computed(() => {
    if (!selectedDate.value) return "";
    return selectedDate.value.toLocaleDateString("id-ID", {
        day: "numeric",
        month: "long",
        year: "numeric",
    });
});

// Watch modelValue to sync
watch(
    () => props.modelValue,
    (newVal) => {
        if (newVal) {
            // Assume format YYYY-MM-DD
            const [y, m, d] = newVal.split("-").map(Number);
            const date = new Date(y, m - 1, d);
            selectedDate.value = date;
            currentMonth.value = m - 1;
            currentYear.value = y;
        } else {
            selectedDate.value = null;
        }
    },
    { immediate: true }
);

const selectDay = (day) => {
    if (day.empty) return;
    selectedDate.value = day.date;
    emit("update:modelValue", formattedDate.value);
    isOpen.value = false;
};

const changeMonth = (delta) => {
    let newM = currentMonth.value + delta;
    if (newM > 11) {
        newM = 0;
        currentYear.value++;
    } else if (newM < 0) {
        newM = 11;
        currentYear.value--;
    }
    currentMonth.value = newM;
};

// Click Outside
const handleClickOutside = (event) => {
    if (containerRef.value && !containerRef.value.contains(event.target)) {
        isOpen.value = false;
    }
};

onMounted(() => {
    document.addEventListener("click", handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener("click", handleClickOutside);
});

// Quick Selectors
const showYearPicker = ref(false);
const showMonthPicker = ref(false);

const quickSelectYear = (year) => {
    currentYear.value = year;
    showYearPicker.value = false;
};

const quickSelectMonth = (index) => {
    currentMonth.value = index;
    showMonthPicker.value = false;
};

const setToday = () => {
    const today = new Date();
    selectedDate.value = today;
    currentMonth.value = today.getMonth();
    currentYear.value = today.getFullYear();
    emit("update:modelValue", formattedDate.value);
    isOpen.value = false;
};

const clearDate = () => {
    selectedDate.value = null;
    emit("update:modelValue", "");
    isOpen.value = false;
};
</script>

<template>
    <div ref="containerRef" class="relative">
        <label
            v-if="label"
            class="block text-sm font-medium text-text-muted mb-1"
        >
            {{ label }}
        </label>

        <!-- Input Trigger -->
        <div
            @click="isOpen = !isOpen"
            class="flex items-center w-full px-3 py-2 bg-bg-input border border-border-base rounded-md cursor-pointer hover:border-primary focus:ring-2 focus:ring-primary focus:border-primary transition-colors duration-200"
            :class="error ? 'border-red-500' : ''"
        >
            <Calendar class="w-4 h-4 text-text-muted mr-2" />
            <span
                :class="displayValue ? 'text-text-base' : 'text-text-muted/50'"
            >
                {{ displayValue || "Select Date" }}
            </span>
        </div>
        <p v-if="error" class="mt-1 text-xs text-red-500">
            {{ error }}
        </p>

        <!-- Popup -->
        <div
            v-if="isOpen"
            class="absolute z-50 mt-1 w-72 bg-bg-card border border-border-base rounded-lg shadow-xl p-4"
        >
            <!-- Header Controls -->
            <div class="flex items-center justify-between mb-4">
                <button
                    type="button"
                    @click="changeMonth(-1)"
                    class="p-1 hover:bg-bg-base rounded"
                >
                    <ChevronLeft class="w-4 h-4 text-text-base" />
                </button>

                <div class="flex items-center gap-2">
                    <!-- Month Selector -->
                    <div class="relative">
                        <button
                            type="button"
                            @click="
                                showMonthPicker = !showMonthPicker;
                                showYearPicker = false;
                            "
                            class="text-sm font-semibold text-text-base hover:text-primary flex items-center"
                        >
                            {{ months[currentMonth] }}
                            <ChevronDown class="w-3 h-3 ml-1" />
                        </button>
                        <!-- Month Dropdown -->
                        <div
                            v-if="showMonthPicker"
                            class="absolute top-full left-0 mt-1 w-32 max-h-48 overflow-y-auto bg-bg-card border border-border-base rounded-md shadow-lg z-50 grid grid-cols-1"
                        >
                            <button
                                type="button"
                                v-for="(m, i) in months"
                                :key="m"
                                @click.stop="quickSelectMonth(i)"
                                class="text-xs text-left px-3 py-2 hover:bg-bg-base text-text-base"
                                :class="
                                    i === currentMonth
                                        ? 'bg-primary/10 text-primary'
                                        : ''
                                "
                            >
                                {{ m }}
                            </button>
                        </div>
                    </div>

                    <!-- Year Selector -->
                    <div class="relative">
                        <button
                            type="button"
                            @click="
                                showYearPicker = !showYearPicker;
                                showMonthPicker = false;
                            "
                            class="text-sm font-semibold text-text-base hover:text-primary flex items-center"
                        >
                            {{ currentYear }}
                            <ChevronDown class="w-3 h-3 ml-1" />
                        </button>
                        <!-- Year Dropdown -->
                        <div
                            v-if="showYearPicker"
                            class="absolute top-full right-0 mt-1 w-24 max-h-48 overflow-y-auto bg-bg-card border border-border-base rounded-md shadow-lg z-50 grid grid-cols-1 scrollbar-thin"
                        >
                            <button
                                type="button"
                                v-for="y in years"
                                :key="y"
                                @click.stop="quickSelectYear(y)"
                                class="text-xs text-left px-3 py-2 hover:bg-bg-base text-text-base"
                                :class="
                                    y === currentYear
                                        ? 'bg-primary/10 text-primary'
                                        : ''
                                "
                            >
                                {{ y }}
                            </button>
                        </div>
                    </div>
                </div>

                <button
                    type="button"
                    @click="changeMonth(1)"
                    class="p-1 hover:bg-bg-base rounded"
                >
                    <ChevronRight class="w-4 h-4 text-text-base" />
                </button>
            </div>

            <!-- Weekday Headers -->
            <div class="grid grid-cols-7 gap-1 mb-2 text-center">
                <span
                    v-for="d in ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa']"
                    :key="d"
                    class="text-xs font-bold text-text-muted"
                >
                    {{ d }}
                </span>
            </div>

            <!-- Days -->
            <div class="grid grid-cols-7 gap-1 mb-3">
                <button
                    type="button"
                    v-for="(day, index) in calendarDays"
                    :key="index"
                    :disabled="day.empty"
                    @click="selectDay(day)"
                    class="h-8 w-8 rounded-full flex items-center justify-center text-sm transition-colors text-text-base"
                    :class="[
                        day.empty ? 'invisible' : 'hover:bg-primary/20',
                        day.isSelected
                            ? 'bg-primary text-white hover:bg-primary'
                            : '',
                    ]"
                >
                    {{ day.num }}
                </button>
            </div>

            <!-- Footer Actions -->
            <div
                class="flex items-center justify-between pt-3"
            >
                <button
                    type="button"
                    @click="setToday"
                    class="text-xs font-medium text-primary hover:text-primary/80 transition-colors"
                >
                    Pick Today
                </button>
                <button
                    type="button"
                    @click="clearDate"
                    class="text-xs font-medium text-red-500 hover:text-red-400 transition-colors"
                >
                    Clear
                </button>
            </div>
        </div>
    </div>
</template>
