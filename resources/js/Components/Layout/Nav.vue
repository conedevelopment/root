<template>
    <div class="mobile-header">
        <div class="mobile-header__inner">
            <Link href="/">
                <img class="mobile-header__logo" src="/vendor/root/root-logo-dark.svg" alt="Root">
            </Link>
            <div class="mobile-header__actions">
                <button
                    type="button"
                    class="js-toggle-sidebar btn btn--secondary btn--icon"
                    @click="toggleSidebar"
                >
                    <Icon class="btn__icon btn__icon--start" name="menu-open"></Icon>
                </button>
                <UserMenu :user="user"></UserMenu>
            </div>
        </div>
    </div>
</template>

<script>
    import { Link } from '@inertiajs/inertia-vue3';
    import UserMenu from './UserMenu';

    export default {
        components: {
            Link,
            UserMenu,
        },

        props: {
            user: {
                type: Object,
                required: true,
            },
        },

        methods: {
            toggleSidebar() {
                this.$parent.$refs.sidebar.toggle();
            },
        },
    }
</script>
