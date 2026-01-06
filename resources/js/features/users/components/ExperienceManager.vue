<script setup>
import { ref, onMounted, reactive, computed } from "vue";
import { useUserStore } from "../stores/userStore";
import {
    Briefcase,
    MapPin,
    Plus,
    Edit2,
    Trash2,
    Calendar,
} from "lucide-vue-next";
import BaseButton from "../../../components/BaseButton.vue";
import BaseInput from "../../../components/BaseInput.vue";
import BaseDateInput from "../../../components/BaseDateInput.vue";
import BaseToggle from "../../../components/BaseToggle.vue";
import BaseTextarea from "../../../components/BaseTextarea.vue";
import BaseSkeleton from "../../../components/BaseSkeleton.vue";

const props = defineProps({
    userId: {
        type: String,
        required: true,
    },
});

const userStore = useUserStore();
const experiences = ref([]);
const isLoading = ref(false);
const viewMode = ref("list"); // 'list' | 'form'
const isEditing = ref(false);

const form = reactive({
    id: null,
    company: "",
    position: "",
    location: "",
    start_date: "",
    end_date: "",
    is_current: false,
    description: "",
});

const fetchExperiences = async () => {
    isLoading.value = true;
    experiences.value = await userStore.fetchExperiences(props.userId);
    isLoading.value = false;
};

const formatDate = (dateString) => {
    if (!dateString) return "";
    const date = new Date(dateString);
    return new Intl.DateTimeFormat("en-US", {
        month: "short",
        year: "numeric",
    }).format(date);
};

const resetForm = () => {
    form.id = null;
    form.company = "";
    form.position = "";
    form.location = "";
    form.start_date = "";
    form.end_date = "";
    form.is_current = false;
    form.description = "";
    userStore.errors = {};
};

const openAddForm = () => {
    resetForm();
    isEditing.value = false;
    viewMode.value = "form";
};

const openEditForm = (exp) => {
    resetForm();
    form.id = exp.id;
    form.company = exp.company;
    form.position = exp.position;
    form.location = exp.location;
    form.start_date = exp.start_date;
    form.end_date = exp.end_date;
    form.is_current = !!exp.is_current;
    form.description = exp.description;
    isEditing.value = true;
    viewMode.value = "form";
};

const closeForm = () => {
    viewMode.value = "list";
    userStore.errors = {};
};

const saveExperience = async () => {
    const payload = { ...form };
    let success = false;

    if (isEditing.value) {
        success = await userStore.updateExperience(form.id, payload);
    } else {
        success = await userStore.addExperience(props.userId, payload);
    }

    if (success) {
        await fetchExperiences();
        closeForm();
    }
};

const deleteExperience = async (id) => {
    if (confirm("Are you sure you want to delete this experience?")) {
        const success = await userStore.deleteExperience(id);
        if (success) {
            await fetchExperiences();
        }
    }
};

onMounted(() => {
    fetchExperiences();
});
</script>

