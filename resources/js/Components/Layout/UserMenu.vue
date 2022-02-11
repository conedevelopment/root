<template>
    <div class="user-menu-wrapper">
        <button
            type="button"
            class="js-user-menu user-menu"
            :aria-expanded="isOpen ? 'true' : 'false'"
            :aria-label="__('User Menu')"
            @click="toggle"
        >
            <img class="user-menu__avatar" :src="user.avatar">
            <div class="user-menu__caption">
                <span class="user-menu__name">{{ user.name }}</span>
                <span class="user-menu__role">{{ user.email }}</span>
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
                <Link href="#" class="user-menu-navigation__item" aria-current="">
                    Personal Informations
                </Link>
            </li>
            <li>
                <Link href="#" class="user-menu-navigation__item" aria-current="">
                    Change Password
                </Link>
            </li>
            <li>
                <button type="submit" form="logout-form" class="user-menu-navigation__item">
                    {{ __('Logout') }}
                </button>
            </li>
        </ul>
    </div>
</template>

<script>
    import { Link } from '@inertiajs/inertia-vue3';
    import Closable from './../../Mixins/Closable';

    export default {
        components: {
            Link,
        },

        mixins: [Closable],

        props: {
            user: {
                type: Object,
                required: true,
            },
        },
    }
</script>
