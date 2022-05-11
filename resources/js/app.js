import * as Vue from 'vue';
import { createInertiaApp } from '@inertiajs/inertia-vue3';
import Root from './Plugins/Root';
import Layout from './Components/Layout/Layout';

window.Vue = Vue;

createInertiaApp({
    resolve: (name) => {
        let page;

        try {
            page = require(`./Pages/${name}`).default;
        } catch (error) {
            page = Vue.resolveComponent(name);
        }

        page.resolveDefaultLayout = () => Layout;

        return page;
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
