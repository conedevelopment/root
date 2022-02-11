<template>
    <div class="mobile-header">
        <div class="mobile-header__inner">
            <a href="#">
                <img class="mobile-header__logo" src="/vendor/root/root-logo-dark.svg" alt="Root">
            </a>
            <div class="mobile-header__actions">
                <button class="js-toggle-sidebar btn btn--secondary btn--icon">
                    <Icon class="btn__icon btn__icon--start" name="menu-open"></Icon>
                </button>
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
                        class="js-user-menu-navigation user-menu-navigation user-menu-navigation--bottom"
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
        </div>
    </div>
</template>

<script>
    import { Link } from '@inertiajs/inertia-vue3';

    export default {
        components: {
            Link,
        },
    }
</script>
