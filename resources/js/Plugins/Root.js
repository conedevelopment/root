import Axios from 'axios';
import Cookie from './../Support/Cookie';
import Debounce from './../Directives/Debounce';
import DispatchesEvents from './../Mixins/DispatchesEvents';
import FormHandler from './../Components/Form/Handler';
import Translator from './../Support/Translator';
import WidgetHandler from './../Components/Widgets/Handler';

export default {
    install(app, options = {}) {
        app.mixin(DispatchesEvents);

        app.component('FormHandler', FormHandler);
        app.component('WidgetHandler', WidgetHandler);

        app.directive('debounce', Debounce);

        app.config.globalProperties.$cookie = new Cookie();

        const translator = new Translator(options.translations || {});

        app.config.globalProperties.__ = (string, replace = {}) => {
            return translator.__(string, replace);
        };

        app.config.globalProperties.$http = Axios.create({
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
        });
    },
}
