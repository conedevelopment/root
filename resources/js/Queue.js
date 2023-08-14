import Item from './Item';

export default class Queue
{
    constructor(url)
    {
        this.url = url;
        this.items = [];
        this.processing = false;
    }

    push(file)
    {
        this.items.unshift(new Item(file, this.url));
    }

    work()
    {
        if (this.items.filter((item) => ! item.failed).length === 0) {
            this.processing = false;

            return;
        }

        if (this.processing) {
            return;
        }

        const index = this.items.findIndex((item) => ! item.failed);

        if (index === -1) {
            return;
        }

        this.items[index]
            .handle()
            .then((item) => {
                console.log(item);
                // this.items.splice(index, 1);
            })
            .catch((error) => {
                //
            })
            .finally(() => {
                this.processing = false;
                // this.work();
            });
    }
}
