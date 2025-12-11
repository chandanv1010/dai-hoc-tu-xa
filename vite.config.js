import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.scss', 'resources/js/app.js'],
            refresh: [
                'resources/views/**',
                'resources/css/**',
                'resources/js/**',
            ],
        }),
    ],
    server: {
        hmr: {
            host: 'localhost',
        },
        watch: {
            usePolling: true,
        },
    },
    css: {
        devSourcemap: true,
    },
    build: {
        sourcemap: true,
        cssCodeSplit: false,
    },
});
