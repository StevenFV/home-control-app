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
                detectTls: process.env.VITE_HOST,
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
            https: {
                key: process.env.VITE_HTTPS_KEY,
                cert: process.env.VITE_HTTPS_CERT
            },
            host: process.env.VITE_HOST,
            hmr: {
                host: process.env.VITE_HOST,
            },
        },
    });
}
