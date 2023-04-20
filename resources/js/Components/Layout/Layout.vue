<template>
    <div class="app">
        <Sidebar ref="sidebar"/>
        <main class="app-body">
            <Nav/>
            <div class="app-body__inner">
                <Header :title="title" :breadcrumbs="breadcrumbs"/>
                <div v-if="alerts.length > 0" class="app-alert">
                    <Alert v-for="(alert, index) in alerts" :key="`${alert.timestamp}-${index}`" v-bind="alert"/>
                </div>
                <slot></slot>
            </div>
        </main>
        <form id="logout-form" action="/logout" method="POST" style="display: none;">
            <input type="hidden" name="_token" :value="token">
        </form>
    </div>
</template>

<script>
    import Alert from './../Alert.vue';
    import Header from './Header.vue';
    import Nav from './Nav.vue';
    import Sidebar from './Sidebar.vue';

    export default {
        components: {
            Alert,
            Header,
            Nav,
            Sidebar,
        },

        mounted() {
            const title = document.title;

            document.title = `${title} | ${this.title}`;

            this.$inertia.on('finish', () => {
                document.title = `${title} | ${this.title}`;
            });
        },

        computed: {
            token() {
                return this.$page.props.csrf_token;
            },
            alerts() {
                return this.$page.props.alerts;
            },
            title() {
                return this.$page.props.title || this.__('Dashboard');
            },
            breadcrumbs() {
                return this.$page.props.breadcrumbs;
            },
        },
    }
</script>
