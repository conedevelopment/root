<template>
    <div>
        <Actions v-if="actions.length > 0" :actions="actions" :models="selection"></Actions>
        <Extracts v-if="extracts.length > 0" :extracts="extracts"></Extracts>
        <Filters v-if="filters.length > 0" :filters="filters"></Filters>
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
    </div>
</template>

<script>
    import Actions from './Actions';
    import Extracts from './Extracts';
    import Filters from './Filters';
    import Head from './Head';
    import Row from './Row';

    export default {
        components: {
            Actions,
            Extracts,
            Filters,
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

        data() {
            return {
                selection: [],
            };
        },
    }
</script>
