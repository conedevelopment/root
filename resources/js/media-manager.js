import Item from './Item';
import { throttle } from './helpers';

document.addEventListener('alpine:init', () => {
    window.Alpine.data('mediaManager', (url, config = {}) => {
        return {
            dragging: false,
            processing: false,
            working: false,
            queue: [],
            items: [],
            next_page_url: url,
            init() {
                this.$root.querySelector('.modal__body').addEventListener('scroll', throttle((event) => {
                    if (this.shouldPaginate(event)) {
                        this.fetch();
                    }
                }));
            },
            fetch() {
                this.processing = true;

                window.$http.get(this.next_page_url).then((response) => {
                    this.items.push(...response.data.data);
                    this.next_page_url = response.data.next_page_url;
                }).catch((error) => {
                    //
                }).finally(() => {
                    this.processing = false;
                });
            },
            shouldPaginate(event) {
                return ! this.processing
                    && this.next_page_url !== null
                    && this.items.length > 0
                    && Math.abs(event.target.scrollHeight - event.target.scrollTop - event.target.clientHeight) < 75;
            },
            queueFiles(files) {
                for (let i = 0; i < files.length; i++) {
                    this.queue.unshift(new Item(files[i]));
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
                const index = this.selection.findIndex((selected) => selected.value === item.value);

                if (index === -1) {
                    item.selected = true;

                    config.multiple
                        ? this.selection.push(item)
                        : this.selection = [item];
                }
            },
            deselect(item) {
                const index = this.selection.findIndex((selected) => selected.value === item.value);

                if (index >= 0) {
                    item.selected = false;

                    this.selection.splice(index, 1);
                }
            },
            toggle(item) {
                item.selected ? this.deselect(item) : this.select(item);
            },
        };
    });
});
