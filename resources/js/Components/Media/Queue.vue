<template>
    <QueuedItem
        v-for="item in queue"
        :ref="item.hash"
        :key="item.hash"
        :hash="item.hash"
        :file="item.file"
        :url="url"
        @retry="() => retry(item)"
    ></QueuedItem>
</template>

<script>
    import QueuedItem from './QueuedItem.vue';

    export default {
        components: {
            QueuedItem,
        },

        props: {
            url: {
                type: String,
                required: true,
            },
        },

        emits: ['processed'],

        data() {
            return {
                queue: [],
                processing: null,
            };
        },

        methods: {
            makeHash() {
                return Math.random().toString(36).replace(/[^a-z]+/g, '').substring(0, 5);
            },
            push(file) {
                this.queue.push({
                    file,
                    failed: false,
                    hash: this.makeHash(),
                });

                this.$nextTick(() => {
                    this.work();
                });
            },
            work() {
                if (this.processing !== null) {
                    return;
                }

                const index = this.queue.findIndex((item) => ! item.failed);

                if (index > -1) {
                    this.processing = index;

                    this.$refs[this.queue[index].hash][0]
                        .handle()
                        .then((item) => {
                            this.$emit('processed', item);
                            this.queue.splice(index, 1);
                        })
                        .catch((error) => {
                            this.queue[index].failed = true;
                        })
                        .finally(() => {
                            this.processing = null;
                            this.work();
                        });
                }
            },
            retry(item) {
                item.failed = false;

                this.work();
            },
        },
    }
</script>
