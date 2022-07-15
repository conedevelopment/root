import { debounce } from './../../Support/Helpers';
import { h, resolveComponent } from 'vue';
import AsyncSelect from './AsyncSelect';
import Checkbox from './Checkbox';
import DateTime from './DateTime';
import Editor from './Editor';
import Fieldset from './Fieldset';
import Hidden from './Hidden';
import Input from './Input';
import Json from './Json';
import Media from './Media';
import Radio from './Radio';
import Range from './Range';
import Select from './Select';
import SubResource from './SubResource';
import Tag from './Tag';
import Textarea from './Textarea';

export default {
    name: 'FormHandler',

    components: {
        AsyncSelect,
        Checkbox,
        DateTime,
        Editor,
        Fieldset,
        Hidden,
        Input,
        Json,
        Media,
        Radio,
        Range,
        Select,
        SubResource,
        Tag,
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
        componentResolver: {
            type: Function,
            default: (component) => resolveComponent(component),
        },
        debounce: {
            type: Number,
            default: 0,
        },
    },

    inheritAttrs: false,

    emits: ['update:modelValue'],

    render() {
        return h(this.componentResolver(this.component), {
            ...this.$attrs,
            name: this.name,
            modelValue: this.modelValue,
            error: this.form.errors[this.name],
            readonly: this.form.processing || ! [undefined, 'false', false].includes(this.$attrs.readonly),
            required: ! [undefined, 'false', false].includes(this.$attrs.required),
            'onUpdate:modelValue': debounce((value) => {
                this.$emit('update:modelValue', value);
                this.form.clearErrors(this.name);
            }, this.debounce || 0),
        }, this.$slots);
    },
}
