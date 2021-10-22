<template>
    <div>
        <label :for="$attrs.id">{{ label }}</label>
        <select v-bind="$attrs" v-model="value">
            <option disabled selected>{{ label }}</option>
            <option v-for="(value, option) in options" :value="value" :key="value">
                {{ option }}
            </option>
        </select>
        <span v-if="error">{{ error }}</span>
    </div>
</template>

<script>
    export default {
        props: {
            modelValue: {
                type: [String, Number, Array],
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
        },

        inheritAttrs: false,

        emits: ['update:modelValue'],

        computed: {
            value: {
                set(value) {
                    this.$emit('update:modelValue', value);
                },
                get() {
                    return JSON.parse(JSON.stringify(this.modelValue));
                },
            },
        },
    }
</script>
