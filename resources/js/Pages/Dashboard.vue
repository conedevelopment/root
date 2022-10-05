<template>
    <div class="app-widget">
        <!--
        <div class="app-widget-row app-widget-row--two-column">
            <div class="card">
                <h2 class="card__title">Widget Title</h2>
                <div class="card__inner">
                    <p>Welcome to Root!</p>
                </div>
            </div>
            <div class="card">
                <h2 class="card__title">Widget Title</h2>
                <div class="card__inner">
                    <p>Welcome to Root!</p>
                </div>
            </div>
        </div>
        <div class="app-widget-row app-widget-row--three-column">
            <div class="card">
                <h2 class="card__title">Widget Title</h2>
                <div class="card__inner">
                    <p>Welcome to Root!</p>
                </div>
            </div>
            <div class="card">
                <h2 class="card__title">Widget Title</h2>
                <div class="card__inner">
                    <p>Welcome to Root!</p>
                </div>
            </div>
            <div class="card">
                <h2 class="card__title">Widget Title</h2>
                <div class="card__inner">
                    <p>Welcome to Root!</p>
                </div>
            </div>
        </div>
        <div class="app-widget-row">
            <div class="card">
                <h2 class="card__title">Widget Title</h2>
                <div class="card__inner">
                    <p>Welcome to Root!</p>
                </div>
            </div>
        </div>
        -->
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
