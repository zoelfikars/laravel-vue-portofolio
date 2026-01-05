<script setup>
import { onMounted, ref } from "vue";
import { useAuthStore } from "../../../stores/auth";
import AppLayout from "../../../layouts/AppLayout.vue";
import BaseCard from "../../../components/BaseCard.vue";
import BaseSkeleton from "../../../components/BaseSkeleton.vue";

const authStore = useAuthStore();
const isLoading = ref(true);

onMounted(() => {
    // Simulate loading data
    setTimeout(() => {
        isLoading.value = false;
    }, 1500);
});
</script>

<template>
    <AppLayout>
        <div class="max-w-4xl mx-auto">
            <!-- Welcome Card -->
            <BaseCard class="mb-8">
                <div v-if="isLoading" class="flex items-center gap-4">
                    <BaseSkeleton shape="circle" width="3rem" height="3rem" />
                    <div class="flex-1">
                        <BaseSkeleton
                            width="40%"
                            height="1.5rem"
                            class="mb-2"
                        />
                        <BaseSkeleton width="20%" height="1rem" />
                    </div>
                </div>
                <div v-else class="flex items-center gap-4">
                    <div
                        class="h-12 w-12 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-xl"
                    >
                        {{ authStore.user?.name?.charAt(0) || "U" }}
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold text-text-base">
                            Halo, {{ authStore.user?.name || "User" }}!
                        </h3>
                        <p class="text-text-muted text-sm">
                            Selamat datang kembali di panel admin.
                        </p>
                    </div>
                </div>

                <div class="mt-6 border-t border-border-base pt-4">
                    <div v-if="isLoading">
                        <BaseSkeleton width="30%" height="1rem" />
                    </div>
                    <p v-else class="text-text-muted">
                        Anda saat ini login sebagai:
                        <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary/20 text-primary"
                        >
                            {{ authStore.user?.roles?.[0] || "Unknown Role" }}
                        </span>
                    </p>
                </div>
            </BaseCard>

            <!-- Quick Stats Dummy -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Stat 1 -->
                <BaseCard>
                    <div v-if="isLoading">
                        <BaseSkeleton
                            width="40%"
                            height="0.875rem"
                            class="mb-2"
                        />
                        <BaseSkeleton width="20%" height="2rem" />
                    </div>
                    <div v-else>
                        <p class="text-text-muted text-sm">Total Projects</p>
                        <p class="text-2xl font-bold text-text-base mt-2">12</p>
                    </div>
                </BaseCard>

                <!-- Stat 2 -->
                <BaseCard>
                    <div v-if="isLoading">
                        <BaseSkeleton
                            width="40%"
                            height="0.875rem"
                            class="mb-2"
                        />
                        <BaseSkeleton width="20%" height="2rem" />
                    </div>
                    <div v-else>
                        <p class="text-text-muted text-sm">Articles</p>
                        <p class="text-2xl font-bold text-text-base mt-2">5</p>
                    </div>
                </BaseCard>

                <!-- Stat 3 -->
                <BaseCard>
                    <div v-if="isLoading">
                        <BaseSkeleton
                            width="40%"
                            height="0.875rem"
                            class="mb-2"
                        />
                        <BaseSkeleton width="20%" height="2rem" />
                    </div>
                    <div v-else>
                        <p class="text-text-muted text-sm">Visitors</p>
                        <p class="text-2xl font-bold text-text-base mt-2">
                            1.2k
                        </p>
                    </div>
                </BaseCard>
            </div>
        </div>
    </AppLayout>
</template>
