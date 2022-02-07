<template>
    <div>
        <label :for="$attrs.id">{{ label }}</label>
        <fieldset v-for="(value, index) in modelValue" :key="index">
            <FormHandler
                v-for="field in value.fields"
                v-bind="field"
                v-model="$parent.form[field.name]"
                :form="$parent.form"
                :key="field.name"
                :name="field.name"
        ></FormHandler>
        </fieldset>
        <span v-if="error">{{ error }}</span>
    </div>
</template>

<script>
    export default {
        props: {
            modelValue: {
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
