<template>
    <div>
        <div v-if="widgets.length > 0" class="app-widget">
            <Widget
                v-for="widget in widgets"
                v-bind="widget"
                :key="widget.key"
            ></Widget>
        </div>
        <Table v-bind="table"></Table>
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
            table: {
                type: Object,
                required: true,
            },
            widgets: {
                type: Array,
                default: () => [],
            },
        },

        layout: function (h, page) {
            return h(this.resolveDefaultLayout(), () => page);
        },
    }
</script>
