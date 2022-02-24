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
                v-model="query"
                :filters="filters"
            ></Filters>
        </div>
        <div class="card">
            <div class="table-responsive">
                <table class="table table--striped">
                    <Head></Head>
                    <tbody>
                        <Row
                            v-for="item in items.data"
                            :key="item.id"
                            :item="item"
                        ></Row>
                    </tbody>
                </table>
            </div>
        </div>
        <Pagination :items="items"></Pagination>
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
            items: {
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

        data() {
            return {
                selection: [],
                processing: false,
            };
        },

        computed: {
            query: {
                get() {
                    const params = new URLSearchParams(window.location.search);

                    return this.filters.reduce((query, filter) => {
                        return Object.assign(query, { [filter.key]: filter.default });
                    }, { page: params.get('page') });
                },
                set(value) {
                    this.fetch(value);
                },
            },
        },

        methods: {
            fetch(query) {
                const params = JSON.parse(JSON.stringify(query));

                if (params.hasOwnProperty('page') && params.page != 1) {
                    params.page = 1;
                }

                this.$inertia.get(window.location.pathname, params, {
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
        },
    }
</script>
