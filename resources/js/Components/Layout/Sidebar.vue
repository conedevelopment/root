<template>
    <aside class="js-app-sidebar app-sidebar">
        <div class="app-sidebar__header">
            <Link href="/">
                <img class="app-sidebar__logo" src="/vendor/root/root-logo-dark.svg" alt="Root">
            </Link>
        </div>
        <nav class="navigation app-sidebar__navigation" :aria-label="__('Site')">
            <ul>
                <li class="navigation-item">
                    <Link class="navigation-item__link" href="/">
                        <Icon class="navigation-item__icon" name="dashboard"></Icon>
                        <span class="navigation-item__caption">{{ __('Dashboard') }}</span>
                    </Link>
                </li>
            </ul>
            <p class="app-sidebar__title">{{ __('Resources') }}</p>
            <ul>
                <li v-for="resource in resources" :key="resource.key" class="navigation-item">
                    <Link :href="resource.urls.index" class="navigation-item__link">
                        <span class="navigation-item__caption">{{ resource.name }}</span>
                    </Link>
                </li>
            </ul>
        </nav>
        <div class="app-sidebar__footer">
            <UserMenu :user="user"></UserMenu>
        </div>
    </aside>
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
            resources: {
                type: Array,
                default: () => [],
            },
            user: {
                type: Object,
                required: true,
            },
        },
    }
</script>
