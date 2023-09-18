document.addEventListener('alpine:init', () => {
    window.Alpine.data('dropdown', (options, selection, config) => {
        return {
            selection: selection,
            options: options,
            search: null,
            open: false,
            highlighted: 0,
            highlight(index) {
                this.open = true;

                if (this.options.length === 0) {
                    this.highlighted = null;
                } else if (index >= this.options.length) {
                    this.highlighted = 0;
                } else if (index < 0) {
                    this.highlighted = this.options.length - 1;
                } else {
                    this.highlighted = index;
                }

                this.$refs.listbox.children[this.highlighted + 1].scrollIntoView({
                    behavior: 'smooth',
                    block: 'nearest',
                });
            },
            highlightNext() {
                this.highlight(this.highlighted + 1);
            },
            highlightPrev() {
                this.highlight(this.highlighted - 1);
            },
            select(item) {
                config.multiple
                    ? this.selection.push(item)
                    : this.selection = [item];
            },
            deselect(item) {
                this.selection.splice(
                    this.selection.findIndex((selected) => selected.value === item.value), 1
                );
            },
            toggle(item) {
                this.selected(item) ? this.deselect(item) : this.select(item);
            },
            selected(item) {
                return this.selection.findIndex((selected) => selected.value === item.value) > -1;
            },
            filter(search) {
                if (! search) {
                    return this.options;
                }

                return this.options.filter((option) => {
                    return option.value.includes(search)
                        || option.label.replace(/<[^>]+>/g, '').includes(search);
                });
            },
        };
    });
});
