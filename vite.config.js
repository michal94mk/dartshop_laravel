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
        // Optimize chunk size
        chunkSizeWarningLimit: 1000,
        rollupOptions: {
            output: {
                // Custom chunk splitting for better caching
                manualChunks: {
                    // Vendor chunks
                    'vendor-vue': ['vue', 'vue-router', 'pinia'],
                    'vendor-ui': ['vue-toastification', '@heroicons/vue'],
                    'vendor-http': ['axios'],
                    'vendor-utils': ['lodash', 'dompurify', 'marked'],
                    'vendor-charts': ['chart.js', 'vue-chartjs'],
                    'vendor-editor': ['quill'],
                    'vendor-stripe': ['@stripe/stripe-js'],
                    
                    // Feature chunks (these will be combined with lazy-loaded routes)
                    'admin-shared': [
                        './resources/js/components/admin/ui/index.js',
                        './resources/js/components/admin/ActionButtons.vue',
                        './resources/js/components/admin/LoadingSpinner.vue'
                    ]
                },
                // Better file naming
                entryFileNames: 'assets/[name]-[hash].js',
                chunkFileNames: 'assets/[name]-[hash].js',
                assetFileNames: 'assets/[name]-[hash].[ext]',
                
                // Remove console.log statements in production
                plugins: process.env.NODE_ENV === 'production' ? [
                    {
                        name: 'remove-console',
                        generateBundle(options, bundle) {
                            Object.keys(bundle).forEach(id => {
                                if (bundle[id].code) {
                                    bundle[id].code = bundle[id].code.replace(/console\.log\([^)]*\);?/g, '');
                                }
                            });
                        }
                    }
                ] : []
            }
        }
    },
    resolve: {
        alias: {
            vue: 'vue/dist/vue.esm-bundler.js',
            '@': '/resources/js',
            '@/components': '/resources/js/components',
            '@/pages': '/resources/js/pages',
            '@/stores': '/resources/js/stores',
            '@/composables': '/resources/js/composables',
            '@/services': '/resources/js/services',
            '@/utils': '/resources/js/utils',
            '@/types': '/resources/js/types',
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
