<template>
    <aside class="app-sidebar" :class="{ 'is-open': isOpen }">
        <div class="app-sidebar__header">
            <Link :href="$app.config.url">
                <img class="app-sidebar__logo" src="/vendor/root/root-logo-dark.svg" alt="Root">
            </Link>
        </div>
        <nav class="navigation app-sidebar__navigation" :aria-label="__('Site')">
            <ul>
                <li class="navigation-item" :class="{ 'is-open': $app.config.url === $page.props.url }">
                    <Link
                        class="navigation-item__link"
                        :href="$app.config.url"
                        :class="{ 'is-active': $app.config.url === $page.props.url }"
                    >
                        <Icon class="navigation-item__icon" name="dashboard"></Icon>
                        <span class="navigation-item__caption">{{ __('Dashboard') }}</span>
                    </Link>
                </li>
            </ul>
            <p class="app-sidebar__title">{{ __('Resources') }}</p>
            <ul>
                <li
                    v-for="resource in $app.resources"
                    class="navigation-item"
                    :class="{ 'is-open': isActive(resource.urls.index) }"
                    :key="resource.key"
                >
                    <Link
                        class="navigation-item__link"
                        :href="resource.urls.index"
                        :class="{ 'is-active': isActive(resource.urls.index) }"
                    >
                        <Icon class="navigation-item__icon" :name="resource.icon"></Icon>
                        <span class="navigation-item__caption">{{ resource.name }}</span>
                    </Link>
                    <!-- <ul class="navigation-submenu">
                        <li></li>
                    </ul> -->
                </li>
            </ul>
        </nav>
        <div class="app-sidebar__footer">
            <UserMenu></UserMenu>
        </div>
    </aside>
</template>

<script>
    import { Link } from '@inertiajs/inertia-vue3';
    import Closable from './../../Mixins/Closable';
    import UserMenu from './UserMenu';

    export default {
        components: {
            Link,
            UserMenu,
        },

        mixins: [Closable],

        mounted() {
            this.$inertia.on('success', this.close);
        },

        methods: {
            isActive(url) {
                return this.$page.props.url.startsWith(url);
            },
        },
    }
</script>
