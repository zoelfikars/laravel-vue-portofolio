<script setup>
import { reactive, onMounted } from "vue";
import { useAuthStore } from "../../../stores/auth";
import { useThemeStore } from "../../../stores/theme";
import { useRouter } from "vue-router";
import BaseInput from "../../../components/BaseInput.vue";
import BaseButton from "../../../components/BaseButton.vue";
import BaseCard from "../../../components/BaseCard.vue";

const authStore = useAuthStore();
const themeStore = useThemeStore();
const router = useRouter();

const form = reactive({
    email: "",
    password: "",
});

const handleLogin = async () => {
    const success = await authStore.login(form);
    if (success) {
        router.push("/admin");
    }
};

onMounted(() => {
    themeStore.initTheme();
});
</script>

<template>
    <div
        class="min-h-screen flex flex-col items-center justify-center bg-bg-base transition-colors duration-200 py-12 px-4 sm:px-6 lg:px-8"
    >
        <!-- Logo -->
        <div class="mb-6">
            <svg
                height="48"
                viewBox="0 0 16 16"
                width="48"
                class="fill-text-base"
            >
                <path
                    d="M8 0c4.42 0 8 3.58 8 8a8.013 8.013 0 0 1-5.45 7.59c-.4.08-.55-.17-.55-.38 0-.27.01-1.13.01-2.2 0-.75-.25-1.23-.54-1.48 1.78-.2 3.65-.88 3.65-3.95 0-.88-.31-1.59-.82-2.15.08-.2.36-1.02-.08-2.12 0 0-.67-.22-2.2.82-.64-.18-1.32-.27-2-.27-.68 0-1.36.09-2 .27-1.53-1.03-2.2-.82-2.2-.82-.44 1.1-.16 1.92-.08 2.12-.51.56-.82 1.28-.82 2.15 0 3.06 1.86 3.75 3.64 3.95-.23.2-.44.55-.51 1.07-.46.21-1.61.55-2.33-.66-.15-.24-.6-.83-1.23-.82-.67.01-.27.38.01.53.34.19.73.9.82 1.13.16.45.68 1.31 2.69.94 0 .67.01 1.3.01 1.49 0 .21-.15.45-.55.38A7.995 7.995 0 0 1 0 8c0-4.42 3.58-8 8-8Z"
                ></path>
            </svg>
        </div>

        <!-- Heading -->
        <h2
            class="text-center text-2xl font-light text-text-base mb-6 tracking-tight"
        >
            Sign in to Portfolio
        </h2>

        <!-- Card -->
        <div class="w-full max-w-[340px]">
            <BaseCard>
                <form @submit.prevent="handleLogin" class="space-y-4">
                    <!-- General Error -->
                    <div
                        v-if="authStore.errors.general"
                        class="bg-red-500/10 border border-red-500/50 text-red-500 px-4 py-3 rounded text-sm mb-4"
                    >
                        {{ authStore.errors.general[0] }}
                    </div>

                    <BaseInput
                        id="email"
                        label="Email Address"
                        type="email"
                        v-model="form.email"
                        :error="authStore.errors.email?.[0]"
                        placeholder="admin@laravue.com"
                        class="mb-4"
                    />

                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label
                                class="block text-sm font-medium text-text-muted"
                                >Password</label
                            >
                            <a
                                href="#"
                                class="text-xs text-primary hover:underline"
                                >Forgot password?</a
                            >
                        </div>
                        <BaseInput
                            id="password"
                            type="password"
                            v-model="form.password"
                            :error="authStore.errors.password?.[0]"
                            placeholder="••••••••"
                        />
                    </div>

                    <div class="pt-2">
                        <BaseButton
                            type="submit"
                            :isLoading="authStore.isLoading"
                            block
                        >
                            Sign in
                        </BaseButton>
                    </div>
                </form>
            </BaseCard>
        </div>

        <!-- Footer link -->
        <p
            class="mt-8 text-center text-xs text-text-muted border-t border-border-base pt-4 w-full max-w-[340px]"
        >
            New to Portfolio?
            <span class="text-primary hover:underline cursor-not-allowed"
                >Create an account</span
            >.
        </p>
    </div>
</template>
