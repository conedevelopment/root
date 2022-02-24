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
        modelValue: {
            default: null,
        },
        component: {
            type: String,
            default: 'SelectFilter',
        },
        default: {
            default: null,
        },
    },

    inheritAttrs: false,

    emits: ['update:modelValue'],

    render() {
        return h(resolveComponent(this.component), {
            ...this.$attrs,
            modelValue: this.modelValue,
            'onUpdate:modelValue': (value) => {
                this.$emit('update:modelValue', value);
            },
        }, this.$slots);
    },
}
