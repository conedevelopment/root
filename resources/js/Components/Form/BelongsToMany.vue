<template>
    <div>
        <label :for="$attrs.id">{{ label }}</label>
        <fieldset v-for="(pivot_values, value) in modelValue" :key="value">
            <FormHandler
                v-for="field in $attrs.pivot_fields[value]"
                v-bind="field"
                v-model="$parent.form[name][value][field.name]"
                :form="$parent.form"
                :key="`${value}.${field.name}`"
                :name="`${name}.${value}.${field.name}`"
            ></FormHandler>
        </fieldset>
        <span v-if="error">{{ error }}</span>
    </div>
</template>

<script>
    export default {
        props: {
            modelValue: {
                type: [Array, Object],
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
            error: {
                type: String,
                default: null,
            },
            nullable: {
                type: Boolean,
                default: false,
            },
        },

        inheritAttrs: false,

        emits: ['update:modelValue'],
    }
</script>
