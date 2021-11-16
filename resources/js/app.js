import { createApp, resolveComponent, h } from 'vue';
import { createInertiaApp } from '@inertiajs/inertia-vue3';
import Root from './Plugins/Root';

createInertiaApp({
    resolve: (name) => {
        try {
            return require(`./Pages/${name}`);
        } catch (error) {
            return resolveComponent(name);
        }
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
