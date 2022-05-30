<template>
    <div>
        <div class="app-operation">
            <Link :href="`${parent.url}/edit`" class="btn btn--primary btn--icon">
                <Icon name="arrow-back" class="btn__icon btn__icon--sm"></Icon>
                {{ resource.model_name }}
            </Link>
            <Link :href="`${field.url}/create`" class="btn btn--primary btn--icon">
                <Icon name="add-circle" class="btn__icon btn__icon--sm"></Icon>
                {{ __('Add :model', { model: field.related_name }) }}
            </Link>
        </div>
        <Table :filters="filters" :items="items"></Table>
    </div>
</template>

<script>
    import { Link } from '@inertiajs/inertia-vue3';
    import Table from './../../Components/Table/Table';

    export default {
        components: {
            Link,
            Table,
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
            resource: {
                type: Object,
                required: true,
            },
            parent: {
                type: Object,
                required: true,
            },
            field: {
                type: Object,
                required: true,
            },
        },

        layout: function (h, page) {
            return h(this.resolveDefaultLayout(), () => page);
        },
    }
</script>
