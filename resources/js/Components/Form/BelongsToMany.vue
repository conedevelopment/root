<template>
    <div>
        <FormHandler
            v-bind="$attrs"
            v-model="value"
            :component="component"
            :form="$parent.form"
            :select-resolver="selectResolver"
        ></FormHandler>
        <fieldset v-for="(pivot, key) in modelValue" :key="key">
            <FormHandler
                v-for="field in fields[key]"
                v-bind="field"
                v-model="modelValue[key][field.name]"
                :form="$parent.form"
                :key="field.name"
            ></FormHandler>
        </fieldset>
    </div>
</template>

<script>
    export default {
        props: {
            modelValue: {
                type: [Array, Object],
                default: () => [],
            },
            async: {
                type: Boolean,
                default: false,
            },
            pivot_fields: {
                type: [Array, Object],
                default: () => [],
            },
        },

        inheritAttrs: false,

        emits: ['update:modelValue'],

        data() {
            return {
                fields: Object.assign({}, this.pivot_fields),
            };
        },

        computed: {
            component() {
                return this.async ? 'AsyncSelect' : 'Select';
            },
            value: {
                set(value) {
                    const data = value.reduce((values, key) => {
                        if (this.modelValue.hasOwnProperty(key)) {
                            return Object.assign(values, { [key]: this.modelValue[key] });
                        }

                        return Object.assign(values, {
                            [key]: this.fields[key].reduce((pivotValues, field) => {
                                return Object.assign(pivotValues, { [field.name]: field.value });
                            }, {}),
                        });
                    }, {});

                    this.$emit('update:modelValue', data);
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
                this.fields = value.reduce((fields, key) => {
                    return Object.assign(fields, {
                        [key]: this.fields.hasOwnProperty(key)
                            ? this.fields[key]
                            : options.find((option) => option.value === key).pivot_fields,
                    });
                }, {});

                return value;
            },
        },
    }
</script>
