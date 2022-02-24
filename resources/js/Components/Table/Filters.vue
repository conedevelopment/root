<template>
    <div>
        <button type="button" class="js-toggle-filter btn btn--secondary btn--icon">
            <Icon class="btn__icon btn__icon--start" name="filter-list"></Icon>
        </button>
        <div>
            <FilterHandler
                v-for="filter in filters"
                v-bind="filter"
                :key="filter.key"
                :modelValue="query[filter.key]"
                @update:modelValue="(value) => update(filter.key, value)"
            ></FilterHandler>
        </div>
    </div>
</template>

<script>
    import FilterHandler from './Filters/Handler';

    export default {
        components: {
            FilterHandler,
        },

        props: {
            modelValue: {
                type: Object,
                default: () => {},
            },
            filters: {
                type: Array,
                default: () => [],
            },
        },

        emits: ['update:modelValue'],

        computed: {
            query: {
                get() {
                    return this.modelValue;
                },
                set(value) {
                    this.$emit('update:modelValue', value);
                },
            },
        },

        methods: {
            update(key, value) {
                this.query = Object.assign({}, this.query, { [key]: value });
            },
        },
    }
</script>
