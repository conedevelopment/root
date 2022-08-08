<template>
    <div>
        <div class="app-operation">
            <div class="app-operation__edit">
                <Link v-if="field.abilities.create" class="btn btn--sm btn--tertiary" :href="`${field.url}/create`">
                    {{ __('Add :model', { model: field.related_name }) }}
                </Link>
            </div>
        </div>
        <Table :filters="filters" :items="items"></Table>
    </div>
</template>

<script>
    import { Link } from '@inertiajs/inertia-vue3';
    import Table from './../../Components/Table/Table.vue';

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
