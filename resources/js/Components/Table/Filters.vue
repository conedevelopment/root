<template>
    <div>
        <button type="button" class="js-toggle-filter btn btn--secondary btn--icon">
            <Icon class="btn__icon btn__icon--start" name="filter-list"></Icon>
        </button>
        <div>
            <FormHandler
                v-for="filter in filters"
                v-bind="filter"
                v-model="form[filter.key]"
                :form="form"
                :key="filter.key"
                :name="filter.key"
                :label="filter.name"
                @update:modelValue="emit"
            ></FormHandler>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            filters: {
                type: Array,
                default: () => [],
            },
            form: {
                type: Object,
                required: true,
            },
        },

        emits: ['change'],

        methods: {
            emit() {
                this.$emit('change');
            },
        },
    }
</script>
