import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import i18n from 'laravel-vue-i18n/vite'

// Use this link technic for access VITE_ variables of .env
// https://stackoverflow.com/questions/66389043/how-can-i-use-vite-env-variables-in-vite-config-js

export default ({mode}) =>
{
    // Load app-level env vars to node-level env vars.
    process.env = {...process.env, ...loadEnv(mode, process.cwd())};

    return defineConfig({
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
            i18n(),
        ],
        server: {
            host: '0.0.0.0',
            hmr: {
                // Should match the domain name for avoid "Cross-Origin Request Blocked"
                host: process.env.VITE_HMR_HOST,

                // Browser will utilise this port, use for redirect the connection to the good Docker container (to npm who listen on the port)
                clientPort: process.env.VITE_HMR_CLIENT_PORT,
            },
            watch: {
                // See this link for more information - https://vitejs.dev/config/server-options.html#server-watch
                usePolling: true,
            }
        },
    });
}
