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
        sourcemap: false, // Disable sourcemaps in production for smaller files
        cssCodeSplit: true, // Enable CSS code splitting
        minify: 'terser', // Use terser for better minification
        terserOptions: {
            compress: {
                drop_console: true, // Remove console.log in production
                drop_debugger: true,
            },
        },
        chunkSizeWarningLimit: 500, // Warn if chunks exceed 500kb
    },
});
