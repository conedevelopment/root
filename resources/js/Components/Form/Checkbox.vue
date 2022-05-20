<template>
    <div class="form-group form-group--vertical-check" :class="class" :style="style">
        <label class="form-label" :for="id">
            <span>{{ label }}</span>
            <span v-if="$attrs.required" class="form-label__required-marker" :aria-label="__('Required')">*</span>
        </label>
        <label v-for="option in options" class="form-check" :key="option.value">
            <input
                v-bind="$attrs"
                class="form-check__control"
                v-model="_value"
                :name="`${name}.${option.value}`"
                :value="option.value"
            >
            <span class="form-check__label" v-html="option.formatted_value"></span>
        </label>
        <label v-if="options.length === 0" class="form-check">
            <input
                v-bind="$attrs"
                class="form-check__control"
                v-model="_value"
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
            class: {
                type: [String, Array, Object],
                default: null,
            },
            style: {
                type: [String, Array, Object],
                default: null,
            },
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
            _value: {
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
