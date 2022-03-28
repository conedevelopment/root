<template>
    <div class="form-group" :class="class" :style="style">
        <label class="form-label" :for="$attrs.id">
            <span>{{ label }}</span>
            <span v-if="$attrs.required" class="form-label__required-marker" :aria-label="__('Required')">*</span>
        </label>
        <input
            class="form-control"
            :class="{ 'form-control--invalid': error !== null }"
            v-bind="$attrs"
            @input="update($event.target.files)"
        >
        <progress v-if="$parent.form.progress" :value="$parent.form.progress.percentage" max="100">
            {{ form.progress.percentage }}%
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
                type: Array,
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
        },

        methods: {
            update(value) {
                this.$emit('update:modelValue', value);
            },
        },
    }
</script>
