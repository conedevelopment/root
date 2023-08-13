document.addEventListener('alpine:init', () => {
    window.Alpine.data('mediaManager', (url, config = {}) => {
        return {
            dragging: false,
            processing: false,
            response: {
                data: [],
                next_page_url: null,
            },
            init() {
                //
            },
            fetch() {
                this.processing = false;

                window.fetch(url, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                }).then((response) => {
                    return response.ok ? response.json() : Promise.reject(response);
                }).then((data) => {
                    this.response.data.push(...data.data);
                    this.response.next_page_url = data.next_page_url;
                }).catch((error) => {
                    //
                }).finally(() => {
                    this.processing = false;
                });
            },
            handleFiles(files) {
                this.dragging = false;
            },
        };
    });
});
