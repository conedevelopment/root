<template>
    <div>
        <Table
            :query="query"
            :filters="filters"
            :actions="actions"
            :extracts="extracts"
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
            filters: {
                type: Array,
                required: true,
            },
            actions: {
                type: Array,
                required: true,
            },
            extracts: {
                type: Array,
                required: true,
            },
        },
    }
</script>
