<template>
    <div>
        <div class="form-row--mixed">
            <Actions
                v-if="actions.length > 0"
                v-model:models="selection"
                :actions="actions"
            ></Actions>
            <Extracts v-if="extracts.length > 0" :extracts="extracts"></Extracts>
            <Filters
                v-if="filters.length > 0"
                :filters="filters"
            ></Filters>
        </div>
        <div class="card">
            <div class="table-responsive">
                <table class="table table--striped">
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
        </div>
        <Pagination :query="query"></Pagination>
    </div>
</template>

<script>
    import Actions from './Actions';
    import Extracts from './Extracts';
    import Filters from './Filters';
    import Head from './Head';
    import Pagination from './Pagination';
    import Row from './Row';

    export default {
        components: {
            Actions,
            Extracts,
            Filters,
            Head,
            Pagination,
            Row,
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

        watch: {
            queryString: {
                handler(newValue, oldValue) {
                    const query = newValue.toURLSearchParams();

                    if (query.has('page') && query.get('page') != 1) {
                        query.set('page', 1);
                    }

                    this.$inertia.get(window.location.pathname, query, {
                        replace: true,
                        preserveState: true,
                        preserveScroll: true,
                        onStart: () => {
                            this.processing = true;
                        },
                        onFinish: () => {
                            this.processing = false;
                        },
                    });
                },
                deep: true,
            },
        },

        data() {
            return {
                selection: [],
                processing: false,
            };
        },
    }
</script>
