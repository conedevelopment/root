<template>

</template>

<script>
    export default {
        props: {
            modelValue: {
                type: [Object, String, Number, Boolean],
                default: null,
            },
        },

        inheritAttrs: false,

        emits: ['update:modelValue'],

        computed: {
            checked() {
                return JSON.stringify(this.$attrs.value) === JSON.stringify(this.modelValue);
            },
        },

        methods: {
            update() {
                this.$emit('update:modelValue', this.$attrs.value);
            },
        },
    }
</script>
