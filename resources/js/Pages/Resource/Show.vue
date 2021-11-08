<template>
    <ul>
        <li v-for="field in model.fields" :key="field.name">
            <strong>{{ field.label }}</strong>: {{ field.formatted_value }}
        </li>
    </ul>
</template>

<script>
    export default {
        props: {
            model: {
                type: Object,
                required: true,
            },
        },
    }
</script>
