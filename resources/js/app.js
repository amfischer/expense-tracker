import './bootstrap';
import '../css/app.css';
import '../css/dropdowns.css';
import '@vuepic/vue-datepicker/dist/main.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import { createPinia } from 'pinia';
import { VueDatePicker } from '@vuepic/vue-datepicker';

// const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `ET - ${title}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        const pinia = createPinia();

        const app = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(pinia);

        app.component('VueDatePicker', VueDatePicker);

        app.mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
