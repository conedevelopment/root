import { debounce } from './../../Support/Helpers';
import { h, resolveComponent } from 'vue';
import AsyncSelect from './AsyncSelect.vue';
import Checkbox from './Checkbox.vue';
import DateTime from './DateTime.vue';
import Editor from './Editor.vue';
import Fieldset from './Fieldset.vue';
import Hidden from './Hidden.vue';
import Input from './Input.vue';
import Media from './Media.vue';
import Radio from './Radio.vue';
import Range from './Range.vue';
import Select from './Select.vue';
import SubResource from './SubResource.vue';
import Tag from './Tag.vue';
import Textarea from './Textarea.vue';

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
