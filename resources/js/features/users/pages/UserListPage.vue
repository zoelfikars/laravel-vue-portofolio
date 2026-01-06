<script setup>
import { ref, reactive, onMounted } from "vue";
import { useUserStore } from "../stores/userStore";
import AppLayout from "../../../layouts/AppLayout.vue";
import BaseCard from "../../../components/BaseCard.vue";
import BaseButton from "../../../components/BaseButton.vue";
import BaseInput from "../../../components/BaseInput.vue";
import BaseModal from "../../../components/BaseModal.vue";
import BaseSkeleton from "../../../components/BaseSkeleton.vue";
import UserProfileForm from "../components/UserProfileForm.vue";
import { Edit2, Trash2, Plus, Search, UserCog } from "lucide-vue-next";

const userStore = useUserStore();

// Search & Filter
const searchQuery = ref("");
let searchTimeout = null;

const onSearch = () => {
    if (searchTimeout) clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        fetchUsers();
    }, 500);
};

// Pagination
const fetchUsers = (page = 1) => {
    userStore.fetchUsers({
        page,
        search: searchQuery.value,
    });
};

const changePage = (url) => {
    if (!url) return;
    const urlParams = new URL(url);
    const page = urlParams.searchParams.get("page");
    fetchUsers(page);
};

// Modal Logic
const isModalOpen = ref(false);
const isEditMode = ref(false);

const form = reactive({
    id: null,
    name: "",
    email: "",
    role: "user",
    password: "",
});

const openCreateModal = () => {
    isEditMode.value = false;
    form.id = null;
    form.name = "";
    form.email = "";
    form.role = "user";
    form.password = "";
    userStore.errors = {};
    isModalOpen.value = true;
};

const openEditModal = (user) => {
    isEditMode.value = true;
    form.id = user.id;
    form.name = user.name;
    form.email = user.email;
    form.role = user.roles && user.roles.length > 0 ? user.roles[0] : "user";
    form.password = ""; // Reset password
    userStore.errors = {};
    isModalOpen.value = true;
};

const closeModal = () => {
    isModalOpen.value = false;
};

const submitForm = async () => {
    let success = false;
    if (isEditMode.value) {
        success = await userStore.updateUser(form.id, form);
    } else {
        success = await userStore.createUser(form);
    }

    if (success) {
        closeModal();
    }
};

const deleteUser = (user) => {
    isDeleteModalOpen.value = true;
    userToDelete.value = user;
};

const confirmDelete = async () => {
    if (userToDelete.value) {
        await userStore.deleteUser(userToDelete.value.id);
        isDeleteModalOpen.value = false;
        userToDelete.value = null;
    }
};

const closeDeleteModal = () => {
    isDeleteModalOpen.value = false;
    userToDelete.value = null;
};

// Delete Modal State
const isDeleteModalOpen = ref(false);
const userToDelete = ref(null);

// Profile Modal State
const isProfileModalOpen = ref(false);
const selectedUserForProfile = ref(null);

const openProfileModal = (user) => {
    selectedUserForProfile.value = user;
    isProfileModalOpen.value = true;
};

const closeProfileModal = () => {
    isProfileModalOpen.value = false;
    selectedUserForProfile.value = null;
};

onMounted(() => {
    fetchUsers();
});
</script>

