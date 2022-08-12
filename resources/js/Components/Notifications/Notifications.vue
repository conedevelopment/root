<template>
    <button
        ref="button"
        type="button"
        class="btn btn--secondary btn--icon btn--has-counter"
        :aria-label="__('View notifications')"
        @click="toggle"
    >
        <Icon class="btn__icon btn__icon--start" name="notification"></Icon>
        <span v-if="response.total_unread > 0" class="btn__counter">{{ response.total_unread }}</span>
    </button>
    <div ref="container" class="app-drawer" v-show="isOpen" style="overflow: auto;">
        <h2 class="app-drawer__title">
            {{ __('Notifications') }}
            <button type="button" class="btn btn--secondary btn--sm btn--icon" @click="close">
                <Icon class="btn__icon btn__icon--sm" name="close"></Icon>
            </button>
        </h2>
        <div class="app-drawer__inner">
            <div v-if="response.data.length > 0" class="accordion-wrapper">
                <Notification
                    v-for="notification in response.data"
                    :key="notification.id"
                    :notification="notification"
                ></Notification>
            </div>
            <div v-else class="alert alert--info">
                {{ __('No results found.') }}
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

            window.addEventListener('keydown', (event) => {
                if (this.isOpen && event.code === 'Escape') {
                    this.close();
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
                processing: false,
                response: { data: [], total_unread: 0 },
            };
        },

        methods: {
            fetch() {
                this.processing = true;

                this.$http.get('/api/notifications').then((response) => {
                    this.response = response.data;
                }).catch((error) => {
                    //
                }).finally(() => {
                    this.processing = false;
                });
            },
            paginate() {
                this.processing = true;

                this.$http.get(this.response.next_page_url).then((response) => {
                    this.response.data.push(...response.data.data);
                    this.response.next_page_url = response.data.next_page_url;
                    this.response.prev_page_url = response.data.prev_page_url;
                    this.response.total_unread = response.data.total_unread;
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
