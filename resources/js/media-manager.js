import Queue from './Queue';

document.addEventListener('alpine:init', () => {
    window.Alpine.data('mediaManager', (url, config = {}) => {
        return {
            dragging: false,
            processing: false,
            queue: new Queue(url),
            selection: config.selection || [],
            items: [],
            next_page_url: url,
            init() {
                //
            },
            fetch() {
                this.processing = false;

                window.$http.get(this.next_page_url, {
                    //
                }).then((response) => {
                    this.items.push(...response.data.data);
                    this.next_page_url = response.data.next_page_url;
                }).catch((error) => {
                    //
                }).finally(() => {
                    this.processing = false;
                });
            },
            handleFiles(files) {
                this.dragging = false;

                for (let i = 0; i < files.length; i++) {
                    this.queue.push(files[i]);
                }

                this.queue.work();
            },
        };
    });
});
