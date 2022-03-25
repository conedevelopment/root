<template>
    <div class="form-group" :class="class" :style="style">
        <label class="form-label" :for="$attrs.id">
            <span>{{ label }}</span>
            <span v-if="$attrs.required" class="form-label__required-marker" :aria-label="__('Required')">*</span>
        </label>
        <input
            class="form-control"
            v-bind="$attrs"
            :class="{ 'form-control--invalid': error !== null }"
            @blur="add"
            @keydown.enter="add"
            @keydown.backspace="removeLast"
        >
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
            name: {
                type: String,
                required: true,
            },
            error: {
                type: String,
                default: null,
            },
        },

        inheritAttrs: false,

        emits: ['update:modelValue'],

        data() {
            return {
                tag: null,
            };
        },

        methods: {
            add() {
                if (this.tag && ! this.modelValue.includes(this.tag)) {
                    const value = Array.from(this.modelValue);

                    value.push(this.tag);

                    this.$emit('update:modelValue', value);

                    this.tag = null;
                }
            },
            remove(index) {
                const value = Array.from(this.modelValue);

                value.splice(index, 1);

                this.$emit('update:modelValue', value);
            },
            removeLast() {
                if (! this.tag) {
                    this.remove(this.modelValue.length - 1);
                }
            },
        },
    }
</script>
