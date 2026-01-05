import { defineConfig } from "vite";
import tailwindcss from "@tailwindcss/vite";
import vue from "@vitejs/plugin-vue";
import path from "path";
import { fileURLToPath } from "url";

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);
export default defineConfig({
    plugins: [tailwindcss(), vue()],
    server: {
        host: "0.0.0.0",
        port: 5173,
        hmr: {
            host: "laravue-portofolio.local",
        },
        watch: {
            usePolling: true,
            ignored: ["**/storage/framework/views/**"],
        },
    },
    resolve: {
        alias: {
            "@": path.resolve(__dirname, "./resources/js"),
        },
    },
});
