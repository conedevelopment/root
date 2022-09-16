<template>
    <div class="media-sidebar">
        <div class="media-sidebar__section">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <h3 style="margin: 0;" class="media-sidebar__title">
                    {{ __(':count files selected', { count: selection.length }) }}
                </h3>
                <button type="button" class="btn btn--sm btn--delete" @click="clear">
                    {{ __('Clear') }}
                </button>
            </div>
            <div class="accordion-wrapper">
                <Item
                    v-for="item in selection"
                    :key="item.id"
                    :item="item"
                    @deselect="deselect(item)"
                ></Item>
            </div>
        </div>
    </div>
</template>

<script>
    import Item from './Selected.vue';

    export default {
        components: {
            Item,
        },

        props: {
            selection: {
                type: Array,
                default: () => [],
            },
        },

        emits: ['select', 'deselect', 'clear'],

        methods: {
            select(item) {
                this.$emit('select', item);
            },
            deselect(item) {
                this.$emit('deselect', item);
            },
            clear() {
                this.$emit('clear');
            },
        },
    }
</script>
