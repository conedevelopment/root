<template>
    <div>
        <div class="form-row--mixed">
            <Actions
                v-if="actions.length > 0"
                :selection="selection"
                :actions="actions"
                @success="clearSelection"
            ></Actions>
            <Extracts
                v-if="extracts.length > 0"
                :extracts="extracts"
            ></Extracts>
            <Filters
                v-if="filters.length > 0"
                :query="query"
                :filters="filters"
                @update:query="fetch"
            ></Filters>
        </div>
        <div class="card">
            <div class="table-responsive">
                <table class="table table--striped">
                    <Head
                        :items="items.data"
                        :query="query"
                        :selection="selection"
                        @update:query="fetch"
                    ></Head>
                    <tbody>
                        <Row v-for="item in items.data" :key="item.id" :item="item"></Row>
                    </tbody>
                </table>
            </div>
        </div>
        <Pagination :query="query" :items="items" @update:query="fetch"></Pagination>
    </div>
</template>

<script>
    import Actions from './../Actions/Actions';
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
                query: this.$inertia.form(window.location.href, this.items.query),
            };
        },

        methods: {
            selected(item) {
                return this.selection.includes(item.id);
            },
            select(item) {
                if (! this.selection.includes(item.id)) {
                    this.selection.push(item.id);
                }
            },
            deselect(item) {
                const index = this.selection.indexOf(item.id);

                if (index !== -1) {
                    this.selection.splice(index, 1);
                }
            },
            selectAll(matching = false) {
                // append all matching to query string
                this.selection = this.items.data.map((item) => item.id);
            },
            clearSelection() {
                this.selection = [];
            },
            fetch() {
                this.query.transform((data) => ({
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
                        this.clearSelection();
                        this.processing = false;
                    },
                });
            },
        },
    }
</script>
