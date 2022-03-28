<template>
    <div class="mobile-header">
        <div class="mobile-header__inner">
            <Link :href="$app.config.url">
                <img class="mobile-header__logo" src="/vendor/root/root-logo-dark.svg" :alt="$app.config.name">
            </Link>
            <div class="mobile-header__actions">
                <button
                    type="button"
                    class="btn btn--secondary btn--icon"
                    @click="toggleSidebar"
                >
                    <Icon class="btn__icon btn__icon--start" name="menu"></Icon>
                </button>
                <UserMenu direction="bottom"></UserMenu>
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

        methods: {
            toggleSidebar() {
                this.$parent.$refs.sidebar.toggle();
            },
        },
    }
</script>
