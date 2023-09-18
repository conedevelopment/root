document.addEventListener('alpine:init', () => {
    window.Alpine.data('notifications', (url) => {
        return {
            open: false,
            processing: false,
            notifications: [],
            nextPageUrl: url,
            unread: 0,
            init() {
                this.fetch();
            },
            fetch() {
                this.processing = true;

                window.$http.get(this.nextPageUrl).then((response) => {
                    this.unread = response.data.total_unread;
                }).catch((error) => {
                    //
                }).finally(() => {
                    this.processing = false;
                });
            },
            patch() {
                //
            },
        };
    });
});
