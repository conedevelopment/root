<template>
    <div class="form-group form-group--vertical-check" :class="class" :style="style">
        <label class="form-label" :for="id">
            <span>{{ label }}</span>
            <span v-if="$attrs.required" class="form-label__required-marker" :aria-label="__('Required')">*</span>
        </label>
        <label v-for="option in options" class="form-check" :key="option.value">
            <input
                v-bind="{ ...$attrs, ...option }"
                class="form-check__control"
                v-model="_value"
            >
            <span class="form-check__label" v-html="option.formatted_value"></span>
            <span
                class="field-feedback"
                :class="{ 'field-feedback--invalid': error !== null }"
                v-if="error !== null || help"
                v-html="error || help"
            ></span>
        </label>
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
                default: null,
            },
            value: {
                default: null,
            },
            formatted_value: {
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
            id: {
                type: String,
                required: true,
            },
            options: {
                type: Array,
                default: () => [],
            },
            help: {
                type: String,
                default: null,
            },
        },

        inheritAttrs: false,

        emits: ['update:modelValue'],

        computed: {
            _value: {
                set(value) {
                    this.$emit('update:modelValue', value);
                },
                get() {
                    return this.modelValue;
                },
            },
        },
    }
</script>
