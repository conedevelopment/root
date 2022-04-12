<template>
    <div>
        <div class="app-operation">
            <Link :href="resource.urls.index" class="btn btn--primary btn--icon">
                <Icon name="arrow-back" class="btn__icon btn__icon--sm"></Icon>
                {{ __('Go back to :resource', { resource: resource.name }) }}
            </Link>
        </div>
        <div class="app-widget">
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
    import { Link } from '@inertiajs/inertia-vue3';
    import Table from './../../Components/Table/Table';
    import Widget from './../../Components/Widgets/Handler';

    export default {
        components: {
            Link,
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
        },

        layout: function (h, page) {
            return h(this.resolveDefaultLayout(), () => page);
        },
    }
</script>
