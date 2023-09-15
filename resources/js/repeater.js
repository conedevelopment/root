document.addEventListener('alpine:init', () => {
    window.Alpine.data('repeater', (url, options = [], config = {}) => {
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
        };
    });
});
