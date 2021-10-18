import { createApp, h } from 'vue';
import { InertiaProgress } from '@inertiajs/progress';
import { createInertiaApp } from '@inertiajs/inertia-vue3';

createInertiaApp({
    resolve: (name) => {
        const page = require(`./Pages/${name}`).default;

        page.layout = page.layout || Layout;

        return page;
    },
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });

        //

        app.use(plugin);
        app.mount(el);
    },
});
