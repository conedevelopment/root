<template>
    <div class="form-group form-group--range" :class="class" :style="style">
        <label class="form-label" :for="$attrs.id">
            <span>{{ label }}</span>
            <span v-if="$attrs.required" class="form-label__required-marker" :aria-label="__('Required')">*</span>
        </label>
        <input
            ref="input"
            class="form-range"
            v-bind="$attrs"
            v-model="_value"
            :class="{ 'form-control--invalid': error !== null }"
        >
        <div class="form-range-display">
            <span class="form-range-display__item is-min">
                {{ $attrs.min }}
            </span>
            <span class="form-range-display__item is-current">
                {{ modelValue || '-' }}
            </span>
            <span class="form-range-display__item is-max">
                {{ $attrs.max }}
            </span>
        </div>
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
                type: [String, Number],
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
        },

        inheritAttrs: false,

        emits: ['update:modelValue'],

        computed: {
            _value: {
                set(value) {
                    this.$emit('update:modelValue', value);
                },
                get() {
                    return this.modelValue;
                },
            },
        },
    }
</script>
