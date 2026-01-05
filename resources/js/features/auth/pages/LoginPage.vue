<script setup>
import { ref, reactive } from "vue";
import { useAuthStore } from "../../../stores/auth";
import { useRouter } from "vue-router";
const authStore = useAuthStore();
const router = useRouter();
const form = reactive({
    email: "",
    password: "",
});
const handleLogin = async () => {
    console.log('test' + form);
    const success = await authStore.login(form);
    if (success) {
        router.push("/admin");
    }
};
</script>
<template>
    <div
        class="min-h-screen flex flex-col items-center justify-center bg-[#0d1117] py-12 px-4 sm:px-6 lg:px-8"
    >
        <div class="mb-6">
            <svg
                height="48"
                aria-hidden="true"
                viewBox="0 0 16 16"
                version="1.1"
                width="48"
                data-view-component="true"
                class="fill-white"
            >
                <path
                    d="M8 0c4.42 0 8 3.58 8 8a8.013 8.013 0 0 1-5.45 7.59c-.4.08-.55-.17-.55-.38 0-.27.01-1.13.01-2.2 0-.75-.25-1.23-.54-1.48 1.78-.2 3.65-.88 3.65-3.95 0-.88-.31-1.59-.82-2.15.08-.2.36-1.02-.08-2.12 0 0-.67-.22-2.2.82-.64-.18-1.32-.27-2-.27-.68 0-1.36.09-2 .27-1.53-1.03-2.2-.82-2.2-.82-.44 1.1-.16 1.92-.08 2.12-.51.56-.82 1.28-.82 2.15 0 3.06 1.86 3.75 3.64 3.95-.23.2-.44.55-.51 1.07-.46.21-1.61.55-2.33-.66-.15-.24-.6-.83-1.23-.82-.67.01-.27.38.01.53.34.19.73.9.82 1.13.16.45.68 1.31 2.69.94 0 .67.01 1.3.01 1.49 0 .21-.15.45-.55.38A7.995 7.995 0 0 1 0 8c0-4.42 3.58-8 8-8Z"
                ></path>
            </svg>
        </div>
        <h2
            class="text-center text-2xl font-light text-white mb-6 tracking-tight"
        >
            Sign in to Portfolio
        </h2>
        <div
            class="max-w-[340px] w-full bg-[#161b22] rounded-lg border border-gray-700 p-5 shadow-sm"
        >
            <form @submit.prevent="handleLogin" class="space-y-4">
                <div
                    v-if="authStore.errors.general"
                    class="bg-red-900/30 border border-red-500/50 text-red-200 px-4 py-3 rounded text-sm mb-4"
                >
                    {{ authStore.errors.general[0] }}
                </div>
                <div>
                    <label
                        for="email"
                        class="block text-sm font-normal text-gray-300 mb-2"
                    >
                        Email Address
                    </label>
                    <input
                        id="email"
                        v-model="form.email"
                        type="email"
                        required
                        class="w-full px-3 py-1.5 bg-[#0d1117] border border-gray-600 rounded-md text-white placeholder-gray-500 text-sm focus:outline-none focus:ring-2 focus:ring-[#1f6feb] focus:border-[#1f6feb] transition-colors duration-200"
                        :class="{ 'border-red-400': authStore.errors.email }"
                        autofocus
                    />
                    <p
                        v-if="authStore.errors.email"
                        class="mt-1 text-xs text-red-400"
                    >
                        {{ authStore.errors.email[0] }}
                    </p>
                </div>
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label
                            for="password"
                            class="block text-sm font-normal text-gray-300"
                        >
                            Password
                        </label>
                    </div>
                    <input
                        id="password"
                        v-model="form.password"
                        type="password"
                        required
                        class="w-full px-3 py-1.5 bg-[#0d1117] border border-gray-600 rounded-md text-white placeholder-gray-500 text-sm focus:outline-none focus:ring-2 focus:ring-[#1f6feb] focus:border-[#1f6feb] transition-colors duration-200"
                        :class="{ 'border-red-400': authStore.errors.password }"
                    />
                    <p
                        v-if="authStore.errors.password"
                        class="mt-1 text-xs text-red-400"
                    >
                        {{ authStore.errors.password[0] }}
                    </p>
                </div>
                <div class="pt-2">
                    <button
                        type="submit"
                        :disabled="authStore.isLoading"
                        class="w-full flex justify-center py-1.5 px-4 border border-transparent rounded-md shadow-sm text-sm font-bold text-white bg-[#238636] hover:bg-[#2ea043] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#2ea043] disabled:opacity-50 disabled:cursor-not-allowed transition duration-150 ease-in-out"
                    >
                        <span v-if="authStore.isLoading">Signing in...</span>
                        <span v-else>Sign in</span>
                    </button>
                </div>
            </form>
        </div>
        <p
            class="mt-8 text-center text-xs text-gray-400 border-t border-gray-700 pt-4 w-full max-w-[340px]"
        >
            New to Portfolio?
            <span class="text-blue-400 hover:text-blue-300 cursor-not-allowed"
                >Create an account</span
            >.
        </p>
    </div>
</template>
