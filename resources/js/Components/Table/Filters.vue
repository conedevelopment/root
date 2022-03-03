<template>
    <div>
        <button type="button" class="js-toggle-filter btn btn--secondary btn--icon">
            <Icon class="btn__icon btn__icon--start" name="filter-list"></Icon>
        </button>
        <div>
            <FilterHandler
                v-for="filter in filters"
                v-bind="filter"
                v-model="form[filter.key]"
                :key="filter.key"
                @update:modelValue="() => $emit('change')"
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
            form: {
                type: Object,
                required: true,
            },
        },

        emits: ['change'],
    }
</script>
