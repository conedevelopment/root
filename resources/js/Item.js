export default class Item
{
    constructor(file)
    {
        this.file = file;
        this.chunks = this._createChunks();
        this.error = null;
        this.failed = false;
        this.hash = this._createHash();
        this.progress = 0;
        this.uploaded = 0;
    }

    handle(url)
    {
        return this.chunks.reduce((promise, chunk, index) => {
            return promise.then(() => {
                return this.upload(url, chunk, index + 1)
            });
        }, Promise.resolve(null));
    }

    upload(url, chunk, index)
    {
        const formData = new FormData();

        formData.set('file', chunk, `${this.hash}${this.file.name}`);

        return window.$http.post(url, formData, {
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

            throw new Error();
        });
    }

    cancel()
    {
        //
    }

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

    _createHash()
    {
        return Math.random().toString(36).replace(/[^a-z]+/g, '').substring(0, 10);
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
