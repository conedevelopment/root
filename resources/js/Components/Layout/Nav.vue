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
                    <Icon class="btn__icon btn__icon--start" :name="isOpen ? 'menu-open' : 'menu'"></Icon>
                </button>
                <button
                    type="button"
                    class="btn btn--secondary btn--icon btn--has-counter"
                    aria-label="Like at oitm"
                >
                    <Icon class="btn__icon btn__icon--start" name="notification"></Icon>
                    <span class="btn__counter">3</span>
                </button>
                <div class="app-drawer">
                    <h2 class="app-drawer__title">
                        {{ __('Notifications') }}
                        <button type="button" class="btn btn--secondary btn--sm btn--icon">
                            <Icon class="btn__icon btn__icon--sm" name="close"></Icon>
                        </button>
                    </h2>
                    <div class="app-drawer__inner">
                        <div class="accordion-wrapper">
                            <div class="accordion accordion--read">
                                <h2 class="accordion__title">
                                    <button type="button" aria-expanded="false">
                                        <span class="accordion__caption">
                                            Értesítés cím
                                            <span class="accordion__meta">2022. 08. 12.</span>
                                        </span>
                                        <svg class="accordion__icon" aria-hidden="true" focusable="false" height="24px" viewBox="0 0 24 24" width="24px">
                                            <path d="M12,2c-5.52,0 -10,4.48 -10,10c0,5.52 4.48,10 10,10c5.52,0 10,-4.48 10,-10c0,-5.52 -4.48,-10 -10,-10Zm0,18c-4.41,0 -8,-3.59 -8,-8c0,-4.41 3.59,-8 8,-8c4.41,0 8,3.59 8,8c0,4.41 -3.59,8 -8,8Z" fill="currentColor"></path>
                                            <path d="M7,12c0,0.55 0.45,1 1,1l8,0c0.55,0 1,-0.45 1,-1c0,-0.55 -0.45,-1 -1,-1l-8,0c-0.55,0 -1,0.45 -1,1Z" fill="currentColor"></path>
                                            <path class="vert" d="M12,7c-0.55,0 -1,0.45 -1,1l0,8c0,0.55 0.45,1 1,1c0.55,0 1,-0.45 1,-1l0,-8c0,-0.55 -0.45,-1 -1,-1Z" fill="currentColor"></path>
                                        </svg>
                                    </button>
                                </h2>
                                <div class="accordion__content hidden">
                                    <p>Curabitur accumsan efficitur turpis et tempus. Nam tincidunt ligula quis venenatis laoreet. Sed faucibus ultricies arcu quis viverra. Fusce sed dictum tellus. Ut est augue, suscipit a quam in, fringilla efficitur lorem. Maecenas ut tellus a purus blandit pretium.</p>
                                </div>
                            </div>
                            <div class="accordion">
                                <h2 class="accordion__title">
                                    <button type="button" aria-expanded="true">
                                        <span class="accordion__caption">
                                            Értesítés cím
                                            <span class="accordion__meta">2022. 08. 12.</span>
                                        </span>
                                        <svg class="accordion__icon" aria-hidden="true" focusable="false" height="24px" viewBox="0 0 24 24" width="24px">
                                            <path d="M12,2c-5.52,0 -10,4.48 -10,10c0,5.52 4.48,10 10,10c5.52,0 10,-4.48 10,-10c0,-5.52 -4.48,-10 -10,-10Zm0,18c-4.41,0 -8,-3.59 -8,-8c0,-4.41 3.59,-8 8,-8c4.41,0 8,3.59 8,8c0,4.41 -3.59,8 -8,8Z" fill="currentColor"></path>
                                            <path d="M7,12c0,0.55 0.45,1 1,1l8,0c0.55,0 1,-0.45 1,-1c0,-0.55 -0.45,-1 -1,-1l-8,0c-0.55,0 -1,0.45 -1,1Z" fill="currentColor"></path>
                                            <path class="vert" d="M12,7c-0.55,0 -1,0.45 -1,1l0,8c0,0.55 0.45,1 1,1c0.55,0 1,-0.45 1,-1l0,-8c0,-0.55 -0.45,-1 -1,-1Z" fill="currentColor"></path>
                                        </svg>
                                    </button>
                                </h2>
                                <div class="accordion__content">
                                    <p>Curabitur accumsan efficitur turpis et tempus. Nam tincidunt ligula quis venenatis laoreet. Sed faucibus ultricies arcu quis viverra. Fusce sed dictum tellus. Ut est augue, suscipit a quam in, fringilla efficitur lorem. Maecenas ut tellus a purus blandit pretium.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <UserMenu direction="bottom"></UserMenu>
            </div>
        </div>
    </div>
</template>

<script>
    import { Link } from '@inertiajs/inertia-vue3';
    import UserMenu from './UserMenu.vue';

    export default {
        components: {
            Link,
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
