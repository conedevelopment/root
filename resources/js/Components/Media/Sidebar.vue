<template>
    <div class="media-sidebar">
        <div class="media-sidebar__section">
            <h3 class="media-sidebar__title">{{ __(':files files selected', { files: items.length }) }}</h3>
            <div class="accordion-wrapper">
                <Item
                    v-for="item in items"
                    :key="item.id"
                    :item="item"
                    @deselect="deselect(item)"
                ></Item>
            </div>
        </div>
    </div>
</template>

<script>
    import Item from './SidebarItem';

    export default {
        components: {
            Item,
        },

        props: {
            items: {
                type: Array,
                required: true,
            },
        },

        methods: {
            deselect(selected) {
                this.items.splice(this.items.findIndex((item) => item.id === selected.id), 1);
            },
        },
    }
</script>
