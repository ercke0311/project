import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue'

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        vue(),
    ],

    server: {
        host: 'project.dev',
        port: 5173,
        strictPort: true,
        cors: {
            origin: 'https://project.dev',
            credentials: true,
        },
        hmr: {
            host: 'project.dev',
            protocol: 'wss',
            port: 5173,
        },
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});