export default class Item
{
    constructor(file, url)
    {
        this.url = url;
        this.file = file;
        this.error = null;
        this.failed = false;
        this.processing = false;
        this.hash = this._createHash();
        this.chunks = this._createChunks();
        this.uploaded = 0;
        this.progress = 0;
    }

    handle()
    {
        return this.chunks.reduce((promise, chunk, index) => {
            return promise.then(() => {
                return this.upload(chunk, index + 1)
            });
        }, Promise.resolve(null));
    }

    upload(chunk, index)
    {
        const formData = new FormData();

        formData.set('file', chunk, `${this.hash}__${this.file.name}.chunk`);

        return window.$http.post(this.url, formData, {
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
            this.processing = true;

            this.error = error.response.data.message;
            this.failed = true;

            throw new Error();
        });
    }

    cancel()
    {
        //
    }

    retry()
    {
        this.hash = this._createHash();
        this.failed = false;
    }

    _createHash()
    {
        return Math.random().toString(36).replace(/[^a-z]+/g, '').substring(0, 5);
    }

    _createChunks()
    {
        let chunks = [];

        const size = 1024 * 1024;
        const count = Math.ceil(this.file.size / size);

        for (let i = 0; i < count; i++) {
            chunks.push(this.file.slice(
                i * size, Math.min(i * size + size, this.file.size), this.file.type
            ));
        }

        return chunks;
    }
}
