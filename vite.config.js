import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import tailwindcss from "@tailwindcss/vite";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/css/app.css",
                "resources/css/jsvectormap.min.css",
                "resources/css/sidebar.css",
                "resources/scss/app.scss",
                "resources/js/app.js",
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
