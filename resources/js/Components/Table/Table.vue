<template>
    <table>
        <Head :columns="columns"></Head>
        <tbody>
            <Row v-for="item in query.data" :key="item.id" :item="item"></Row>
        </tbody>
    </table>
</template>

<script>
    import Head from './Head';
    import Row from './Row';

    export default {
        components: {
            Head,
            Row,
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
        },

        computed: {
            columns() {
                const fields = this.query.data?.[0]?.fields || [];

                return fields.map((field) => {
                    return {
                        label: field.label,
                        name: field.name,
                        sortable: field.sortable,
                    };
                });
            },
        },
    }
</script>
