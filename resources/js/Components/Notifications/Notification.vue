<template>
    <Accordion ref="accordion" :class="{ 'accordion--read': notification.read_at !== null }">
        <template #header>
            <span class="accordion__caption">
                {{ notification.title }}
                <span class="accordion__meta">
                    <time :datetime="notification.created_at">{{ notification.formatted_created_at }}</time>
                </span>
            </span>
        </template>
        <div v-html="notification.content"></div>
    </Accordion>
</template>

<script>
    export default {
        props: {
            notification: {
                type: Object,
                required: true,
            },
        },

        mounted() {
            this.$refs.accordion.$dispatcher.on('open', () => {
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

                this.$http.patch(`${this.$parent.url}/${this.notification.id}`).then((response) => {
                    Object.assign(this.notification, response.data);
                    this.$parent.response.total_unread--;
                }).catch((error) => {
                    //
                }).finally(() => {
                    this.processing = false;
                });
            },
        },
    }
</script>
