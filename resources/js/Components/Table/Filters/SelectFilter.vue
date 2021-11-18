<template>
    <select>
        <option value="" disabled selected>{{ name }}</option>
        <option v-for="(label, value) in options" :key="value" :value="value">
            {{ label }}
        </option>
    </select>
</template>

<script>
    export default {
        props: {
            name: {
                type: String,
                required: true,
            },
            options: {
                type: Array,
                required: true,
            },
        },

        inheritAttrs: false,
    }
</script>
