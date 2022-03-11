<template>
    <div class="form-group" :class="class" :style="style">
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
                <div v-for="(pivot, key) in modelValue" :key="key">
                    <fieldset v-if="pivotFields?.[key] && pivotFields[key].length > 0">
                        <legend>{{ formattedValue[key] }}</legend>
                        <FormHandler
                            v-for="field in pivotFields[key]"
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
            formatted_value: {
                type: [Array, Object],
                default: () => [],
            },
        },

        inheritAttrs: false,

        emits: ['update:modelValue'],

        data() {
            return {
                pivotFields: Object.assign({}, this.pivot_fields),
                formattedValue: Object.assign({}, this.formatted_value),
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
                            [key]: this.pivotFields[key].reduce((pivotValues, field) => {
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
                this.pivotFields = value.reduce((fields, key) => {
                    return Object.assign(fields, {
                        [key]: this.pivotFields.hasOwnProperty(key)
                            ? this.pivotFields[key]
                            : options.find((option) => option.value === key).pivot_fields,
                    });
                }, {});

                this.formattedValue = value.reduce((fields, key) => {
                    return Object.assign(fields, {
                        [key]: this.formattedValue.hasOwnProperty(key)
                            ? this.formattedValue[key]
                            : options.find((option) => option.value === key).formatted_value,
                    });
                }, {});

                return value;
            },
        },
    }
</script>
