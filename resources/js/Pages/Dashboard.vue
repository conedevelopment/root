<template>
    <div class="app-widget">
        <Widget
            v-for="widget in widgets"
            :key="widget.key"
            v-bind="widget"
        ></Widget>
    </div>
</template>

<script>
    import Widget from './../Components/Widgets/Handler';

    export default {
        components: {
            Widget,
        },

        props: {
            widgets: {
                type: Array,
                default: () => [],
            },
        },

        layout: function (h, page) {
            return h(this.resolveDefaultLayout(), () => page);
        },
    }
</script>
