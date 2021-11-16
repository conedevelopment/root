import * as dayjs from 'dayjs';
import Axios from 'axios';
import Cookie from './../Support/Cookie';
import DispatchesEvents from './../Mixins/DispatchesEvents';
import FormHandler from './../Components/Form/Handler';
import WidgetHandler from './../Components/Widgets/Handler';

export default {
    install(app) {
        app.mixin(DispatchesEvents);

        app.component('FormHandler', FormHandler);
        app.component('WidgetHandler', WidgetHandler);

        app.config.globalProperties.$cookie = new Cookie();

        app.config.globalProperties.$date = dayjs;

        app.config.globalProperties.$http = Axios.create({
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
        });
    },
}
