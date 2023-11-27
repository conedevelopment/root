document.addEventListener('alpine:init', () => {
    window.Alpine.data('table', () => {
        return {
            selection: [],
            all: false,
            init() {
                //
            },
        };
    });
});
