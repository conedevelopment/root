<template>
    <div>
        <Table
            :actions="actions"
            :extracts="extracts"
            :filters="filters"
            :query="query"
            :urls="urls"
        ></Table>
    </div>
</template>

<script>
    import Table from './../../Components/Table/Table';

    export default {
        components: {
            Table,
        },

        props: {
            query: {
                type: Object,
                required: true,
            },
            urls: {
                type: Object,
                required: true,
            },
            filters: {
                type: Array,
                default: () => [],
            },
            actions: {
                type: Array,
                default: () => [],
            },
            extracts: {
                type: Array,
                default: () => [],
            },
        },
    }
</script>
