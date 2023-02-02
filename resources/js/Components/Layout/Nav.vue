<template>
    <div class="app-topbar">
        <div class="app-topbar__inner">
            <Link :href="$app.config.url">
                <img class="app-topbar__logo" :src="$app.config.branding.logo" :alt="$app.config.name">
            </Link>
            <div class="app-topbar__actions">
                <button
                    type="button"
                    class="btn btn--secondary btn--icon"
                    data-action="open-navigation"
                    @click="toggleSidebar"
                >
                    <Icon class="btn__icon btn__icon--start" :name="isOpen ? 'menu-open' : 'menu'"/>
                </button>
                <Notifications></Notifications>
                <UserMenu direction="bottom"></UserMenu>
            </div>
        </div>
    </div>
</template>

<script>
    import { Link } from '@inertiajs/vue3';
    import Notifications from './../Notifications/Notifications.vue';
    import UserMenu from './UserMenu.vue';

    export default {
        components: {
            Link,
            Notifications,
            UserMenu,
        },

        mounted() {
            this.sync();
            this.$parent.$refs.sidebar.$dispatcher.on('open', this.sync);
            this.$parent.$refs.sidebar.$dispatcher.on('close', this.sync);
        },

        data() {
            return {
                isOpen: false,
            };
        },

        methods: {
            toggleSidebar() {
                this.$parent.$refs.sidebar.toggle();
            },
            sync() {
                this.isOpen = this.$parent.$refs.sidebar.isOpen;
            },
        },
    }
</script>
