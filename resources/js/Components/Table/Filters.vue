<template>
    <div>
        <FilterHandler
            v-for="filter in filters"
            :key="filter.key"
            v-bind="filter"
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
        },
    }
</script>
