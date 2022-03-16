<template>
    <div class="form-group" :class="class" :style="style">
        <label class="form-label" :for="$attrs.id">{{ label }}</label>
        <select
            class="form-control"
            v-bind="$attrs"
            v-model="value"
            :class="{ 'form-control--invalid': error !== null }"
        >
            <option
                :disabled="! nullable || $attrs.multiple"
                :value="null"
                selected
            >
                {{ __('Select :label', { label }) }}
            </option>
            <option v-for="option in options" :value="option.value" :key="option.value">
                {{ option.formatted_value }}
            </option>
        </select>
        <span class="field-feedback field-feedback--invalid" v-if="error">{{ error }}</span>
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
                type: [String, Number, Array, Object],
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
            options: {
                type: Object,
                required: true,
            },
            nullable: {
                type: Boolean,
                default: false,
            },
            selectResolver: {
                type: Function,
                default: (value, options) => value,
            },
        },

        inheritAttrs: false,

        emits: ['update:modelValue'],

        computed: {
            value: {
                set(value) {
                    this.$emit('update:modelValue', this.selectResolver(value, this.options));
                },
                get() {
                    return JSON.parse(JSON.stringify(this.modelValue));
                },
            },
        },
    }
</script>
