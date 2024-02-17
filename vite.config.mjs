import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import i18n from 'laravel-vue-i18n/vite'

export default ({mode}) =>
{
    process.env = {...process.env, ...loadEnv(mode, process.cwd())};

    return defineConfig({
        plugins: [
            laravel({
                input: 'resources/js/app.js',
                refresh: true
            }),
            vue({
                template: {
                    transformAssetUrls: {
                        base: null,
                        includeAbsolute: false,
                    },
                },
            }),
            i18n(),
        ],
        server: {
            host: process.env.VITE_HOST,
            hmr: {
                host: process.env.VITE_HOST,
            },
        },
    });
}
