<template>
    <div class="form-group" :class="class" :style="style">
        <label class="form-label" :for="$attrs.id">
            <span>{{ label }}</span>
            <span v-if="$attrs.required" class="form-label__required-marker" :aria-label="__('Required')">*</span>
        </label>
        <input
            class="form-file"
            :class="{ 'form-control--invalid': error !== null }"
            v-bind="$attrs"
            @input="update($event.target.files)"
        >
        <progress v-if="$parent.form.progress" :value="$parent.form.progress.percentage" max="100">
            {{ $parent.form.progress.percentage }}%
        </progress>
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
                type: [Array, Object],
                default: () => [],
            },
            value: {
                type: [Array, Object],
                default: () => [],
            },
            formatted_value: {
                type: [Array, Object],
                default: () => [],
            },
            pivot_fields: {
                type: [Array, Object],
                default: () => [],
            },
            label: {
                type: String,
                required: true,
            },
            error: {
                type: String,
                default: null,
            },
            options: {
                type: Array,
                default: () => [],
            },
            selection: {
                type: Array,
                default: () => [],
            },
        },

        inheritAttrs: false,

        methods: {
            update(value) {
                this.$emit('update:modelValue', value);
            },
        },
    }
</script>
