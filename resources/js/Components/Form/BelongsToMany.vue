<template>
    <div>
        <label :for="$attrs.id">{{ label }}</label>
        <span v-if="error">{{ error }}</span>
    </div>
</template>

<script>
    import AsyncSelect from './AsyncSelect';
    import Select from './Select';

    export default {
        components: {
            AsyncSelect,
            Select,
        },

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
