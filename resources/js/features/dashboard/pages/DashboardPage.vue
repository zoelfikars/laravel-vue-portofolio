<script setup>
import AppLayout from "../../../layouts/AppLayout.vue";
import { useAuthStore } from "../../../stores/auth";
const authStore = useAuthStore();
</script>
<template>
    <AppLayout>
        <div class="max-w-4xl mx-auto">
            <div
                class="bg-[#161b22] border border-gray-700 rounded-lg shadow-sm overflow-hidden"
            >
                <div class="p-6">
                    <div class="flex items-center gap-4 mb-4">
                        <div
                            class="h-12 w-12 rounded-full bg-blue-600/20 flex items-center justify-center text-blue-400 font-bold text-xl"
                        >
                            {{ authStore.user?.name?.charAt(0) || "U" }}
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold text-white">
                                Halo, {{ authStore.user?.name || "User" }}!
                            </h3>
                            <p class="text-gray-400 text-sm">
                                Selamat datang kembali di panel admin.
                            </p>
                        </div>
                    </div>
                    <div class="mt-6 border-t border-gray-700 pt-4">
                        <p class="text-gray-300">
                            Anda saat ini login sebagai:
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-900 text-blue-200"
                            >
                                {{
                                    authStore.user?.roles?.[0] || "Unknown Role"
                                }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>
            <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-[#161b22] border border-gray-700 rounded-lg p-6">
                    <p class="text-gray-400 text-sm">Total Projects</p>
                    <p class="text-2xl font-bold text-white mt-2">12</p>
                </div>
                <div class="bg-[#161b22] border border-gray-700 rounded-lg p-6">
                    <p class="text-gray-400 text-sm">Articles</p>
                    <p class="text-2xl font-bold text-white mt-2">5</p>
                </div>
                <div class="bg-[#161b22] border border-gray-700 rounded-lg p-6">
                    <p class="text-gray-400 text-sm">Visitors</p>
                    <p class="text-2xl font-bold text-white mt-2">1.2k</p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
