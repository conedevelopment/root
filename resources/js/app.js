import { createApp, h } from 'vue';
// import { InertiaProgress } from '@inertiajs/progress';
import { createInertiaApp } from '@inertiajs/inertia-vue3';

createInertiaApp({
    resolve: (name) => require(`./Pages/${name}`),
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .mount(el);

        // document.dispatchEvent(new CustomEvent('root:booting', { detail: app }));
        // document.dispatchEvent(new CustomEvent('root:booted', { detail: app }));
    },
});
