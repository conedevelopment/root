<template>
    <aside class="js-app-sidebar app-sidebar">
        <div class="app-sidebar__header">
            <a href="/">
                <img class="app-sidebar__logo" src="/vendor/root/root-logo-dark.svg" alt="Root">
            </a>
        </div>
        <nav class="navigation app-sidebar__navigation" aria-label="Site">
            <ul>
                <li class="navigation-item">
                    <a class="navigation-item__link" href="#">
                        <Icon class="navigation-item__icon" name="dashboard"></Icon>
                        <span class="navigation-item__caption">Dashboard</span>
                    </a>
                </li>
            </ul>
            <p class="app-sidebar__title">Content</p>
            <ul>
                <li class="navigation-item is-open">
                    <a class="navigation-item__link" href="#">
                        <Icon class="navigation-item__icon" name="post"></Icon>
                        <span class="navigation-item__caption">Posts</span>
                    </a>
                    <ul class="navigation-submenu">
                        <li>
                            <a href="#" aria-current="page">All Posts</a>
                        </li>
                        <li>
                            <a href="#">New Post</a>
                        </li>
                    </ul>
                </li>
            </ul>
            <p class="app-sidebar__title">Administration</p>
            <ul>
                <li class="navigation-item">
                    <a class="navigation-item__link" href="#">
                        <Icon class="navigation-item__icon" name="person"></Icon>
                        <span class="navigation-item__caption">Users</span>
                    </a>
                </li>
            </ul>
        </nav>
        <div class="app-sidebar__footer">
            <div class="user-menu-wrapper">
                <button class="js-user-menu user-menu" aria-expanded="false" aria-label="User Menu">
                    <img class="user-menu__avatar" src="https://s.gravatar.com/avatar/fd9479a898c593601efd2fe758a86dba?s=120">
                    <div class="user-menu__caption">
                        <span class="user-menu__name">Adam Laki</span>
                        <span class="user-menu__role">Administrator</span>
                    </div>
                    <div class="js-user-menu-toggle user-menu__toggle">
                        <Icon class="user-menu__arrow" name="unfold-more"></Icon>
                    </div>
                </button>
                <ul
                    class="js-user-menu-navigation user-menu-navigation user-menu-navigation--top"
                    aria-labelledby="user-user-navigation"
                >
                    <li>
                        <a class="user-menu-navigation__item" aria-current="" href="#">Personal Informations</a>
                    </li>
                    <li>
                        <a class="user-menu-navigation__item" aria-current="" href="#">Change Password</a>
                    </li>
                    <li>
                        <button class="user-menu-navigation__item" type="button">Logout</button>
                    </li>
                </ul>
            </div>
        </div>
    </aside>
</template>

<script>
    import { Link } from '@inertiajs/inertia-vue3';

    export default {
        components: {
            Link,
        },
    }
</script>
