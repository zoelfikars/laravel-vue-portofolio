<script setup>
import { ref, onMounted, reactive } from "vue";
import { useUserStore } from "../stores/userStore";
import BaseInput from "../../../components/BaseInput.vue";
import BaseTextarea from "../../../components/BaseTextarea.vue";
import BaseToggle from "../../../components/BaseToggle.vue";
import BaseDateInput from "../../../components/BaseDateInput.vue";
import BaseTagInput from "../../../components/BaseTagInput.vue";
import BaseFileUpload from "../../../components/BaseFileUpload.vue";
import BaseButton from "../../../components/BaseButton.vue";
import BaseSkeleton from "../../../components/BaseSkeleton.vue";
import BaseTabs from "../../../components/BaseTabs.vue";
import ExperienceManager from "./ExperienceManager.vue";

const props = defineProps({
    user: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits(["success", "cancel"]);

const userStore = useUserStore();
const isLoadingData = ref(true);

const form = reactive({
    full_name: "",
    place_of_birth: "",
    date_of_birth: "",
    work_interest: "",
    summary: "",
    is_active: false,
    hobbies: [],
    skills: [],
    photo: null,
    cv: null,
});

const existingPhotoUrl = ref("");
const existingCvUrl = ref("");
const activeTab = ref("general");

const tabs = [
    { label: "General Info", value: "general" },
    { label: "Skills & Hobbies", value: "skills" },
    { label: "Work Experience", value: "experience" },
];

const fetchProfile = async () => {
    isLoadingData.value = true;
    userStore.errors = {}; // Reset errors
    const profile = await userStore.fetchUserProfile(props.user.id);

    if (profile) {
        form.full_name = profile.full_name;
        form.place_of_birth = profile.place_of_birth;
        form.date_of_birth = profile.date_of_birth;
        form.work_interest = profile.work_interest;
        form.summary = profile.summary;
        form.is_active = profile.is_active;
        form.hobbies = profile.hobbies || [];
        form.skills = profile.skills || [];
        existingPhotoUrl.value = profile.photo_url;
        existingCvUrl.value = profile.cv_url;
    } else {
        // Pre-fill name from user data if profile doesn't exist
        form.full_name = props.user.name;
    }

    isLoadingData.value = false;
};

const submit = async () => {
    const success = await userStore.updateUserProfile(props.user.id, form);
    if (success) {
        emit("success");
    }
};

onMounted(() => {
    fetchProfile();
});
</script>

<template>
    <div>
        <div v-if="isLoadingData" class="space-y-4">
            <BaseSkeleton height="2.5rem" />
            <BaseSkeleton height="2.5rem" />
            <BaseSkeleton height="5rem" />
            <BaseSkeleton height="10rem" />
        </div>

        <div v-else class="space-y-6">
            <!-- Tabs Navigation -->
            <BaseTabs :tabs="tabs" v-model="activeTab" />

            <form @submit.prevent="submit" class="space-y-6">
                <!-- General Tab -->
                <div v-show="activeTab === 'general'" class="space-y-6">
                    <!-- Basic Info -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <BaseInput
                            v-model="form.full_name"
                            label="Full Name"
                            placeholder="Enter full name"
                            :error="userStore.errors.full_name?.[0]"
                        />

                        <BaseInput
                            v-model="form.work_interest"
                            label="Work Interest"
                            placeholder="e.g. Full Stack Developer"
                            :error="userStore.errors.work_interest?.[0]"
                        />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <BaseInput
                            v-model="form.place_of_birth"
                            label="Place of Birth"
                            placeholder="City name"
                            :error="userStore.errors.place_of_birth?.[0]"
                        />
                        <BaseDateInput
                            v-model="form.date_of_birth"
                            label="Date of Birth"
                            :error="userStore.errors.date_of_birth?.[0]"
                        />
                    </div>

                    <BaseTextarea
                        v-model="form.summary"
                        label="Summary"
                        placeholder="Brief professional summary..."
                        :rows="4"
                        :error="userStore.errors.summary?.[0]"
                    />

                    <!-- File Uploads -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <BaseFileUpload
                            v-model="form.photo"
                            label="Profile Photo"
                            accept="image/*"
                            :previewUrl="existingPhotoUrl"
                            :error="userStore.errors.photo?.[0]"
                        />

                        <BaseFileUpload
                            v-model="form.cv"
                            label="Curriculum Vitae (PDF)"
                            accept="application/pdf"
                            :previewUrl="existingCvUrl"
                            :error="userStore.errors.cv?.[0]"
                        />
                    </div>
                </div>

                <!-- Skills Content -->
                <div v-show="activeTab === 'skills'" class="space-y-6">
                    <!-- Hobbies -->
                    <BaseTagInput
                        v-model="form.hobbies"
                        label="Hobbies & Interests"
                        placeholder="Type and press Enter (e.g. Hiking, Photography)"
                        :error="userStore.errors.hobbies?.[0]"
                    />

                    <!-- Skills -->
                    <BaseTagInput
                        v-model="form.skills"
                        label="Skills & Expertise"
                        fetchUrl="/api/skills"
                        placeholder="e.g. Laravel, Vue.js, DevOps"
                        :error="userStore.errors.skills?.[0]"
                    />
                </div>

                <!-- Experience Tab -->
                <div v-show="activeTab === 'experience'" class="space-y-6">
                    <ExperienceManager :userId="user.id" />
                </div>

                <!-- Settings (Global) -->
                <div
                    class="pt-4 border-t border-border-base"
                    v-show="activeTab !== 'experience'"
                >
                    <BaseToggle
                        v-model="form.is_active"
                        label="Set as Active Portfolio Profile"
                        :error="userStore.errors.is_active?.[0]"
                    />
                    <p class="mt-1 text-xs text-text-muted">
                        If enabled, this profile will be visible on the public
                        portfolio site.
                    </p>
                </div>

                <!-- Actions (Global except Experience which has its own) -->
                <div
                    class="flex justify-end gap-3 pt-4"
                    v-show="activeTab !== 'experience'"
                >
                    <BaseButton
                        type="button"
                        variant="secondary"
                        @click="$emit('cancel')"
                    >
                        Cancel
                    </BaseButton>
                    <BaseButton
                        type="submit"
                        variant="primary"
                        :isLoading="userStore.isLoading"
                    >
                        Save Changes
                    </BaseButton>
                </div>
            </form>
        </div>
    </div>
</template>
