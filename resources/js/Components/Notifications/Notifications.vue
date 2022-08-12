<template>
    <button
        type="button"
        class="btn btn--secondary btn--icon btn--has-counter"
        aria-label="Like at oitm"
        @click="toggle"
    >
        <Icon class="btn__icon btn__icon--start" name="notification"></Icon>
        <span class="btn__counter">3</span>
    </button>
    <div class="app-drawer" v-show="isOpen">
        <h2 class="app-drawer__title">
            {{ __('Notifications') }}
            <button type="button" class="btn btn--secondary btn--sm btn--icon" @click="close">
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
</template>

<script>
    import { throttle } from './../../Support/Helpers';
    import Closable from './../../Mixins/Closable';
    import Notification from './Notification.vue';

    export default {
        components: {
            Notification,
        },

        mixins: [Closable],

        mounted() {
            this.fetch();

            const mq = window.matchMedia('(prefers-reduced-motion: reduce)');

            window.addEventListener('keydown', (event) => {
                if (this.isOpen && event.code === 'Escape') {
                    this.close();
                }
            });

            this.$dispatcher.on('open', () => {
                this.$refs.modal.removeAttribute('aria-hidden');
                this.$refs.modal.setAttribute('aria-modal', 'true');
                this.$refs.backdrop.removeAttribute('aria-hidden');

                this.$nextTick(() => {
                    this.$refs.modal.focus();
                });

                document.body.style.cssText = 'overflow: hidden; padding-right: ' + this.getScrollbarWidth() + 'px;';
            });

            this.$dispatcher.on('close', (event) => {
                this.$refs.modal.classList.add('animate--out');
                this.$refs.backdrop.classList.add('animate--out');

                if (mq.matches) {
                    this.transitionEndCallback();
                } else {
                    this.$refs.modal.addEventListener('transitionend', this.transitionEndCallback);
                }
            });

            this.$refs.container.addEventListener('scroll', throttle((event) => {
                if (this.shouldPaginate()) {
                    this.paginate();
                }
            }, 300));
        },

        data() {
            return {
                notifications: {},
            };
        },

        computed: {
            unreadNotificationsCount() {
                return this.notifications.filter((notification) => {
                    return notification.read_at === null;
                }).length;
            },
        },

        methods: {
            fetch() {
                this.$http.get('/api/notifications').then((response) => {
                    this.notifications = response.data;
                    this.$nextTick(() => {
                        this.trapFocus();
                    });
                }).catch((error) => {
                    //
                }).finally(() => {
                    //
                });
            },
            getScrollbarWidth() {
                return window.innerWidth - document.documentElement.clientWidth;
            },
            transitionEndCallback() {
                this.$refs.backdrop.setAttribute('aria-hidden', 'true');
                this.$refs.modal.setAttribute('aria-hidden', 'true');
                this.$refs.modal.removeAttribute('aria-modal');

                this.$refs.button.focus();

                this.$refs.modal.removeEventListener('transitionend', this.transitionEndCallback);
                this.$refs.modal.classList.remove('animate--out');
                this.$refs.backdrop.classList.remove('animate--out');

                document.body.removeAttribute('style');
            },
            trapFocus() {
                const focusables = this.$refs.focusables.querySelectorAll('a[href]:not([disabled]), button:not([disabled])');

                window.addEventListener('keydown', (event) => {
                    if (this.isOpen && event.code === 'Tab' && document.activeElement === focusables[focusables.length - 1]) {
                        focusables[0].focus();
                        event.preventDefault();
                    }
                });
            },
            paginate() {
                this.processing = true;

                this.$http.get(this.response.next_page_url).then((response) => {
                    this.response.data.push(...response.data.data);
                    this.response.next_page_url = response.data.next_page_url;
                    this.response.prev_page_url = response.data.prev_page_url;
                }).catch((error) => {
                    //
                }).finally(() => {
                    this.processing = false;
                });
            },
            shouldPaginate() {
                const el = this.$refs.container;

                return ! this.processing
                    && this.response.next_page_url !== null
                    && this.response.data.length > 0
                    && (el.scrollHeight - el.scrollTop - el.clientHeight) < 1;
            },
        },
    }
</script>
