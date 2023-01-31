<template>
    <fieldset :class="class" :style="style">
        <legend>{{ label }}</legend>
        <div>
            <FormHandler
                v-for="field in fields"
                v-bind="field"
                v-model="modelValue[field.name]"
                :form="$parent.form"
                :key="`${name}-${field.name}`"
                :id="`${name}.${field.name}`"
                :name="`${name}.${field.name}`"
            />
        </div>
    </fieldset>
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
