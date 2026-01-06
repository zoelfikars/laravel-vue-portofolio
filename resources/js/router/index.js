import { createRouter, createWebHistory, RouterView } from "vue-router";
import { useAuthStore } from "../stores/auth";
import { h } from "vue";
import DashboardPage from "../features/dashboard/pages/DashboardPage.vue";
import LoginPage from "../features/auth/pages/LoginPage.vue";

import PublicLayout from "../layouts/PublicLayout.vue";
import HomeView from "../features/public/pages/HomeView.vue";

const routes = [
    {
        path: "/",
        component: PublicLayout,
        children: [
            {
                path: "",
                name: "Home",
                component: HomeView,
            },
            {
                path: "experiences",
                name: "Experiences",
                component: () =>
                    import("../features/public/pages/ExperienceListView.vue"),
            },
            {
                path: "projects",
                component: { render: () => h(RouterView) },
                children: [
                    {
                        path: "",
                        name: "Projects",
                        component: () =>
                            import(
                                "../features/public/pages/ProjectListView.vue"
                            ),
                    },
                    {
                        path: ":slug",
                        name: "ProjectDetail",
                        component: () =>
                            import(
                                "../features/public/pages/ProjectDetailView.vue"
                            ),
                        props: true,
                    },
                ],
            },
        ],
    },
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
        path: "/users",
        name: "UserList",
        component: () => import("../features/users/pages/UserListPage.vue"),
        meta: { requiresAuth: true },
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
