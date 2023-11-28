document.addEventListener('alpine:init', () => {
    window.Alpine.data('table', (config) => {
        return {
            models: config.models,
            selection: [],
            selectedAllMatchingQuery: false,
            selectedAllModels: false,
            init() {
                this.$watch('selection', () => {
                    this.$refs.selectCheckbox.indeterminate = this.selection.length > 0 && this.selection.length < this.models.length;

                    this.selectedAllModels = this.selection.length > 0 && this.selection.length === this.models.length;
                });
            },
        };
    });
});
