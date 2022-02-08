<template>
    <div>
        <FormHandler
            v-bind="$attrs"
            v-model="$parent.form[name]"
            :component="component"
            :form="$parent.form"
            :name="name"
            :select-resolver="selectResolver"
        ></FormHandler>
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
            async: {
                type: Boolean,
                default: false,
            },
        },

        inheritAttrs: false,

        emits: ['update:modelValue'],

        computed: {
            component() {
                return this.async ? 'AsyncSelect' : 'Select';
            },
        },

        methods: {
            selectResolver(value, options) {
                return value;
            },
        },
    }
</script>
