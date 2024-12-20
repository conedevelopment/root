import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

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
            input: [
                'resources/js/app.js',
                'resources/js/editor.js',
                'resources/js/media-manager.js',
                'resources/js/repeater.js',
                'resources/js/dropdown.js',
                'resources/js/table.js',
                'resources/js/chart.js',
                'resources/sass/app.scss',
            ],
            refresh: true,
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
