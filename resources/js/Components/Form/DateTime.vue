<template>
    <div class="form-group" :class="class" :style="style">
        <label class="form-label" :for="$attrs.id">
            <span>{{ label }}</span>
            <span v-if="$attrs.required" class="form-label__required-marker" :aria-label="__('Required')">*</span>
        </label>
        <div class="form-group-inner--stack">
            <input
                class="form-control"
                v-bind="$attrs"
                v-model="date"
                :class="{ 'form-control--invalid': error !== null }"
            >
            <input
                v-if="with_time"
                type="time"
                step="1"
                class="form-control"
                v-model="time"
                :disabled="$attrs.disabled"
                :class="{ 'form-control--invalid': error !== null }"
            >
        </div>
        <span
            class="field-feedback"
            :class="{ 'field-feedback--invalid': error !== null }"
            v-if="error !== null || help"
            v-html="error || help"
        ></span>
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
                type: String,
                default: null,
            },
            label: {
                type: String,
                default: null,
            },
            error: {
                type: String,
                default: null,
            },
            with_time: {
                type: Boolean,
                default: false,
            },
            value: {
                type: String,
                default: null,
            },
            formattedValue: {
                type: String,
                default: null,
            },
            help: {
                type: String,
                default: null,
            },
        },

        inheritAttrs: false,

        emits: ['update:modelValue'],

        data() {
            return {
                _value: new Date(this.modelValue),
            };
        },

        computed: {
            date: {
                set(value) {
                    value = value.split('-');
                    this._value.setFullYear(value[0]);
                    this._value.setMonth(value[1] - 1);
                    this._value.setDate(value[2]);
                    this.$emit('update:modelValue', this._value.toISOString());
                },
                get() {
                    return this.modelValue ? [
                        this._value.getFullYear(),
                        (this._value.getMonth() + 1).toString().padStart(2, 0),
                        this._value.getDate().toString().padStart(2, 0),
                    ].join('-') : null;
                },
            },
            time: {
                set(value) {
                    value = value.split(':');
                    this._value.setHours(value[0]);
                    this._value.setMinutes(value[1]);
                    this._value.setSeconds(value[2]);
                    this.$emit('update:modelValue', this._value.toISOString());
                },
                get() {
                    return this.modelValue ? [
                        this._value.getHours().toString().padStart(2, 0),
                        this._value.getMinutes().toString().padStart(2, 0),
                        this._value.getSeconds().toString().padStart(2, 0),
                    ].join(':') : null;
                },
            },
        },
    }
</script>