<template>
    <AppLayout>
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div
                class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4"
            >
                <h2 class="text-2xl font-bold text-text-base">
                    User Management
                </h2>
                <div class="flex items-center gap-4 w-full sm:w-auto">
                    <!-- Search -->
                    <div class="relative w-full sm:w-64">
                        <div
                            class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
                        >
                            <Search class="h-4 w-4 text-text-muted" />
                        </div>
                        <input
                            v-model="searchQuery"
                            @input="onSearch"
                            type="text"
                            class="bg-bg-input border border-border-base text-text-base text-sm rounded-md focus:ring-primary focus:border-primary block w-full pl-10 p-2.5 transition-colors duration-200"
                            placeholder="Search users..."
                        />
                    </div>

                    <BaseButton @click="openCreateModal">
                        <Plus class="w-4 h-4 mr-2" />
                        Add User
                    </BaseButton>
                </div>
            </div>

            <!-- Table Card -->
            <BaseCard class="p-0 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-text-muted">
                        <thead
                            class="text-xs text-text-muted uppercase bg-bg-base border-b border-border-base"
                        >
                            <tr>
                                <th scope="col" class="px-6 py-3">User</th>
                                <th scope="col" class="px-6 py-3">Role</th>
                                <th scope="col" class="px-6 py-3">
                                    Joined Date
                                </th>
                                <th scope="col" class="px-6 py-3 text-right">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Loading Skeleton Rows -->
                            <tr
                                v-if="userStore.isLoading"
                                v-for="i in 5"
                                :key="i"
                                class="border-b border-border-base"
                            >
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <BaseSkeleton
                                            shape="circle"
                                            width="2.5rem"
                                            height="2.5rem"
                                        />
                                        <div class="ml-4 space-y-2">
                                            <BaseSkeleton
                                                width="8rem"
                                                height="0.875rem"
                                            />
                                            <BaseSkeleton
                                                width="10rem"
                                                height="0.75rem"
                                            />
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <BaseSkeleton
                                        width="4rem"
                                        height="1.25rem"
                                        className="rounded-full"
                                    />
                                </td>
                                <td class="px-6 py-4">
                                    <BaseSkeleton width="6rem" height="1rem" />
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <BaseSkeleton
                                            width="2rem"
                                            height="1rem"
                                        />
                                        <BaseSkeleton
                                            width="2rem"
                                            height="1rem"
                                        />
                                    </div>
                                </td>
                            </tr>

                            <!-- Empty State -->
                            <tr v-else-if="!userStore.users.length">
                                <td
                                    colspan="4"
                                    class="px-6 py-12 text-center text-text-muted"
                                >
                                    No users found matching your query.
                                </td>
                            </tr>

                            <!-- Data Rows -->
                            <tr
                                v-else
                                v-for="user in userStore.users"
                                :key="user.id"
                                class="border-b border-border-base hover:bg-bg-base/50 transition-colors"
                            >
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div
                                            class="h-10 w-10 flex-shrink-0 bg-bg-base rounded-full flex items-center justify-center text-primary font-bold border border-border-base"
                                        >
                                            {{
                                                user.name
                                                    .charAt(0)
                                                    .toUpperCase()
                                            }}
                                        </div>
                                        <div class="ml-4">
                                            <div
                                                class="text-sm font-medium text-text-base"
                                            >
                                                {{ user.name }}
                                            </div>
                                            <div
                                                class="text-sm text-text-muted"
                                            >
                                                {{ user.email }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-primary/20 text-primary border border-primary/20"
                                    >
                                        {{ user.roles[0] || "User" }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{
                                        new Date(
                                            user.created_at
                                        ).toLocaleDateString("id-ID", {
                                            year: "numeric",
                                            month: "long",
                                            day: "numeric",
                                        })
                                    }}
                                </td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <button
                                        @click="openProfileModal(user)"
                                        class="text-text-muted hover:text-primary transition-colors"
                                        title="Manage Profile"
                                    >
                                        <UserCog class="w-4 h-4" />
                                    </button>
                                    <button
                                        @click="openEditModal(user)"
                                        class="text-text-muted hover:text-primary transition-colors"
                                        title="Edit"
                                    >
                                        <Edit2 class="w-4 h-4" />
                                    </button>
                                    <button
                                        @click="deleteUser(user)"
                                        class="text-text-muted hover:text-red-500 transition-colors"
                                        title="Delete"
                                    >
                                        <Trash2 class="w-4 h-4" />
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div
                    class="bg-bg-base/50 px-4 py-3 border-t border-border-base flex items-center justify-between sm:px-6 transition-colors duration-200"
                >
                    <div
                        class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between"
                    >
                        <div>
                            <p class="text-sm text-text-muted">
                                Showing
                                <span class="font-medium text-text-base">{{
                                    userStore.pagination.from || 0
                                }}</span>
                                to
                                <span class="font-medium text-text-base">{{
                                    userStore.pagination.to || 0
                                }}</span>
                                of
                                <span class="font-medium text-text-base">{{
                                    userStore.pagination.total
                                }}</span>
                                results
                            </p>
                        </div>
                        <div>
                            <nav
                                class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px"
                                aria-label="Pagination"
                            >
                                <!-- Links -->
                                <button
                                    v-for="(link, index) in userStore.pagination
                                        .links"
                                    :key="index"
                                    :disabled="!link.url"
                                    @click="changePage(link.url)"
                                    v-html="link.label"
                                    class="relative inline-flex items-center px-4 py-2 border text-sm font-medium transition-colors"
                                    :class="[
                                        link.active
                                            ? 'z-10 bg-primary border-primary text-primary-foreground'
                                            : 'bg-bg-card border-border-base text-text-muted hover:bg-bg-base',
                                        !link.url
                                            ? 'opacity-50 cursor-not-allowed'
                                            : 'cursor-pointer',
                                        index === 0 ? 'rounded-l-md' : '',
                                        index ===
                                        userStore.pagination.links.length - 1
                                            ? 'rounded-r-md'
                                            : '',
                                    ]"
                                />
                            </nav>
                        </div>
                    </div>
                </div>
            </BaseCard>

            <!-- Create/Edit Modal -->
            <BaseModal
                :isOpen="isModalOpen"
                :title="isEditMode ? 'Edit User' : 'Add New User'"
                @close="closeModal"
            >
                <div class="space-y-4">
                    <BaseInput
                        v-model="form.name"
                        label="Name"
                        :error="userStore.errors.name?.[0]"
                        placeholder="John Doe"
                    />

                    <BaseInput
                        v-model="form.email"
                        label="Email"
                        type="email"
                        :error="userStore.errors.email?.[0]"
                        placeholder="john@example.com"
                    />

                    <BaseInput
                        v-model="form.password"
                        label="Password"
                        type="password"
                        :error="userStore.errors.password?.[0]"
                        :placeholder="
                            isEditMode
                                ? 'Leave blank to keep current'
                                : '********'
                        "
                        :labelAppend="isEditMode ? '(Optional)' : ''"
                    />
                </div>

                <template #footer>
                    <BaseButton
                        @click="submitForm"
                        variant="primary"
                        :isLoading="userStore.isLoading"
                        class="ml-3"
                    >
                        {{ isEditMode ? "Update" : "Create" }}
                    </BaseButton>
                    <BaseButton @click="closeModal" variant="secondary">
                        Cancel
                    </BaseButton>
                </template>
            </BaseModal>
            <!-- Delete Confirmation Modal -->
            <BaseModal
                :isOpen="isDeleteModalOpen"
                title="Confirm Delete"
                @close="closeDeleteModal"
                maxWidth="sm:max-w-md"
            >
                <div class="text-sm text-text-muted">
                    Are you sure you want to delete user
                    <span class="font-bold text-text-base">{{
                        userToDelete?.name
                    }}</span
                    >? This action cannot be undone.
                </div>

                <template #footer>
                    <BaseButton
                        @click="confirmDelete"
                        variant="danger"
                        :isLoading="userStore.isLoading"
                        class="ml-3"
                    >
                        Delete
                    </BaseButton>
                    <BaseButton @click="closeDeleteModal" variant="secondary">
                        Cancel
                    </BaseButton>
                </template>
            </BaseModal>

            <!-- User Profile Modal -->
            <BaseModal
                :isOpen="isProfileModalOpen"
                :title="
                    selectedUserForProfile
                        ? `Manage Profile: ${selectedUserForProfile.name}`
                        : 'Manage Profile'
                "
                @close="closeProfileModal"
                maxWidth="sm:max-w-4xl"
            >
                <UserProfileForm
                    v-if="selectedUserForProfile"
                    :user="selectedUserForProfile"
                    @success="closeProfileModal"
                    @cancel="closeProfileModal"
                />
            </BaseModal>
        </div>
    </AppLayout>
</template>
