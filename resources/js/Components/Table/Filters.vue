<template>

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
