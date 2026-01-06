import { defineStore } from "pinia";
import axios from "../../../lib/axios";
import { useNotificationStore } from "../../../stores/notification";

export const useUserStore = defineStore("user", {
    state: () => ({
        users: [],
        pagination: {
            current_page: 1,
            last_page: 1,
            total: 0,
            per_page: 10,
        },
        isLoading: false,
        errors: {},
    }),

    actions: {
        async fetchUsers(params = {}) {
            this.isLoading = true;
            try {
                const response = await axios.get("/api/users", { params });
                this.users = response.data.data;
                this.pagination = response.data.meta;
                return true;
            } catch (error) {
                console.error("Failed to fetch users:", error);
                return false;
            } finally {
                this.isLoading = false;
            }
        },

        async createUser(userData) {
            this.isLoading = true;
            this.errors = {};
            const notificationStore = useNotificationStore();
            try {
                await axios.post("/api/users", userData);
                notificationStore.success("User berhasil ditambahkan");
                await this.fetchUsers({ page: 1 });
                return true;
            } catch (error) {
                if (error.response && error.response.status === 422) {
                    this.errors = error.response.data.errors;
                } else {
                    notificationStore.error(
                        error.response?.data?.message ||
                            "Terjadi kesalahan saat membuat user"
                    );
                }
                return false;
            } finally {
                this.isLoading = false;
            }
        },

        async updateUser(id, userData) {
            this.isLoading = true;
            this.errors = {};
            const notificationStore = useNotificationStore();
            try {
                await axios.put(`/api/users/${id}`, userData);
                notificationStore.success("User berhasil diperbarui");
                await this.fetchUsers({ page: this.pagination.current_page });
                return true;
            } catch (error) {
                if (error.response && error.response.status === 422) {
                    this.errors = error.response.data.errors;
                } else {
                    notificationStore.error(
                        error.response?.data?.message ||
                            "Terjadi kesalahan saat memperbarui user"
                    );
                }
                return false;
            } finally {
                this.isLoading = false;
            }
        },

        async deleteUser(id) {
            this.isLoading = true;
            const notificationStore = useNotificationStore();
            try {
                await axios.delete(`/api/users/${id}`);
                notificationStore.success("User berhasil dihapus");
                await this.fetchUsers({ page: this.pagination.current_page });
                return true;
            } catch (error) {
                notificationStore.error(
                    error.response?.data?.message || "Gagal menghapus user"
                );
                return false;
            } finally {
                this.isLoading = false;
            }
        },

        async fetchUserProfile(userId) {
            this.isLoading = true;
            try {
                const response = await axios.get(
                    `/api/users/${userId}/profile`
                );
                // If null (404/empty), we return null, components handle empty form
                return response.data.data;
            } catch (error) {
                console.error("Failed to fetch profile:", error);
                return null;
            } finally {
                this.isLoading = false;
            }
        },

        async updateUserProfile(userId, payload) {
            this.isLoading = true;
            this.errors = {};
            const notificationStore = useNotificationStore();

            try {
                // Convert to FormData
                const formData = new FormData();

                for (const key in payload) {
                    if (payload[key] !== null && payload[key] !== undefined) {
                        // Handle Arrays (like hobbies)
                        if (Array.isArray(payload[key])) {
                            payload[key].forEach((item, index) => {
                                formData.append(`${key}[${index}]`, item);
                            });
                        }
                        // Convert boolean to 1/0
                        else if (typeof payload[key] === "boolean") {
                            formData.append(key, payload[key] ? "1" : "0");
                        } else {
                            formData.append(key, payload[key]);
                        }
                    }
                }

                // POST request required for multipart/form-data
                const response = await axios.post(
                    `/api/users/${userId}/profile`,
                    formData,
                    {
                        headers: {
                            "Content-Type": "multipart/form-data",
                        },
                    }
                );

                notificationStore.success(
                    response.data.message || "Profil berhasil diperbarui"
                );
                return true;
            } catch (error) {
                if (error.response && error.response.status === 422) {
                    this.errors = error.response.data.errors;
                } else {
                    notificationStore.error(
                        error.response?.data?.message ||
                            "Gagal memperbarui profil"
                    );
                }
                return false;
            } finally {
                this.isLoading = false;
            }
        },
    },
});
