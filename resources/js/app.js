import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/inertia-vue3';
import FormHandler from './Components/Form/Handler';
import FormInput from './Components/Form/Input';
import FormSelect from './Components/Form/Select';

createInertiaApp({
    resolve: (name) => require(`./Pages/${name}`),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });

        app.component('FormHandler', FormHandler);
        app.component('FormInput', FormInput);
        app.component('FormSelect', FormSelect);

        app.use(plugin);

        document.dispatchEvent(new CustomEvent('root:booting', { detail: app }));

        app.mount(el);

        document.dispatchEvent(new CustomEvent('root:booted', { detail: app }));
    },
});