<template>
    <div class="space-y-4">
        <!-- Header / Add Button -->
        <div
            v-if="viewMode === 'list'"
            class="flex justify-between items-center mb-4"
        >
            <h3 class="text-lg font-medium text-text-base">Work Experience</h3>
            <BaseButton size="sm" @click="openAddForm">
                <Plus class="w-4 h-4 mr-1" /> Add Experience
            </BaseButton>
        </div>

        <!-- Loading State -->
        <div v-if="isLoading" class="space-y-3">
            <BaseSkeleton height="4rem" />
            <BaseSkeleton height="4rem" />
        </div>

        <!-- List Mode -->
        <div v-else-if="viewMode === 'list'">
            <div
                v-if="experiences.length === 0"
                class="text-center py-8 text-text-muted bg-bg-base/50 rounded-lg border border-border-base border-dashed"
            >
                <Briefcase class="w-8 h-8 mx-auto mb-2 opacity-50" />
                <p>No work experience added yet.</p>
            </div>

            <div class="relative border-l border-border-base ml-3 space-y-6">
                <div
                    v-for="exp in experiences"
                    :key="exp.id"
                    class="ml-6 relative"
                >
                    <!-- Dot -->
                    <span
                        class="absolute -left-[31px] mt-1.5 h-4 w-4 rounded-full border-2 border-primary bg-bg-card"
                    ></span>

                    <div
                        class="bg-bg-card border border-border-base rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow duration-200"
                    >
                        <div class="flex justify-between items-start">
                            <div>
                                <h4
                                    class="text-md font-semibold text-text-base"
                                >
                                    {{ exp.position }}
                                </h4>
                                <div class="text-sm font-medium text-primary">
                                    {{ exp.company }}
                                </div>
                            </div>
                            <div class="flex space-x-1">
                                <button
                                    @click="openEditForm(exp)"
                                    class="p-1 text-text-muted hover:text-primary transition-colors"
                                >
                                    <Edit2 class="w-4 h-4" />
                                </button>
                                <button
                                    @click="deleteExperience(exp.id)"
                                    class="p-1 text-text-muted hover:text-red-500 transition-colors"
                                >
                                    <Trash2 class="w-4 h-4" />
                                </button>
                            </div>
                        </div>

                        <div
                            class="mt-2 flex items-center text-xs text-text-muted space-x-4"
                        >
                            <span class="flex items-center">
                                <Calendar class="w-3.5 h-3.5 mr-1" />
                                {{ formatDate(exp.start_date) }} -
                                {{
                                    exp.is_current
                                        ? "Present"
                                        : formatDate(exp.end_date)
                                }}
                            </span>
                            <span v-if="exp.location" class="flex items-center">
                                <MapPin class="w-3.5 h-3.5 mr-1" />
                                {{ exp.location }}
                            </span>
                        </div>

                        <p
                            v-if="exp.description"
                            class="mt-2 text-sm text-text-muted whitespace-pre-line"
                        >
                            {{ exp.description }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Mode -->
        <div
            v-else-if="viewMode === 'form'"
            class="bg-bg-base/30 p-4 rounded-lg border border-border-base"
        >
            <h3 class="text-lg font-medium text-text-base mb-4">
                {{ isEditing ? "Edit Experience" : "Add Experience" }}
            </h3>

            <form @submit.prevent="saveExperience" class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <BaseInput
                        v-model="form.position"
                        label="Position"
                        placeholder="e.g. Senior Developer"
                        :error="userStore.errors.position?.[0]"
                    />
                    <BaseInput
                        v-model="form.company"
                        label="Company"
                        placeholder="e.g. Acme Corp"
                        :error="userStore.errors.company?.[0]"
                    />
                </div>

                <BaseInput
                    v-model="form.location"
                    label="Location"
                    placeholder="e.g. New York, Remote"
                    :error="userStore.errors.location?.[0]"
                />

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <BaseDateInput
                        v-model="form.start_date"
                        label="Start Date"
                        :error="userStore.errors.start_date?.[0]"
                    />

                    <div v-if="!form.is_current">
                        <BaseDateInput
                            v-model="form.end_date"
                            label="End Date"
                            :error="userStore.errors.end_date?.[0]"
                        />
                    </div>
                    <div
                        v-else
                        class="flex items-center h-full pt-6 text-sm text-text-muted"
                    >
                        Present
                    </div>
                </div>

                <BaseToggle
                    v-model="form.is_current"
                    label="I currently work here"
                />

                <BaseTextarea
                    v-model="form.description"
                    label="Description"
                    :rows="3"
                    placeholder="Describe your responsibilities..."
                    :error="userStore.errors.description?.[0]"
                />

                <div class="flex justify-end space-x-3 pt-2">
                    <BaseButton
                        type="button"
                        variant="secondary"
                        @click="closeForm"
                        >Cancel</BaseButton
                    >
                    <BaseButton
                        type="submit"
                        variant="primary"
                        :isLoading="userStore.isLoading"
                        >Save</BaseButton
                    >
                </div>
            </form>
        </div>
    </div>
</template>
