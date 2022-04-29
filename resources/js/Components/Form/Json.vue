<template>
    <div class="form-group-stack">
        <FormHandler
            v-for="field in fields"
            v-bind="field"
            v-model="modelValue[field.name]"
            :form="$parent.form"
            :key="`${name}-${field.name}`"
            :id="`${name}.${field.name}`"
            :name="`${name}.${field.name}`"
        ></FormHandler>
    </div>
</template>

<script>
    export default {
        props: {
            modelValue: {
                type: [Object, Array],
                default: () => [],
            },
            fields: {
                type: Array,
                default: () => [],
            },
            name: {
                type: String,
                required: true,
            },
        },

        inheritAttrs: false,
    }
</script>
