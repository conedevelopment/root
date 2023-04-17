<template>
    <div>
        <div class="app-operation">
            <div class="app-operation__edit">
                <Link v-if="relation.abilities.create" class="btn btn--sm btn--tertiary" :href="`${relation.url}/create`">
                    {{ __('Add :model', { model: relation.related_name }) }}
                </Link>
            </div>
        </div>
        <Table :filters="filters" :items="items"></Table>
    </div>
</template>

<script>
    import { Link } from '@inertiajs/vue3';
    import Table from './../../Components/Table/Table.vue';

    export default {
        components: {
            Link,
            Table,
        },

        props: {
            actions: {
                type: Array,
                default: () => [],
            },
            widgets: {
                type: Array,
                default: () => [],
            },
            items: {
                type: Object,
                required: true,
            },
            filters: {
                type: Array,
                default: () => [],
            },
            relation: {
                type: Object,
                required: true,
            },
        },

        layout: function (h, page) {
            return h(this.resolveDefaultLayout(), () => page);
        },
    }
</script>
