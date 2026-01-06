<script setup>
import { ref, onMounted, computed } from "vue";
import { useUserStore } from "../stores/userStore";
import BaseInput from "../../../components/BaseInput.vue";
import BaseSelect from "../../../components/BaseSelect.vue";
import BaseButton from "../../../components/BaseButton.vue";
import BaseModal from "../../../components/BaseModal.vue";
import {
    Github,
    Linkedin,
    Instagram,
    Facebook,
    Twitter,
    Mail,
    Globe,
    Phone,
    Link as LinkIcon,
    Trash2,
    Edit2,
    Plus,
    AlertCircle,
} from "lucide-vue-next";

const props = defineProps({
    userId: { type: String, required: true },
});

const userStore = useUserStore();
const contacts = ref([]);
const isLoading = ref(false);
const isEditing = ref(false);
const editingId = ref(null);

// Delete Modal State
const isDeleteModalOpen = ref(false);
const contactToDelete = ref(null);

const form = ref({
    platform: "",
    url: "",
});

const platformOptions = [
    { label: "GitHub", value: "github" },
    { label: "LinkedIn", value: "linkedin" },
    { label: "Instagram", value: "instagram" },
    { label: "Facebook", value: "facebook" },
    { label: "Twitter (X)", value: "twitter" },
    { label: "WhatsApp", value: "whatsapp" },
    { label: "Email", value: "email" },
    { label: "Website", value: "website" },
    { label: "Other", value: "other" },
];

const getIcon = (platform) => {
    switch (platform.toLowerCase()) {
        case "github":
            return Github;
        case "linkedin":
            return Linkedin;
        case "instagram":
            return Instagram;
        case "facebook":
            return Facebook;
        case "twitter":
            return Twitter;
        case "whatsapp":
        case "phone":
            return Phone;
        case "email":
            return Mail;
        case "website":
            return Globe;
        default:
            return LinkIcon;
    }
};

const fetchContacts = async () => {
    isLoading.value = true;
    contacts.value = await userStore.fetchContacts(props.userId);
    isLoading.value = false;
};

const resetForm = () => {
    form.value = { platform: "", url: "" };
    isEditing.value = false;
    editingId.value = null;
    userStore.errors = {};
};

const editContact = (contact) => {
    form.value = {
        platform: contact.platform,
        url: contact.url,
    };
    isEditing.value = true;
    editingId.value = contact.id;
    userStore.errors = {};
};

const submit = async () => {
    let success;
    if (isEditing.value) {
        success = await userStore.updateContact(editingId.value, form.value);
    } else {
        success = await userStore.addContact(props.userId, form.value);
    }

    if (success) {
        await fetchContacts();
        resetForm();
    }
};

const confirmDelete = (contact) => {
    contactToDelete.value = contact;
    isDeleteModalOpen.value = true;
};

const handleDelete = async () => {
    if (!contactToDelete.value) return;

    const success = await userStore.deleteContact(contactToDelete.value.id);
    if (success) {
        await fetchContacts();
        isDeleteModalOpen.value = false;
        contactToDelete.value = null;
    }
};

const cancelDelete = () => {
    isDeleteModalOpen.value = false;
    contactToDelete.value = null;
};

onMounted(() => {
    fetchContacts();
});
</script>

<template>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- List -->
        <div class="space-y-4">
            <h3 class="text-lg font-medium text-text-base">
                Existing Contacts
            </h3>

            <div
                v-if="contacts.length === 0"
                class="text-text-muted text-sm italic"
            >
                No contacts added yet.
            </div>

            <div v-else class="space-y-3">
                <div
                    v-for="contact in contacts"
                    :key="contact.id"
                    class="flex items-center justify-between p-3 bg-bg-card border border-border-base rounded-lg group"
                >
                    <div class="flex items-center gap-3 overflow-hidden">
                        <component
                            :is="getIcon(contact.platform)"
                            class="w-5 h-5 text-text-muted shrink-0"
                        />
                        <div class="min-w-0">
                            <p
                                class="text-sm font-medium text-text-base capitalize"
                            >
                                {{ contact.platform }}
                            </p>
                            <p class="text-xs text-text-muted truncate">
                                {{ contact.url }}
                            </p>
                        </div>
                    </div>

                    <div
                        class="flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity"
                    >
                        <button
                            type="button"
                            @click="editContact(contact)"
                            class="p-1.5 text-text-muted hover:text-primary hover:bg-bg-base rounded transition-colors"
                        >
                            <Edit2 class="w-4 h-4" />
                        </button>
                        <button
                            type="button"
                            @click="confirmDelete(contact)"
                            class="p-1.5 text-text-muted hover:text-red-500 hover:bg-bg-base rounded transition-colors"
                        >
                            <Trash2 class="w-4 h-4" />
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form -->
        <div class="bg-bg-card p-4 rounded-lg border border-border-base h-fit">
            <h3 class="text-lg font-medium text-text-base mb-4">
                {{ isEditing ? "Edit Contact" : "Add New Contact" }}
            </h3>

            <div class="space-y-4" @keydown.enter.prevent="submit">
                <BaseSelect
                    v-model="form.platform"
                    label="Platform"
                    :options="platformOptions"
                    placeholder="Select Platform"
                    :error="userStore.errors.platform?.[0]"
                />

                <BaseInput
                    v-model="form.url"
                    label="URL / Value"
                    placeholder="https://... or mailto:..."
                    :error="userStore.errors.url?.[0]"
                />

                <div class="flex justify-end gap-2 pt-2">
                    <BaseButton
                        v-if="isEditing"
                        type="button"
                        variant="secondary"
                        size="sm"
                        @click="resetForm"
                    >
                        Cancel
                    </BaseButton>
                    <BaseButton
                        type="button"
                        variant="primary"
                        :isLoading="userStore.isLoading"
                        size="sm"
                        @click="submit"
                    >
                        <Plus v-if="!isEditing" class="w-4 h-4 mr-1" />
                        {{ isEditing ? "Update" : "Add" }}
                    </BaseButton>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <BaseModal
        :isOpen="isDeleteModalOpen"
        title="Delete Contact"
        maxWidth="sm:max-w-md"
        @close="cancelDelete"
    >
        <div class="flex flex-col items-center text-center p-4">
            <div
                class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center mb-4 text-red-600"
            >
                <AlertCircle class="w-6 h-6" />
            </div>
            <h3 class="text-lg font-medium text-text-base mb-2">
                Are you sure?
            </h3>
            <p class="text-text-muted mb-6">
                Do you really want to delete this contact? This process cannot
                be undone.
            </p>
            <div
                v-if="contactToDelete"
                class="flex items-center gap-2 p-2 bg-bg-base rounded-md mb-6 w-full justify-center"
            >
                <component
                    :is="getIcon(contactToDelete.platform)"
                    class="w-4 h-4 text-text-muted"
                />
                <span class="text-sm font-medium text-text-base capitalize">{{
                    contactToDelete.platform
                }}</span
                >:
                <span class="text-xs text-text-muted truncate max-w-[150px]">{{
                    contactToDelete.url
                }}</span>
            </div>
        </div>

        <template #footer>
            <div class="flex w-full gap-3">
                <BaseButton
                    type="button"
                    variant="secondary"
                    class="flex-1"
                    @click="cancelDelete"
                >
                    Cancel
                </BaseButton>
                <BaseButton
                    type="button"
                    variant="danger"
                    class="flex-1"
                    @click="handleDelete"
                    :isLoading="userStore.isLoading"
                >
                    Delete
                </BaseButton>
            </div>
        </template>
    </BaseModal>
</template>
