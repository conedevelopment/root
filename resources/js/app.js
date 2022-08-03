import './../sass/app.scss';

import { createInertiaApp } from '@inertiajs/inertia-vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import * as Vue from 'vue';
import Layout from './Components/Layout/Layout.vue';
import Root from './Plugins/Root';

window.Vue = Vue;

createInertiaApp({
    resolve: (name) => {
        return resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue'))
            .catch(() => {
                return new Promise((resolve, reject) => {
                    resolve(Vue.resolveComponent(name));
                });
            })
            .then((page) => {
                page.default.resolveDefaultLayout = () => Layout;

                return page;
            });
    },
    setup({ el, App, props, plugin }) {
        const app = Vue.createApp({ render: () => Vue.h(App, props) });

        app.use(plugin);
        app.use(Root, window.Root);

        document.dispatchEvent(new CustomEvent('root:booting', { detail: { app } }));

        const instance = app.mount(el);

        document.dispatchEvent(new CustomEvent('root:booted', { detail: { app, instance } }));
    },
});
