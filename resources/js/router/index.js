import { createRouter, createWebHistory } from "vue-router";
import { useAuthStore } from "../stores/auth";
import DashboardPage from "../features/dashboard/pages/DashboardPage.vue";
import LoginPage from "../features/auth/pages/LoginPage.vue";

const routes = [
    {
        path: "/login",
        name: "Login",
        component: LoginPage,
        meta: { guest: true },
    },
    {
        path: "/admin",
        name: "AdminDashboard",
        component: DashboardPage,
        meta: { requiresAuth: true },
    },
    {
        path: "/",
        redirect: "/login",
    },
];
const router = createRouter({
    history: createWebHistory(),
    routes,
});
router.beforeEach(async (to, from, next) => {
    const authStore = useAuthStore();
    if (!authStore.user && !authStore.isAuthCheckDone) {
        await authStore.fetchUser();
    }
    const isAuthenticated = authStore.isAuthenticated;
    if (to.meta.requiresAuth && !isAuthenticated) {
        next("/login");
    } else if (to.meta.guest && isAuthenticated) {
        next("/admin");
    } else {
        next();
    }
});

export default router;
