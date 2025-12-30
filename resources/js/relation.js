document.addEventListener('alpine:init', () => {
    window.Alpine.data('relation', (url, config) => {
        return {
            processing: false,
            items: [],
            config: config,
            init() {
                //
            },
            fetch() {
                const data = new FormData(this.$refs.form);
                const query = new URLSearchParams(data).toString();

                this.processing = true;

                window.$http.get(`${url}?${query}`).then((response) => {
                    this.items = response.data.data;
                }).catch((error) => {
                    this.items = [];
                }).finally(() => {
                    this.processing = false;
                });
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
