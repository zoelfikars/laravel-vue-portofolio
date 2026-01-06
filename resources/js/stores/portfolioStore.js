import { defineStore } from "pinia";
import apiClient from "../lib/axios.js";

export const usePortfolioStore = defineStore("portfolio", {
    state: () => ({
        homeData: null,
        experiences: [],
        projects: [],
        currentProject: null,
        loading: false,
        error: null,
        projectsPagination: null,
    }),

    actions: {
        async fetchHome() {
            this.loading = true;
            try {
                const response = await apiClient.get("/api/public/home");
                this.homeData = response.data.data;
            } catch (err) {
                this.error =
                    err.response?.data?.message || "Failed to fetch home data";
            } finally {
                this.loading = false;
            }
        },

        async fetchExperiences() {
            this.loading = true;
            try {
                const response = await apiClient.get("/api/public/experiences");
                this.experiences = response.data.data;
            } catch (err) {
                this.error =
                    err.response?.data?.message ||
                    "Failed to fetch experiences";
            } finally {
                this.loading = false;
            }
        },

        async fetchProjects(params = {}) {
            this.loading = true;
            try {
                const response = await apiClient.get("/api/public/projects", {
                    params,
                });
                this.projects = response.data.data;
                this.projectsPagination = response.data.meta; // Assuming standard resource pagination
            } catch (err) {
                this.error =
                    err.response?.data?.message || "Failed to fetch projects";
            } finally {
                this.loading = false;
            }
        },

        async fetchProjectDetail(slug) {
            this.loading = true;
            this.currentProject = null;
            try {
                const response = await apiClient.get(
                    `/api/public/projects/${slug}`
                );
                this.currentProject = response.data.data;
            } catch (err) {
                this.error =
                    err.response?.data?.message ||
                    "Failed to fetch project detail";
            } finally {
                this.loading = false;
            }
        },
    },
});
