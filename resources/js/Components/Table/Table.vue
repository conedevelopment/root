<template>
    <div>
        <div v-if="filters.length > 0 || actions.length > 0 || extracts.length > 0" class="app-operation">
            <Filters
                v-if="filters.length > 0"
                :query="query"
                :filters="filters"
                @update:query="fetch"
            ></Filters>
            <Search
                v-if="searchable"
                v-model="query.search"
                :placeholder="__('Search')"
                @update:modelValue="fetch"
            ></Search>
            <Actions
                v-if="actions.length > 0"
                :actions="actions"
                :all-matching="allMatching"
                :selection="selection"
                @success="clearSelection"
            ></Actions>
            <Extracts
                v-if="extracts.length > 0"
                :extracts="extracts"
            ></Extracts>
        </div>
        <div class="app-list">
            <div class="card">
                <div class="table-responsive">
                    <table class="table table--striped table--clear-border table--rounded" v-if="items.data.length">
                        <Head
                            :items="items.data"
                            :query="query"
                            :selection="selection"
                            :columns="columns"
                            @update:query="fetch"
                        ></Head>
                        <tbody>
                            <Row v-for="item in items.data" :key="item.id" :item="item"></Row>
                        </tbody>
                    </table>
                    <div v-else class="alert alert--info">
                        {{ __('No results found.') }}
                    </div>
                </div>
            </div>
            <Pagination :query="query" :items="items" @update:query="fetch"></Pagination>
        </div>
    </div>
</template>

<script>
    import Actions from './../Actions/Actions';
    import Extracts from './Extracts';
    import Filters from './Filters';
    import Head from './Head';
    import Pagination from './Pagination';
    import Row from './Row';
    import Search from './Search';

    export default {
        components: {
            Actions,
            Extracts,
            Filters,
            Head,
            Pagination,
            Row,
            Search,
        },

        props: {
            actions: {
                type: Array,
                default: () => [],
            },
            extracts: {
                type: Array,
                default: () => [],
            },
            filters: {
                type: Array,
                default: () => [],
            },
            items: {
                type: Object,
                required: true,
            },
        },

        data() {
            return {
                selection: [],
                allMatching: false,
                processing: false,
                query: this.$inertia.form(window.location.href, this.items.query),
            };
        },

        computed: {
            columns() {
                const fields = this.items.data?.[0]?.fields || [];

                return fields.map((field) => ({
                    label: field.label,
                    name: field.name,
                    sortable: field.sortable,
                    searchable: field.searchable,
                }));
            },
            searchable() {
                return this.columns.some((column) => column.searchable);
            },
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

                this.allMatching = false;
            },
            selectAll(matching = false) {
                this.allMatching = matching;
                this.selection = this.items.data.map((item) => item.id);
            },
            clearSelection() {
                this.allMatching = false;
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
