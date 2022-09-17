<template>
    <div class="uploader-item" :class="{ 'is-pending': ! processing }">
        <progress
            v-if="error === null"
            max="100"
            style="max-width: 100%;"
            :value="processing ? progress : null"
        ></progress>
        <span v-else class="uploader-item__error">
            <span>{{ error }}</span>
            <button type="button" class="btn btn--secondary btn--sm" @click="retry">
                {{ __('Retry') }}
            </button>
        </span>
    </div>
</template>

<script>
    export default {
        props: {
            hash: {
                type: String,
                required: true,
            },
            file: {
                type: Object,
                required: true,
            },
            url: {
                type: String,
                required: true,
            },
        },

        emits: ['retry'],

        data() {
            return {
                error: null,
                uploaded: 0,
                processing: false,
            };
        },

        computed: {
            progress() {
                return Math.floor((this.uploaded * 100) / this.file.size);
            },
        },

        methods: {
            handle() {
                this.processing = true;

                const chunks = this.createChunks();

                return chunks.reduce((promise, chunk, index) => {
                    return promise.then(() => {
                        return this.upload(chunk, index + 1, chunks.length)
                    });
                }, Promise.resolve(null));
            },
            upload(chunk, index, total) {
                const formData = new FormData();

                formData.set('file', chunk, `${this.hash}__${this.file.name}.chunk`);

                return this.$http.post(this.url, formData, {
                    headers: {
                        'X-Chunk-Hash': this.hash,
                        'X-Chunk-Index': index,
                        'X-Chunk-Total': total,
                        'Content-Type': 'multipart/form-data',
                    },
                    onUploadProgress: (event) => {
                        this.uploaded += event.loaded;
                    },
                }).then((response) => {
                    return response.data;
                }).catch((error) => {
                    this.processing = true;

                    this.error = error.response.data.message || this.__('Something went wrong!');

                    throw new Error();
                });
            },
            cancel() {
                //
            },
            retry() {
                this.error = null;
                this.uploaded = 0;
                this.$emit('retry');
            },
            createChunks() {
                let chunks = [];

                const size = 1024 * 1024;
                const count = Math.ceil(this.file.size / size);

                for (let i = 0; i < count; i++) {
                    chunks.push(this.file.slice(
                        i * size, Math.min(i * size + size, this.file.size), this.file.type
                    ));
                }

                return chunks;
            },
        },
    }
</script>
