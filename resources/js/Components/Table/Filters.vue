<template>
    <div>
        <FilterHandler
            v-for="filter in filters"
            :key="filter.key"
            v-bind="filter"
            :value="queryString.get(filter.key)"
            @change="(value) => queryString.set(filter.key, value)"
        ></FilterHandler>
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
