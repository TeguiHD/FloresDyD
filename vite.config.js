import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    server: {
        host: '127.0.0.1',
        origin: 'http://127.0.0.1:5173',
        cors: true,
        hmr: {
            host: '127.0.0.1',
        },
    },
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
    // Optimizaciones para producción
    build: {
        rollupOptions: {
            output: {
                manualChunks: {
                    alpine: ['alpinejs', '@alpinejs/collapse', '@alpinejs/focus', '@alpinejs/intersect'],
                },
            },
        },
        // Usar esbuild en lugar de terser (no requiere instalación extra)
        minify: 'esbuild',
    },
});
