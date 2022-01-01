import { h, resolveComponent } from 'vue';
import AsyncSelect from './AsyncSelect';
import Editor from './Editor';
import Input from './Input';
import Media from './Media';
import Select from './Select';
import Textarea from './Textarea';

export default {
    name: 'FormHandler',

    components: {
        AsyncSelect,
        Editor,
        Input,
        Media,
        Select,
        Textarea,
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
                this.$emit('update:modelValue', value);
            },
        }, this.$slots);
    },
}
