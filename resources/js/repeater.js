document.addEventListener('alpine:init', () => {
    window.Alpine.data('repeater', (url, config) => {
        return {
            processing: false,
            items: [],
            fetch() {
                this.processing = true;

                window.$http.post(url).then((response) => {
                    //
                }).catch((error) => {
                    //
                }).finally(() => {
                    this.processing = false;
                });
            },
        };
    });
});
