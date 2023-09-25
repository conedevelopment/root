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
                    this.notifications.push(...response.data.data);
                    this.nextPageUrl = response.data.next_page_url;
                }).catch((error) => {
                    //
                }).finally(() => {
                    this.processing = false;
                });
            },
            markAsRead(notification) {
                if (notification.is_read) {
                    return;
                }

                this.processing = true;

                window.$http.patch(notification.url).then((response) => {
                    this.unread--;
                    notification.is_read = true;
                }).catch((error) => {
                    //
                }).finally(() => {
                    this.processing = false;
                });
            },
        };
    });
});
