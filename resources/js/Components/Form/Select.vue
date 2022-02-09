<template>
    <div>
        <label :for="$attrs.id">{{ label }}</label>
        <select v-bind="$attrs" v-model="value">
            <option :disabled="! nullable" :value="null" selected>{{ label }}</option>
            <option v-for="option in options" :value="option.value" :key="option.value">
                {{ option.formatted_value }}
            </option>
        </select>
        <span v-if="error">{{ error }}</span>
    </div>
</template>

<script>
    export default {
        props: {
            modelValue: {
                type: [String, Number, Array, Object],
                default: null,
            },
            label: {
                type: String,
                required: true,
            },
            name: {
                type: String,
                required: true,
            },
            error: {
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
            value: {
                set(value) {
                    this.$emit('update:modelValue', this.selectResolver(value, this.options));
                },
                get() {
                    let value = this.modelValue;

                    if (value instanceof Object) {
                        value = Object.keys(value);
                    }

                    return JSON.parse(JSON.stringify(value));
                },
            },
        },
    }
</script>
