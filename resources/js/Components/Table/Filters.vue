<template>
    <div>
        <button type="button" class="js-toggle-filter btn btn--secondary btn--icon" @click="toggle">
            <Icon class="btn__icon btn__icon--start" name="filter-list"></Icon>
        </button>
        <div v-show="isOpen">
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
    </div>
</template>

<script>
    import Closable from './../../Mixins/Closable';

    export default {
        mixins: [Closable],

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
