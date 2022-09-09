<template>
    <div class="modal-footer modal-footer--column">
        <div class="media-selected-images">
            <Item
                v-for="item in selection"
                class="is-small"
                selected
                :key="item.id"
                :item="item"
                @select="select"
                @deselect="deselect"
            ></Item>
        </div>
    </div>
</template>

<script>
    import Item from './Item.vue';

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
            edit(item) {
                this.$emit('edit', item);
            },
            clear() {
                this.$emit('clear');
            },
        },
    }
</script>
