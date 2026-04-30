import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue2';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/js/social-post-addon.js',
                'resources/css/social-post-addon.css',
            ],
            publicDirectory: 'resources/dist',
        }),
        vue(),
    ],
    server: {
        cors: true,
        strictPort: true,
        port: 5173, // Default Vite port
        hmr: {
            host: 'localhost',
        },
    },
});
