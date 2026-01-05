import { defineStore } from "pinia";
import axios from "../lib/axios";

export const useAuthStore = defineStore("auth", {
    state: () => ({
        user: null,
        isLoading: false,
        errors: {},
        isAuthCheckDone: false,
    }),
    getters: {
        isAuthenticated: (state) => !!state.user,
    },
    actions: {
        async login(credentials) {
            this.isLoading = true;
            this.errors = {};
            try {
                await axios.get("/sanctum/csrf-cookie");
                await axios.post("/api/login", credentials);
                await this.fetchUser();
                return true;
            } catch (error) {
                if (error.response && error.response.status === 422) {
                    this.errors = error.response.data.errors;
                } else if (error.response && error.response.data.message) {
                    this.errors = { general: [error.response.data.message] };
                }
                return false;
            } finally {
                this.isLoading = false;
            }
        },
        async fetchUser() {
            try {
                const response = await axios.get("/api/user");
                this.user = response.data;
            } catch (error) {
                this.user = null;
            } finally {
                this.isAuthCheckDone = true;
            }
        },
        async logout() {
            try {
                await axios.post("/api/logout");
            } catch (error) {
                console.error("Logout failed", error);
            } finally {
                this.resetAuth();
                window.location.href = "/login";
            }
        },
        resetAuth() {
            this.user = null;
            this.errors = {};
        },
    },
});
