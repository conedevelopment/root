import { createApp, resolveComponent, h } from 'vue';
import { createInertiaApp } from '@inertiajs/inertia-vue3';
import Root from './Plugins/Root';
import Layout from './Components/Layout/Layout';

createInertiaApp({
    resolve: (name) => {
        let page;

        try {
            page = require(`./Pages/${name}`).default;
        } catch (error) {
            page = resolveComponent(name);
        }

        page.resolveDefaultLayout = () => Layout;

        return page;
    },
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });

        app.use(plugin);
        app.use(Root);

        document.dispatchEvent(new CustomEvent('root:booting', { detail: { app } }));

        app.mount(el);

        document.dispatchEvent(new CustomEvent('root:booted', { detail: { app } }));
    },
});
