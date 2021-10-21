<template>
    <div>
        <label :for="$attrs.id">{{ label }}</label>
        <select v-bind="$attrs" :value="modelValue" @change="update">
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
                type: [String, Number],
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

        methods: {
            update(event) {
                this.$emit('update:modelValue', event.target.value);
            },
        },
    }
</script>
