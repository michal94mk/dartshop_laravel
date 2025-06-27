import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    clearScreen: false,
    optimizeDeps: {
        force: true
    },
    build: {
        // Generate source maps for easier debugging
        sourcemap: true,
        // Clear the output directory on each build
        emptyOutDir: true,
    },
    resolve: {
        alias: {
            vue: 'vue/dist/vue.esm-bundler.js',
        },
    },
    server: {
        port: 5173,
        host: 'localhost',
        hmr: {
            host: 'localhost',
        },
        // Add headers to prevent caching issues
        headers: {
            'Cache-Control': 'no-store',
        },
        // Add proxy configuration for API
        proxy: {
            '/api': {
                target: 'http://localhost:8000',
                changeOrigin: true,
                secure: false,
                ws: true, // WebSocket support
                cookieDomainRewrite: 'localhost',
                xfwd: true, // Forward X-Forwarded-* headers
            },
            '/sanctum': {
                target: 'http://localhost:8000',
                changeOrigin: true,
                secure: false,
                cookieDomainRewrite: 'localhost',
            },
        },
        fs: {
            strict: false,
        },
    },
});
