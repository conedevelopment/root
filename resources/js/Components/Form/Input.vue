<template>
    <div class="form-group" :class="class" :style="style">
        <label class="form-label" :for="$attrs.id">{{ label }}</label>
        <input
            class="form-control"
            v-bind="$attrs"
            :class="{ 'form-control--invalid': error !== null }"
            :value="modelValue"
            @input="update"
        >
        <span class="field-feedback field-feedback--invalid" v-if="error">{{ error }}</span>
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
