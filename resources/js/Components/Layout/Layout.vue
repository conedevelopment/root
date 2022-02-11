<template>
    <div class="app">
        <Sidebar :resources="resources" :user="user"></Sidebar>
        <main class="app-body">
            <Nav :user="user"></Nav>
            <div class="app-body__inner">
                <Header></Header>
                <slot></slot>
            </div>
        </main>
        <form id="logout-form" action="/logout" method="POST" style="display: none;">
            <input type="hidden" name="_token" :value="$page.props.csrf_token">
        </form>
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
