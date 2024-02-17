import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m';
import { i18nVue } from 'laravel-vue-i18n';
import { library } from '@fortawesome/fontawesome-svg-core'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { faGlobe } from '@fortawesome/free-solid-svg-icons'
import PrimeVue from 'primevue/config';
import ConfirmationService from 'primevue/confirmationservice';
import ToastService from 'primevue/toastservice';
import Tooltip from 'primevue/tooltip';
import '../css/primevue-theme.css';
import 'primevue/resources/primevue.min.css';
import 'primeicons/primeicons.css'; // ex: les icônes pour sorter dans les DataTables
import PrimeLocale from "@/primeLocale";
import RouterLink from '@/Components/RouterLink.vue';

const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Laravel';
const appLocale = window.document.getElementsByTagName('html')[0]?.lang || 'en';

library.add(faGlobe)

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue, Ziggy)
            .use(i18nVue, {
                resolve: async lang => {
                    const langs = import.meta.glob('../../lang/*.json');
                    return await langs[`../../lang/${ lang }.json`]();
                }
            })
            .component('font-awesome-icon', FontAwesomeIcon)
            .use(PrimeVue, { locale: PrimeLocale[appLocale] })
            .use(ConfirmationService)
            .use(ToastService)
            .directive('tooltip',Tooltip)
            // Workaround pour PrimeVue qui appelle router-link, car il s'attend que Vue-router soit installé,
            // alors que l'app utilise Inertia pour la navigation
            .component('router-link', RouterLink)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
