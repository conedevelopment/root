<template>
    <div class="app">
        <Sidebar :resources="resources"></Sidebar>
        <main class="app-body">
            <Nav></Nav>
            <div class="app-body__inner">
                <Header></Header>
                <slot></slot>
            </div>
        </main>
    </div>
</template>

<script>
    import Header from './Header';
    import Nav from './Nav';
    import Sidebar from './Sidebar';

    export default {
        components: {
            Header,
            Nav,
            Sidebar,
        },

        computed: {
            resources() {
                return window.Root.resources;
            },
            user() {
                return window.Root.user;
            },
        },
    }
</script>
