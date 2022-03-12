<template>
    <aside class="app-sidebar" :class="{ 'is-open': isOpen }">
        <div class="app-sidebar__header">
            <Link :href="$app.config.url">
                <img class="app-sidebar__logo" src="/vendor/root/root-logo-dark.svg" alt="Root">
            </Link>
        </div>
        <nav class="navigation app-sidebar__navigation" :aria-label="__('Site')">
            <ul>
                <li class="navigation-item">
                    <Link class="navigation-item__link" :href="$app.config.url">
                        <Icon class="navigation-item__icon" name="dashboard"></Icon>
                        <span class="navigation-item__caption">{{ __('Dashboard') }}</span>
                    </Link>
                </li>
            </ul>
            <p class="app-sidebar__title">{{ __('Resources') }}</p>
            <ul>
                <li v-for="resource in $app.resources" :key="resource.key" class="navigation-item">
                    <Link :href="resource.urls.index" class="navigation-item__link">
                        <span class="navigation-item__caption">{{ resource.name }}</span>
                    </Link>
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
    }
</script>
