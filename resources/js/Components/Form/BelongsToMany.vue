<template>
    <div class="form-group">
        <div class="form-row--mixed">
            <FormHandler
                v-bind="$attrs"
                v-model="value"
                :component="component"
                :form="$parent.form"
                :name="name"
                :select-resolver="selectResolver"
            ></FormHandler>
            <div>
                <fieldset v-for="(pivot, key) in modelValue" :key="key">
                    <FormHandler
                        v-for="field in fields[key]"
                        v-bind="field"
                        v-model="modelValue[key][field.name]"
                        :form="$parent.form"
                        :key="`${key}-${field.name}`"
                        :id="`${name}.${key}.${field.name}`"
                        :name="`${name}.${key}.${field.name}`"
                    ></FormHandler>
                </fieldset>
            </div>
        </div>
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
