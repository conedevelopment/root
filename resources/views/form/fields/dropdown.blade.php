<div class="form-group--row">
    <label class="form-label" for="colors">Choose colors</label>
    <div class="combobox" x-data="data()" x-id="['dropdown', 'list-item']" x-on:focusin.window="! $refs.panel.contains($event.target) && close()" x-on:keydown.escape.prevent.stop="close($refs.input)">
        <div class="combobox__selected-items" x-show="selectedItems().length > 0">
            <template x-for="(item, index) in selectedItems" :key="item.id">
                <span class="combobox-item" x-show="item.selected">
                    <span x-text="item.label"></span>
                    <button :aria-label="`Remove ${item.label}`" @click="unselectItemById(item.id)" class="btn btn--primary btn--sm btn--icon">
                        <svg aria-hidden='true' fill='none' focusable='false' height='24' stroke-linecap='round' stroke-linejoin='round' stroke-width='3' stroke='currentColor' viewBox='0 0 24 24' width='24' xmlns='http://www.w3.org/2000/svg' class='btn__icon'>
                            <line x1='18' y1='6' x2='6' y2='18'></line>
                            <line x1='6' y1='6' x2='18' y2='18'></line>
                        </svg>
                    </button>
                </span>
            </template>
        </div>
        <div class="combobox__inner" @click.outside="close($refs.input)" x-ref="panel">
            <input :aria-activedescendant="highlightedItemId()" :aria-controls="$id('dropdown')" :aria-expanded="open" @click="toggle()" aria-autocomplete="list" class="form-control combobox__control" id="colors" placeholder="Search for a color" role="combobox" type="text" x-model="search" x-on:keyup.down.prevent="highlightNextItem" x-on:keyup.enter.prevent="toggleFromKeyboard" x-on:keyup.up.prevent="highlightPreviousItem" x-on:keyup="if ($event.key !== 'Escape' && $event.key !== 'Tab' && $event.key !== 'Shift') { open = true }" x-ref="input">
            <div class="combobox__dropdown" x-show="open || search.length > 0">
                <ul :id="$id('dropdown')" aria-label="Colors" aria-multiselectable="true" role="listbox" tabindex="-1" x-ref="listbox">
                    <template x-for="(item, index) in filteredItems" :key="item.id">
                        <li :aria-selected="item.selected" :class="{'highlighted': index === highlightedItemIndex}" :id="$id('list-item', item.id)" @click.prevent="item.selected = ! item.selected; highlightedItemIndex = null;" role="option">
                            <span x-text="item.label"></span>
                            <span x-show="item.selected">
                                <svg aria-hidden='true' fill='none' focusable='false' height='24' stroke-linecap='round' stroke-linejoin='round' stroke-width='3' stroke='currentColor' viewBox='0 0 24 24' width='24' xmlns='http://www.w3.org/2000/svg' class='icon'>
                                    <polyline points='20 6 9 17 4 12'></polyline>
                                </svg>
                            </span>
                        </li>
                    </template>
                    <li class="combobox__no-results" x-show="search !== '' && filteredItems().length === 0">
                        No results found.
                    </li>
                </ul>
            </div>
        </div>
        <button @click="deselectAllItems" class="btn btn--outline-primary btn--sm combobox__reset" x-show="selectedItems().length > 0">
            Deselect all colors
        </button>
    </div>
</div>
<script>
    function data() {
        return {
            items: [{
                    "id": "red",
                    "label": "Red",
                    "selected": false
                },
                {
                    "id": "orange",
                    "label": "Orange",
                    "selected": false
                },
                {
                    "id": "yellow",
                    "label": "Yellow",
                    "selected": false
                },
                {
                    "id": "green",
                    "label": "Green",
                    "selected": false
                },
                {
                    "id": "blue",
                    "label": "Blue",
                    "selected": false
                },
                {
                    "id": "purple",
                    "label": "Purple",
                    "selected": false
                },
                {
                    "id": "hot-pink",
                    "label": "Hot pink",
                    "selected": false
                },
                {
                    "id": "light-pink",
                    "label": "Light pink",
                    "selected": false
                },
                {
                    "id": "white",
                    "label": "White",
                    "selected": false
                },
                {
                    "id": "black",
                    "label": "Black",
                    "selected": false
                },
                {
                    "id": "brown",
                    "label": "Brown",
                    "selected": false
                }
            ],
            open: false,
            search: '',
            highlightedItemIndex: null,
            selectedItems() {
                return this.items.filter(item => item.selected);
            },
            unselectItemById(id) {
                this.items.find(item => item.id === id).selected = false;
            },
            filteredItems() {
                return this.items.filter(item => {
                    return item.label.toLowerCase().includes(this.search.toLowerCase());
                })
            },
            toggle() {
                if (this.open) {
                    return this.close();
                }

                this.$refs.input.focus();
                this.open = true;
                this.highlightedItemIndex = null;
            },
            close(focusAfter) {
                if (!this.open) return;

                this.open = false;
                this.search = '';
                this.highlightedItemIndex = null;

                focusAfter && focusAfter.focus();
            },
            highlightNextItem() {
                this.open = true;

                if (this.highlightedItemIndex === null) {
                    this.highlightedItemIndex = 0;
                    return;
                }

                this.highlightedItemIndex++;

                if (this.highlightedItemIndex >= this.filteredItems().length) {
                    this.highlightedItemIndex = 0;
                }

                this.$refs.listbox.children[this.highlightedItemIndex + 1].scrollIntoView({
                    behavior: 'smooth',
                    block: 'nearest'
                });
            },
            highlightPreviousItem() {
                this.open = true;
                this.highlightedItemIndex--;

                if (this.highlightedItemIndex < 0) {
                    this.highlightedItemIndex = this.filteredItems().length - 1;
                }

                this.$refs.listbox.children[this.highlightedItemIndex + 1].scrollIntoView({
                    behavior: 'smooth',
                    block: 'nearest'
                });
            },
            highlightedItemId() {
                const highlightedItem = this.filteredItems()[this.highlightedItemIndex];
                return highlightedItem ? this.$id('list-item', highlightedItem.id) : null;
            },
            toggleFromKeyboard() {
                if (this.highlightedItemIndex === null) {
                    return;
                }

                this.filteredItems()[this.highlightedItemIndex].selected = !this.filteredItems()[this.highlightedItemIndex].selected;
            },
            deselectAllItems() {
                this.items.forEach(item => item.selected = false);
            }
        }
    }
</script>
