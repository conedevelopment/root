document.addEventListener('alpine:init', () => {
    window.Alpine.data('theme', () => {
        return {
            systemMode: window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light',
            theme: window.$cookie.get('theme', 'system'),
            init() {
                window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (event) => {
                    if (this.theme === 'system') {
                        document.documentElement.setAttribute('data-theme-mode', event.matches ? 'dark' : 'light');
                    }
                });

                document.documentElement.setAttribute('data-theme-mode', this.theme === 'system' ? this.systemMode : this.theme);

                this.changeAssets();

                const observer = new MutationObserver(() => {
                    this.changeAssets();
                });

                observer.observe(document.documentElement, { attributes: true });
            },
            change(theme) {
                document.documentElement.classList.add('no-transition');

                window.$cookie.set('theme', theme);

                this.theme = theme;

                document.documentElement.setAttribute('data-theme-mode', theme === 'system' ? this.systemMode : theme);

                this.$root.querySelector(`.theme-switcher__${theme}-mode`).focus();

                document.documentElement.classList.remove('no-transition');
            },
            changeAssets() {
                const theme = document.documentElement.getAttribute('data-theme-mode') === 'system'
                    ? this.systemMode
                    : document.documentElement.getAttribute('data-theme-mode');

                document.querySelectorAll('img[data-theme-mode]').forEach((el) => {
                    el.src = el.getAttribute(`data-${theme}-asset`);
                });
            },
        };
    });
});
