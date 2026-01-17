import axios from "axios";
import { useAuthStore } from "../stores/auth";

const apiClient = axios.create({
    baseURL: import.meta.env.VITE_API_BASE_URL || "https://zoelfikars.site",
    withCredentials: true,
    headers: {
        Accept: "application/json",
        "Content-Type": "application/json",
        "X-Requested-With": "XMLHttpRequest",
    },
});
function getCookie(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2)
        return decodeURIComponent(parts.pop().split(";").shift());
}
apiClient.interceptors.request.use((config) => {
    const token = getCookie("XSRF-TOKEN");
    if (token) {
        config.headers["X-XSRF-TOKEN"] = token;
    }
    return config;
});
apiClient.interceptors.response.use(
    (response) => response,
    (error) => {
        const authStore = useAuthStore();
        const status = error.response ? error.response.status : null;
        if (status === 401 || status === 419) {
            authStore.resetAuth();
        }
        return Promise.reject(error);
    }
);
export default apiClient;
