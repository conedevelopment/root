<template>
    <div class="form-group" :class="class" :style="style">
        <label class="form-label" :for="$attrs.id">
            <span>{{ label }}</span>
            <span v-if="$attrs.required" class="form-label__required-marker" :aria-label="__('Required')">*</span>
        </label>
        <select
            class="form-control"
            v-bind="$attrs"
            v-model="_value"
            :class="{ 'form-control--invalid': error !== null }"
        >
            <option
                :disabled="! nullable || $attrs.multiple"
                :value="null"
                selected
            >
                {{ __('Select :label', { label }) }}
            </option>
            <option v-for="option in options" v-bind="option" :key="option.value">
                {{ option.formatted_value }}
            </option>
        </select>
        <span
            class="field-feedback"
            :class="{ 'field-feedback--invalid': error !== null }"
            v-if="error !== null || help"
            v-html="error || help"
        ></span>
    </div>
</template>

<script>
    export default {
        props: {
            class: {
                type: [String, Array, Object],
                default: null,
            },
            style: {
                type: [String, Array, Object],
                default: null,
            },
            modelValue: {
                type: [String, Number, Array, Object],
                default: null,
            },
            value: {
                type: [String, Number, Array, Object],
                default: null,
            },
            formatted_value: {
                type: [String, Number, Array, Object],
                default: null,
            },
            label: {
                type: String,
                required: true,
            },
            error: {
                type: String,
                default: null,
            },
            help: {
                type: String,
                default: null,
            },
            options: {
                type: Object,
                required: true,
            },
            nullable: {
                type: Boolean,
                default: false,
            },
            selectResolver: {
                type: Function,
                default: (value, options) => value,
            },
        },

        inheritAttrs: false,

        emits: ['update:modelValue'],

        computed: {
            _value: {
                set(value) {
                    this.$emit('update:modelValue', this.selectResolver(value, this.options));
                },
                get() {
                    return JSON.parse(JSON.stringify(this.modelValue));
                },
            },
        },
    }
</script>
