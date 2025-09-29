export default class Item
{
    /**
     * Create a new item instance.
     */
    constructor(file, config)
    {
        this.file = file;
        this.error = null;
        this.failed = false;
        this.progress = 0;
        this.uploaded = 0;
        this.config = config;
        this.hash = this._createHash();
        this.chunks = this._createChunks();
    }

    /**
     * Handle the upload of the chunks.
     */
    handle()
    {
        return this.chunks.reduce((promise, chunk, index) => {
            return promise.then(() => {
                return this.upload(chunk, index + 1)
            });
        }, Promise.resolve(null));
    }

    /**
     * Upload the given chunk.
     */
    upload(chunk, index)
    {
        const formData = new FormData();

        formData.set('file', chunk, `${this.hash}${this.file.name}`);

        return window.$http.post(this.config.url, formData, {
            headers: {
                'X-Chunk-Hash': this.hash,
                'X-Chunk-Index': index,
                'X-Chunk-Total': this.chunks.length,
                'Content-Type': 'multipart/form-data',
            },
            onUploadProgress: (event) => {
                this.uploaded += event.loaded;
                this.progress = Math.floor((this.uploaded * 100) / this.file.size);
            },
        }).then((response) => {
            return response.data;
        }).catch((error) => {
            this.error = error.response.data.message;
            this.failed = true;

            throw new Error(error.response.data.message);
        }).finally(() => {
            this.uploaded = 0;
            this.progress = 0;
            this.processing = false;
        });
    }

    /**
     * Cancel the upload.
     */
    cancel()
    {
        //
    }

    /**
     * Retry the upload.
     */
    retry()
    {
        this.chunks = this._createChunks();
        this.error = null;
        this.failed = false;
        this.hash = this._createHash();
        this.processing = false;
        this.progress = 0;
        this.uploaded = 0;
    }

    /**
     * Create a unique hash.
     */
    _createHash()
    {
        return Math.random().toString(36).replace(/[^a-z]+/g, '').substring(0, 10);
    }

    /**
     * Create the file chunks.
     */
    _createChunks()
    {
        let chunks = [];

        const size = this.config.chunkSize;
        const count = Math.ceil(this.file.size / size);

        for (let i = 0; i < count; i++) {
            chunks.push(this.file.slice(
                i * size, Math.min(i * size + size, this.file.size), this.file.type
            ));
        }

        return chunks;
    }
}
