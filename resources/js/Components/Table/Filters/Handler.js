import { h, resolveComponent } from 'vue';
import InputFilter from './InputFilter';
import SelectFilter from './SelectFilter';

export default {
    name: 'FilterHandler',

    components: {
        InputFilter,
        SelectFilter,
    },

    props: {
        component: {
            type: String,
            default: 'SelectFilter',
        },
    },

    inheritAttrs: false,

    render() {
        return h(resolveComponent(this.component), this.$attrs, this.$slots);
    },
}
