document.addEventListener('alpine:init', () => {
    window.Alpine.data('mediaManager', (url, config = {}) => {
        return {
            dragging: false,
            processing: false,
            response: {
                data: [],
                next_page_url: null,
                prev_page_url: null,
            },
            init() {
                //
            },
            fetch() {
                //
            },
            handleFiles(files) {
                this.dragging = false;
            },
        };
    });
});
