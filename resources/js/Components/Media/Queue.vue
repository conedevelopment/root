<template>
    <QueuedItem
        v-for="(file, index) in queue"
        ref="queue"
        :key="`queued-${file.name}-${index}`"
        :file="file"
        :url="url"
        @processed="($event) => processed($event, file)"
    ></QueuedItem>
</template>

<script>
    import QueuedItem from './QueuedItem.vue';

    export default {
        components: {
            QueuedItem,
        },

        props: {
            queue: {
                type: Array,
                default: () => [],
            },
            url: {
                type: String,
                required: true,
            },
        },

        mounted() {
            this.process();
        },

        emits: ['update:queue', 'processed'],

        methods: {
            process() {
                if (this.$refs.queue.length > 0) {
                    this.$refs.queue[0].upload();
                }
            },
            processed(item, file) {
                this.$emit('processed', item);

                const queue = Array.from(this.queue);

                queue.splice(queue.indexOf(file), 1);

                this.$emit('update:queue', queue);

                this.$nextTick(() => {
                    this.process();
                });
            },
        },
    }
</script>
