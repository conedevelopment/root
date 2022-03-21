<template>
    <div class="form-group" :class="class" :style="style">
        <div class="form-row--mixed form-row--accordion">
            <FormHandler
                v-bind="$attrs"
                v-model="value"
                :component="component"
                :form="$parent.form"
                :name="name"
                :select-resolver="selectResolver"
            ></FormHandler>
            <div class="accordion-wrapper">
                <div v-for="(pivot, key) in modelValue" :key="key">
                    <div class="accordion" v-if="pivotFields?.[key] && pivotFields[key].length > 0">
                        <h2 class="accordion__title">
                            <button type="button" aria-expanded="true">
                                {{ formattedValue[key] }}
                                <svg aria-hidden="true" focusable="false" height="24px" viewBox="0 0 24 24" width="24px">
                                    <path d="M12,2c-5.52,0 -10,4.48 -10,10c0,5.52 4.48,10 10,10c5.52,0 10,-4.48 10,-10c0,-5.52 -4.48,-10 -10,-10Zm0,18c-4.41,0 -8,-3.59 -8,-8c0,-4.41 3.59,-8 8,-8c4.41,0 8,3.59 8,8c0,4.41 -3.59,8 -8,8Z" fill="currentColor"></path>
                                    <path d="M7,12c0,0.55 0.45,1 1,1l8,0c0.55,0 1,-0.45 1,-1c0,-0.55 -0.45,-1 -1,-1l-8,0c-0.55,0 -1,0.45 -1,1Z" fill="currentColor"></path>
                                    <path class="vert" d="M12,7c-0.55,0 -1,0.45 -1,1l0,8c0,0.55 0.45,1 1,1c0.55,0 1,-0.45 1,-1l0,-8c0,-0.55 -0.45,-1 -1,-1Z" fill="currentColor"></path>
                                </svg>
                            </button>
                        </h2>
                        <div class="accordion__content form-group-stack">
                            <FormHandler
                                v-for="field in pivotFields[key]"
                                v-bind="field"
                                v-model="modelValue[key][field.name]"
                                :form="$parent.form"
                                :key="`${key}-${field.name}`"
                                :id="`${name}.${key}.${field.name}`"
                                :name="`${name}.${key}.${field.name}`"
                            ></FormHandler>
                        </div>
                    </div>
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
