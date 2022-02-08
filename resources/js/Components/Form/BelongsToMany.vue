<template>
    <div>
        <fieldset></fieldset>
    </div>
</template>

<script>
    export default {
        props: {
            modelValue: {
                type: [Array, Object],
                default: () => [],
            },
            name: {
                type: String,
                required: true,
            },
        },

        inheritAttrs: false,

        emits: ['update:modelValue'],
    }
</script>
