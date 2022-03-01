<template>
    <div class="form-group">
        <label class="form-label" :for="$attrs.id">{{ label }}</label>
        <textarea
            class="form-control"
            v-bind="$attrs"
            :value="modelValue"
            :class="{ 'form-control--invalid': error !== null }"
            @input="update"
        ></textarea>
        <span class="field-feedback field-feedback--invalid" v-if="error">{{ error }}</span>
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
