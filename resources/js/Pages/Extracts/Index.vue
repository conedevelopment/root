<template>
    <div>
        <div v-if="widgets.length > 0" class="app-widget">
            <Widget
                v-for="widget in widgets"
                v-bind="widget"
                :key="widget.key"
            ></Widget>
        </div>
        <Table :actions="actions" :filters="filters" :items="items"></Table>
    </div>
</template>

<script>
    import Table from './../../Components/Table/Table.vue';
    import Widget from './../../Components/Widgets/Handler';

    export default {
        components: {
            Table,
            Widget,
        },

        props: {
            items: {
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
            widgets: {
                type: Array,
                default: () => [],
            },
            resource: {
                type: Object,
                required: true,
            },
            extract: {
                type: Object,
                required: true,
            },
        },

        layout: function (h, page) {
            return h(this.resolveDefaultLayout(), () => page);
        },
    }
</script>
