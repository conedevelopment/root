<template>
    <div>
        <button type="button" class="js-toggle-filter btn btn--secondary btn--icon" @click="test">
            <Icon class="btn__icon btn__icon--start" name="filter-list"></Icon>
        </button>
        <div style="display: none;">
            <FilterHandler
                v-for="filter in filters"
                v-bind="filter"
                :key="filter.key"
            ></FilterHandler>
        </div>
    </div>
</template>

<script>
    import FilterHandler from './Filters/Handler';

    export default {
        components: {
            FilterHandler,
        },

        props: {
            filters: {
                type: Array,
                default: () => [],
            },
        },

        methods: {
            test() {
                this.$inertia.get('/posts', {
                    foo: 'bar',
                    boo: [1,2,3],
                    sorty: { a: 'b', c: 'e' }
                })
            },
        },
    }
</script>
