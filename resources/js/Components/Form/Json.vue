<template>
    <fieldset>
        <legend v-if="with_legend">{{ label }}</legend>
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
    </fieldset>
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
