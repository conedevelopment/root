<template>
    <div class="form-group form-group--vertical-check">
        <label class="form-label" :for="id">{{ label }}</label>
        <label v-for="option in options" class="form-check" :key="option.value">
            <input
                v-bind="$attrs"
                class="form-check__control"
                v-model="selection"
                :name="`${name}.${option.value}`"
                :value="option.value"
            >
            <span class="form-check__label">{{ option.formatted_value }}</span>
        </label>
        <label v-if="options.length === 0" class="form-check">
            <input
                v-bind="$attrs"
                class="form-check__control"
                v-model="selection"
                :id="id"
                :name="name"
            >
            <span class="form-check__label">{{ label }}</span>
        </label>
    </div>
</template>

<script>
    export default {
        props: {
            modelValue: {
                default: null,
            },
            value: {
                default: null,
            },
            formatted_value: {
                default: null,
            },
            label: {
                type: String,
                required: true,
            },
            error: {
                type: String,
                default: null,
            },
            id: {
                type: String,
                requried: true,
            },
            name: {
                type: String,
                requried: true,
            },
            options: {
                type: Array,
                default: () => [],
            },
        },

        inheritAttrs: false,

        emits: ['update:modelValue'],

        computed: {
            selection: {
                set(value) {
                    this.$emit('update:modelValue', value);
                },
                get() {
                    return this.modelValue === null && this.options.length > 0
                        ? []
                        : this.modelValue;
                },
            },
        },
    }
</script>
