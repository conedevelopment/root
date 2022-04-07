<template>
    <div class="form-group">
        <input
            class="form-control"
            type="search"
            v-bind="$attrs"
            v-model.lazy="value"
            v-debounce="300"
        >
    </div>
</template>

<script>
    export default {
        props: {
            modelValue: {
                type: String,
                default: null,
            },
        },

        inheritAttrs: false,

        emits: ['update:modelValue'],

        computed: {
            value: {
                get() {
                    return this.modelValue;
                },
                set(value) {
                    this.$emit('update:modelValue', value);
                },
            },
        },
    }
</script>
