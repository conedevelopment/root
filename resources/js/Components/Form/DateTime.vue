<template>
    <div class="form-group" :class="class" :style="style">
        <label class="form-label" :for="$attrs.id">{{ label }}</label>
        <div class="form-group-inner--stack">
            <input
                class="form-control"
                v-bind="$attrs"
                v-model="date"
                :class="{ 'form-control--invalid': error !== null }"
            >
            <input
                type="time"
                step="1"
                class="form-control"
                v-model="time"
                :disabled="$attrs.disabled"
                :class="{ 'form-control--invalid': error !== null }"
            >
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
        },

        inheritAttrs: false,

        emits: ['update:modelValue'],

        data() {
            return {
                value: new Date(this.modelValue),
            };
        },

        computed: {
            date: {
                set(value) {
                    value = value.split('-');
                    this.value.setFullYear(value[0]);
                    this.value.setMonth(value[1] - 1);
                    this.value.setDate(value[2]);
                    this.$emit('update:modelValue', this.value.toISOString());
                },
                get() {
                    return this.modelValue ? [
                        this.value.getFullYear(),
                        (this.value.getMonth() + 1).toString().padStart(2, 0),
                        this.value.getDate().toString().padStart(2, 0),
                    ].join('-') : null;
                },
            },
            time: {
                set(value) {
                    value = value.split(':');
                    this.value.setHours(value[0]);
                    this.value.setMinutes(value[1]);
                    this.value.setSeconds(value[2]);
                    this.$emit('update:modelValue', this.value.toISOString());
                },
                get() {
                    return this.modelValue ? [
                        this.value.getHours().toString().padStart(2, 0),
                        this.value.getMinutes().toString().padStart(2, 0),
                        this.value.getSeconds().toString().padStart(2, 0),
                    ].join(':') : null;
                },
            },
        },
    }
</script>
