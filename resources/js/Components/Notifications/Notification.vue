<template>
    <div class="notification" :class="{ 'is-read': notification.read_at !== null }">
        <h2 class="notification__title">
            <button type="button" class="notification__btn" :aria-expanded="isOpen" @click="toggle">
                <span class="notification-heading">
                    <span class="notification-heading__title">
                        {{ notification.formatted_type }}
                    </span>
                    <span class="notification-heading__meta">
                        <time :datetime="notification.created_at">{{ notification.formatted_created_at }}</time>
                    </span>
                </span>
                <svg aria-hidden="true" focusable="false" height="24px" viewBox="0 0 24 24" width="24px">
                    <path d="M12,2c-5.52,0 -10,4.48 -10,10c0,5.52 4.48,10 10,10c5.52,0 10,-4.48 10,-10c0,-5.52 -4.48,-10 -10,-10Zm0,18c-4.41,0 -8,-3.59 -8,-8c0,-4.41 3.59,-8 8,-8c4.41,0 8,3.59 8,8c0,4.41 -3.59,8 -8,8Z" fill="currentColor"></path>
                    <path d="M7,12c0,0.55 0.45,1 1,1l8,0c0.55,0 1,-0.45 1,-1c0,-0.55 -0.45,-1 -1,-1l-8,0c-0.55,0 -1,0.45 -1,1Z" fill="currentColor"></path>
                    <path class="vert" d="M12,7c-0.55,0 -1,0.45 -1,1l0,8c0,0.55 0.45,1 1,1c0.55,0 1,-0.45 1,-1l0,-8c0,-0.55 -0.45,-1 -1,-1Z" fill="currentColor"></path>
                </svg>
            </button>
        </h2>
        <div class="notification__content" v-show="isOpen" v-html="notification.content"></div>
    </div>
</template>

<script>
    import Closable from './../../Mixins/Closable';

    export default {
        mixins: [Closable],

        props: {
            notification: {
                type: Object,
                required: true,
            },
        },

        mounted() {
            this.$dispatcher.on('open', () => {
                if (this.notification.read_at === null && ! this.processing) {
                    this.read();
                }
            });
        },

        data() {
            return {
                processing: false,
            };
        },

        methods: {
            read() {
                this.processing = true;

                this.$http.patch(`/api/notifications/${this.notification.id}`).then((response) => {
                    Object.assign(this.notification, response.data);
                }).catch((error) => {
                    //
                }).finally(() => {
                    this.processing = false;
                });
            },
        },
    }
</script>
