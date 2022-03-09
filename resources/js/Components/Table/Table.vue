<template>
    <div>
        <div class="form-row--mixed">
            <Actions v-if="actions.length > 0" v-model:selection="selection" :actions="actions"></Actions>
            <Extracts v-if="extracts.length > 0" :extracts="extracts"></Extracts>
            <Filters v-if="filters.length > 0" :form="queryHandler" :filters="filters" @change="fetch"></Filters>
        </div>
        <div class="card">
            <div class="table-responsive">
                <table class="table table--striped">
                    <Head v-model:selection="selection" :form="queryHandler" :items="items.data"></Head>
                    <tbody>
                        <Row v-for="item in items.data" :key="item.id" :item="item"></Row>
                    </tbody>
                </table>
            </div>
        </div>
        <Pagination :form="queryHandler" :items="items" @change="fetch"></Pagination>
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
            query: {
                type: Object,
                default: () => {},
            },
        },

        data() {
            return {
                selection: [],
                processing: false,
                queryHandler: this.$inertia.form(window.location.href, this.query),
            };
        },

        methods: {
            fetch() {
                this.queryHandler.transform((data) => ({
                    ...data,
                    page: 1
                })).get(this.items.path, {
                    replace: true,
                    preserveState: true,
                    preserveScroll: true,
                    onStart: () => {
                        this.processing = true;
                    },
                    onFinish: () => {
                        this.selection = [];
                        this.processing = false;
                    },
                });
            },
        },
    }
</script>
