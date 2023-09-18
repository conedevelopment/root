document.addEventListener('alpine:init', () => {
    window.Alpine.data('dropdown', (options, config) => {
        return {
            selection: [],
            options: options,
            search: null,
            open: false,
            highlighted: null,
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
            toggleFromKeyboard() {
                if (this.highlightedItemIndex === null) {
                    this.options[this.highlightedItemIndex].selected = ! this.options[this.highlightedItemIndex].selected;
                }
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
                console.log(item);
                this.selected(item) ? this.deselect(item) : this.select(item);
            },
            selected(item) {
                return this.selection.findIndex((selected) => selected.value === item.value) > -1;
            },
        };
    });
});
