<template>
    <table>
        <Head></Head>
        <tbody>
            <Row
                v-for="item in query.data"
                :key="item.id"
                :item="item"
            ></Row>
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

        data() {
            return {
                selection: [],
            };
        },
    }
</script>
