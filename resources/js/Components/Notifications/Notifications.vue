<template>
    <button
        ref="button"
        type="button"
        class="js-notification-open notification-btn"
        aria-controls="notifications"
        :aria-label="__('View notifications (:count unread notifications)', { count: unreadNotificationsCount })"
        :class="{ 'has-unread-notifications': unreadNotificationsCount > 0 }"
        :data-unread-notifications="unreadNotificationsCount"
        @click="toggle"
    >
        <Icon name="notification"></Icon>
    </button>
    <div
        ref="modal"
        class="js-notification-panel modal"
        style="margin: 0;"
        tabindex="-1"
        id="notifications"
        aria-labelledby="notifications-title"
        aria-hidden="true"
        :role="isOpen ? 'dialog' : 'none'"
    >
        <div ref="focusables" class="modal__inner" data-simplebar>
            <div class="modal__title-wrapper">
                <h3 class="modal__title" id="notifications-title">{{ __('Notifications') }}</h3>
                <button
                    type="button"
                    class="js-notification-close btn btn--primary btn--icon modal__close"
                    :aria-label="__('Close notifications')"
                    @click="close"
                >
                    <Icon name="close"></Icon>
                </button>
            </div>
            <div class="js-messages modal__body">
                <div v-if="notifications.data.length === 0" class="alert alert--info" role="alert">
                    {{ __('No notifications are available.') }}
                </div>
                <Notification
                    v-for="notification in notifications.data"
                    :key="notification.id"
                    :notification="notification"
                ></Notification>
            </div>
        </div>
    </div>
    <div
        ref="backdrop"
        class="js-notification-backdrop modal-backdrop"
        style="margin: 0;"
        aria-hidden="true"
        @click="close"
    ></div>
</template>

<script>
    import { throttle } from './../../Support/Helpers';
    import Closable from './../../Mixins/Closable';
    import Notification from './Notification';

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
                if (window.Oitm.user.id === null) {
                    return;
                }

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
