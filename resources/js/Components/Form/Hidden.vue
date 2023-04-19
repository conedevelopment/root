<template>
    <input v-bind="$attrs" v-model="_value">
</template>

<script>
    export default {
        props: {
            modelValue: {
                type: [String, Number],
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
            value: {
                type: [String, Number],
                default: null,
            },
            formattedValue: {
                type: [String, Number],
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
