<template>
    <select v-model="value">
        <option :value="null" disabled>{{ name }}</option>
        <option v-for="(label, value) in options" :key="value" :value="value">
            {{ label }}
        </option>
    </select>
</template>

<script>
    export default {
        props: {
            modelValue: {
                type: [String, Number, Array],
                default: null,
            },
            name: {
                type: String,
                required: true,
            },
            options: {
                type: [Object, Array],
                required: true,
            },
        },

        inheritAttrs: false,

        computed: {
            value: {
                get() {
                    return JSON.parse(JSON.stringify(this.modelValue));
                },
                set(value) {
                    this.$emit('update:modelValue', value);
                },
            }
        },
    }
</script>
