import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    envDir: './../../../',
    build: {
        rollupOptions: {
            output: {
                manualChunks: () => 'app',
            },
        },
    },
    server: {
        https: true,
        host: '127.0.0.1',
        hmr: {
            host: '127.0.0.1',
        },
    },
    plugins: [
        laravel({
            input: 'resources/js/app.js',
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
    resolve: {
        alias: [
            {
                find: /^~(.*)$/,
                replacement: '$1',
            },
        ],
    },
});
