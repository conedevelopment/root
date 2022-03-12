<template>
    <div class="user-menu-wrapper">
        <button
            type="button"
            class="user-menu"
            :aria-expanded="isOpen ? 'true' : 'false'"
            :aria-label="__('User Menu')"
            @click="toggle"
        >
            <img class="user-menu__avatar" :src="$app.user.avatar">
            <div class="user-menu__caption">
                <span class="user-menu__name">{{ $app.user.name }}</span>
                <span class="user-menu__role">{{ $app.user.email }}</span>
            </div>
            <div class="user-menu__toggle">
                <Icon class="user-menu__arrow" name="unfold-more"></Icon>
            </div>
        </button>
        <ul
            class="user-menu-navigation user-menu-navigation--top"
            aria-labelledby="user-user-navigation"
        >
            <li>
                <button type="submit" form="logout-form" class="user-menu-navigation__item">
                    {{ __('Logout') }}
                </button>
            </li>
        </ul>
    </div>
</template>

<script>
    import Closable from './../../Mixins/Closable';

    export default {
        mixins: [Closable],
    }
</script>
