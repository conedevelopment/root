import Item from './Item';
import { throttle } from './helpers';

document.addEventListener('alpine:init', () => {
    window.Alpine.data('mediaManager', (url, config) => {
        return {
            dragging: false,
            processing: false,
            working: false,
            queue: [],
            items: [],
            nextPageUrl: url,
            init() {
                this.$root.querySelector('.modal__body').addEventListener('scroll', throttle((event) => {
                    if (this.shouldPaginate(event)) {
                        this.fetch();
                    }
                }));
            },
            fetch() {
                this.processing = true;

                window.$http.get(this.nextPageUrl).then((response) => {
                    this.items.push(...response.data.data);
                    this.nextPageUrl = response.data.next_page_url;
                }).catch((error) => {
                    //
                }).finally(() => {
                    this.processing = false;
                });
            },
            shouldPaginate(event) {
                return ! this.processing
                    && this.nextPageUrl !== null
                    && this.items.length > 0
                    && Math.abs(event.target.scrollHeight - event.target.scrollTop - event.target.clientHeight) < 75;
            },
            queueFiles(files) {
                for (let i = 0; i < files.length; i++) {
                    this.queue.unshift(new Item(files[i], {
                        url: url,
                        chunkSize: config.chunk_size,
                    }));
                }

                if (! this.working) {
                    this.work();
                }
            },
            work() {
                const next = this.queue.findLast((item) => ! item.failed);

                if (next) {
                    this.working = true;

                    next.handle(url).then((item) => {
                        this.queue.splice(this.queue.indexOf(next), 1);
                        this.items.unshift(item);
                    }).catch((error) => {
                            //
                    }).finally(() => {
                        this.working = false;
                        this.work();
                    });
                }
            },
            retry(item) {
                item.retry();

                if (! this.working) {
                    this.work();
                }
            },
            select(item) {
                config.multiple
                    ? this.selection.push(item)
                    : this.selection = [item];
            },
            deselect(item) {
                this.selection.splice(
                    this.selection.findIndex((selected) => selected.value === item.value), 1
                );
            },
            toggle(item) {
                this.selected(item) ? this.deselect(item) : this.select(item);
            },
            selected(item) {
                return this.selection.findIndex((selected) => selected.value === item.value) > -1;
            },
        };
    });
});
