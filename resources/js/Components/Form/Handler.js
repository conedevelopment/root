import { h, resolveComponent } from 'vue';

export default {
    name: 'FormHandler',

    props: {
        modelValue: {
            default: null,
        },
        error: {
            type: String,
            default: null,
        },
        _component: {
            type: String,
            default: 'FormInput',
        },
    },

    inheritAttrs: false,

    emits: ['update:modelValue'],

    render() {
        return h(resolveComponent(this._component), {
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
