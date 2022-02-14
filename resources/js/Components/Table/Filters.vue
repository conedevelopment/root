<template>
    <div>
        <button type="button" class="js-toggle-filter btn btn--secondary btn--icon">
            <Icon class="btn__icon btn__icon--start" name="filter-list"></Icon>
        </button>
        <div style="display: none;">
            <FilterHandler
                v-for="filter in filters"
                :key="filter.key"
                v-bind="filter"
                :value="queryString.get(filter.key)"
                @change="(value) => queryString.set(filter.key, value)"
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
            filters: {
                type: Array,
                default: () => [],
            },
            queryString: {
                type: Object,
                required: true,
            },
        },
    }
</script>
