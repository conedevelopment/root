document.addEventListener('alpine:init', () => {
    window.Alpine.data('repeater', (url, options = []) => {
        return {
            processing: false,
            options: options,
            add() {
                this.processing = true;

                window.$http.post(url).then((response) => {
                    this.options.push(response.data);
                }).catch((error) => {
                    //
                }).finally(() => {
                    this.processing = false;
                });
            },
            remove(index) {
                this.options.splice(index, 1);
            },
            swap(from, to) {
                const tmp = this.options[to];

                this.options[to] = this.options[from];

                this.options[from] = tmp;
            },
        };
    });
});
