import { h, resolveComponent } from 'vue';
import Input from './Input';
import Select from './Select';

export default {
    name: 'FormHandler',

    components: {
        Input,
        Select,
    },

    props: {
        modelValue: {
            default: null,
        },
        error: {
            type: String,
            default: null,
        },
        component: {
            type: String,
            default: 'Input',
        },
    },

    inheritAttrs: false,

    emits: ['update:modelValue'],

    render() {
        return h(resolveComponent(this.component), {
            ...this.$attrs,
            error: this.error,
            modelValue: this.modelValue,
            required: ! [undefined, 'false'].includes(this.$attrs.required),
            'onUpdate:modelValue': (value) => {
                this.update(value);
            },
        }, this.$slots);
    },

    methods: {
        update(value) {
            this.$emit('update:modelValue', value);
        },
    },
}
