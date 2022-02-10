<template>
    <div>
        <FormHandler
            v-bind="$attrs"
            v-model="value"
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
            value: {
                set(value) {
                    console.log(value);
                },
                get() {
                    if (! Array.isArray(this.modelValue) && this.modelValue instanceof Object) {
                        return Object.keys(this.modelValue);
                    }

                    return JSON.parse(JSON.stringify(this.modelValue));
                },
            },
        },

        methods: {
            selectResolver(value, options) {
                return value;
            },
        },
    }
</script>
