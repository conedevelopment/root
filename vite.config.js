import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import basicSsl from '@vitejs/plugin-basic-ssl';

export default defineConfig({
    server: {
        https: true,
        host: 'localhost',
    },
    plugins: [
        basicSsl(),
        laravel(['resources/js/app.js', 'resources/sass/app.scss']),
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
