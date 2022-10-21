<template>
    <div class="modal-filter">
        <FormHandler
            v-for="filter in filters"
            v-bind="filter"
            v-model="query[filter.key]"
            :form="query"
            :key="filter.key"
            :name="filter.key"
            :label="filter.name"
            @update:modelValue="emit"
        ></FormHandler>
    </div>
</template>

<script>
    export default {
        props: {
            filters: {
                type: Array,
                default: () => [],
            },
            query: {
                type: Object,
                required: true,
            },
        },

        emits: ['update:query'],

        methods: {
            emit() {
                this.$emit('update:query');
            },
        },
    }
</script>
