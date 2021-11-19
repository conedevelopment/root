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
        value: {
            default: null,
        },
    },

    inheritAttrs: false,

    emits: ['change'],

    render() {
        return h(resolveComponent(this.component), {
            ...this.$attrs,
            modelValue: this.value,
            'onUpdate:modelValue': (value) => {
                this.$emit('change', value);
            },
        }, this.$slots);
    },
}
