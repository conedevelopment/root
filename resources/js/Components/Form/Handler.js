import { h, resolveComponent } from 'vue';
import AsyncSelect from './AsyncSelect';
import BelongsToMany from './BelongsToMany';
import Checkbox from './Checkbox';
import DateTime from './DateTime';
import Editor from './Editor';
import Input from './Input';
import Media from './Media';
import Radio from './Radio';
import Select from './Select';
import Textarea from './Textarea';

export default {
    name: 'FormHandler',

    components: {
        AsyncSelect,
        BelongsToMany,
        Checkbox,
        DateTime,
        Editor,
        Input,
        Media,
        Radio,
        Select,
        Textarea,
    },

    props: {
        modelValue: {
            default: null,
        },
        name: {
            type: String,
            required: true,
        },
        component: {
            type: String,
            default: 'Input',
        },
        form: {
            type: Object,
            required: true,
        },
    },

    inheritAttrs: false,

    emits: ['update:modelValue'],

    render() {
        return h(resolveComponent(this.component), {
            ...this.$attrs,
            name: this.name,
            modelValue: this.modelValue,
            error: this.form.errors[this.name],
            disabled: this.form.processing,
            required: ! [undefined, 'false', false].includes(this.$attrs.required),
            'onUpdate:modelValue': (value) => {
                this.$emit('update:modelValue', value);
                this.form.clearErrors(this.name);
            },
        }, this.$slots);
    },
}
