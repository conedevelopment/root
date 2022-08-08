import { h, resolveComponent } from 'vue';
import Widget from './Widget.vue';

export default {
    name: 'WidgetHandler',

    components: {
        Widget,
    },

    props: {
        component: {
            type: String,
            default: 'Widget',
        },
    },

    inheritAttrs: false,

    render() {
        return h(resolveComponent(this.component), this.$attrs, this.$slots);
    },
}
