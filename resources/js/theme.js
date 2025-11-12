document.addEventListener('alpine:init', () => {
    window.Alpine.data('theme', () => {
        return {
            theme: window.$cookie.get('theme', 'system'),
            change(theme) {
                document.dispatchEvent(new CustomEvent('theme:change', {
                    detail: { theme },
                }));

                this.theme = theme;

                this.$root.querySelector(`.theme-switcher__${theme}-mode`).focus();
            },
        };
    });
});
