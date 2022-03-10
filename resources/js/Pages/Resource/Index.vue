<template>
    <div>
        <div class="widgets">
            <Widget
                v-for="widget in widgets"
                :key="widget.key"
                v-bind="widget"
            ></Widget>
        </div>
        <Table
            :actions="actions"
            :extracts="extracts"
            :filters="filters"
            :items="items"
            :urls="urls"
        ></Table>
    </div>
</template>

<script>
    import Table from './../../Components/Table/Table';
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
