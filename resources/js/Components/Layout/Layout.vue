<template>
    <div class="app">
        <Sidebar ref="sidebar"></Sidebar>
        <main class="app-body">
            <Nav></Nav>
            <div class="app-body__inner">
                <Header></Header>
                <div class="app-body-alerts">
                    <Alert v-for="(alert, index) in alerts" :key="index" v-bind="alert"></Alert>
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
    import Alert from './../Alert';
    import Header from './Header';
    import Nav from './Nav';
    import Sidebar from './Sidebar';

    export default {
        components: {
            Alert,
            Header,
            Nav,
            Sidebar,
        },

        computed: {
            token() {
                return this.$page.props.csrf_token;
            },
            alerts() {
                return this.$page.props.alerts;
            },
        },
    }
</script>
