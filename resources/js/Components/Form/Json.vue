<template>
    <div class="form-group-stack">
        <fieldset>
            <legend>{{ label }}</legend>
            <FormHandler
                v-for="field in fields"
                v-bind="field"
                v-model="modelValue[field.name]"
                :form="$parent.form"
                :key="`${name}-${field.name}`"
                :id="`${name}.${field.name}`"
                :name="`${name}.${field.name}`"
            ></FormHandler>
        </fieldset>
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
            label: {
                type: String,
                required: true,
            },
            name: {
                type: String,
                required: true,
            },
        },

        inheritAttrs: false,
    }
</script>
