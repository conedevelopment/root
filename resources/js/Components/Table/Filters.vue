<template>
    <div class="app-operation__filter">
        <button
            type="button"
            class="btn btn--secondary btn--icon btn--has-counter"
            :aria-label="isOpen ? __('Close filters') : __('Open filters')"
            @click="toggle"
        >
            <Icon
                class="btn__icon btn__icon--start"
                :name="isOpen ? 'filter-list-off' : 'filter-list'"
            ></Icon>
            <span v-if="activeFilters > 0" class="btn__counter">{{ activeFilters }}</span>
        </button>
        <div class="app-drawer" v-show="isOpen">
            <h2 class="app-drawer__title">
                {{ __('Filters') }}
                <button type="button" class="btn btn--secondary btn--sm btn--icon" @click="close">
                    <Icon class="btn__icon btn__icon--sm" name="close"></Icon>
                </button>
            </h2>
            <div class="app-drawer__inner">
                <FormHandler
                    v-for="filter in filters"
                    v-bind="filter"
                    v-model="query[filter.key]"
                    :form="query"
                    :key="filter.key"
                    :name="filter.key"
                    :label="filter.name"
                    @update:modelValue="emit"
                ></FormHandler>
                <div class="form-group--reset">
                    <button type="button" class="btn btn--sm btn--icon btn--tertiary" :aria-label="__('Reset')" @click="reset">
                        {{ __('Reset') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import Closable from './../../Mixins/Closable';

    export default {
        mixins: [Closable],

        props: {
            filters: {
                type: Array,
                default: () => [],
            },
            query: {
                type: Object,
                required: true,
            },
        },

        emits: ['update:query'],

        computed: {
            activeFilters() {
                return this.filters.filter((filter) => filter.active).length;
            },
        },

        methods: {
            emit() {
                this.$emit('update:query');
            },
            reset() {
                this.$inertia.get(this.$parent.items.path);
            },
        },
    }
</script>
