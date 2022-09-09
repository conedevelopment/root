<template>
    <div class="uploader-item">
        <div v-if="! error" class="uploader-item__progress" :style="{ width: `${progress}%` }"></div>
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
            file: {
                type: File,
                required: true,
            },
            url: {
                type: String,
                required: true,
            },
        },

        emits: ['success'],

        beforeMount() {
            this.generateHash();
            this.createChunks();
        },

        data() {
            return {
                chunks: [],
                hash: null,
                error: null,
                uploaded: 0,
            };
        },

        computed: {
            progress() {
                return Math.floor((this.uploaded * 100) / this.file.size);
            },
        },

        methods: {
            upload() {
                const formData = new FormData();

                formData.set('is_last', this.chunks.length === 1);
                formData.set('file', this.chunks[0], `${this.hash}__${this.file.name}.chunk`);

                this.$http.post(this.url, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data',
                    },
                    onUploadProgress: (event) => {
                        this.uploaded += event.loaded;
                    },
                }).then((response) => {
                    this.chunks.shift();

                    if (this.chunks.length === 0) {
                        this.$emit('success', response.data);
                    }
                }).catch((error) => {
                    this.error = this.__('Something went wrong!');
                });
            },
            cancel() {
                //
            },
            retry() {
                this.error = null;
                this.uploaded = 0;
                this.generateHash();
                this.createChunks();
            },
            generateHash() {
                this.hash = Math.random().toString(36).replace(/[^a-z]+/g, '').substring(0, 5);
            },
            createChunks() {
                let chunks = [];
                const size = 1024 * 2048;
                const count = Math.ceil(this.file.size / size);

                for (let i = 0; i < count; i++) {
                    chunks.push(this.file.slice(
                        i * size, Math.min(i * size + size, this.file.size), this.file.type
                    ));
                }

                this.chunks = chunks;
            },
        },
    }
</script>
