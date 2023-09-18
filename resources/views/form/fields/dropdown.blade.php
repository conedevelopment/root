<div class="form-group--row" x-data="dropdown({{ json_encode($options) }}, {{ json_encode($config) }})">
    <label class="form-label" for="{{ $attrs->get('id') }}">{{ $label }}</label>
    <div
        class="combobox"
        {{-- x-on:focusout="open = false" --}}
        x-on:keydown.escape.prevent.stop="open = false"
    >
        <div class="combobox__selected-items" x-show="selection.length > 0">
            <template x-for="(item, index) in selection">
                <span class="combobox-item">
                    <span x-html="item.label"></span>
                    <button
                        type="button"
                        class="btn btn--primary btn--sm btn--icon"
                        aria-label="Remove"
                    >
                        <x-root::icon name="close" class="btn__icon" />
                    </button>
                </span>
            </template>
        </div>
        <div class="combobox__inner" x-on:click.away="open = false" x-ref="panel">
            <input
                {{ $attrs }}
                aria-autocomplete="list"
                aria-controls="{{ $attrs->get('id') }}-dropdown"
                role="combobox"
                x-model="search"
                {{-- x-bind:aria-activedescendant="" --}}
                x-bind:aria-expanded="open"
                x-on:focus="open = true"
                x-on:keyup.down.prevent="highlightNext"
                x-on:keyup.enter.prevent="toggleFromKeyboard"
                x-on:keyup.up.prevent="highlightPrev"
                x-on:input="open = true"
                x-ref="input"
            >
            <div class="combobox__dropdown" x-show="open" x-cloak>
                <ul
                    id="{{ $attrs->get('id') }}-dropdown"
                    aria-label="{{ $label }}"
                    aria-multiselectable="{{ $attrs->get('multiple') ? 'true' : 'false' }}"
                    role="listbox"
                    tabindex="-1"
                    x-ref="listbox"
                >
                    <template x-for="(option, index) in options">
                        <li
                            role="option"
                            x-bind:aria-selected="selected(option)"
                            x-bind:aria-selected="selected(option)"
                            x-bind:class="{ 'highlighted': highlighted === index }"
                            x-on:click="toggle(option)"
                        >
                            <span x-html="option.html"></span>
                            <template x-if="selected(option)">
                                <x-root::icon name="check" />
                            </template>
                        </li>
                    </template>
                    <li class="combobox__no-results" x-show="options.length === 0">
                        {{ __('No results found.') }}
                    </li>
                </ul>
            </div>
        </div>
        <button
            x-cloak
            type="button"
            class="btn btn--outline-primary btn--sm combobox__reset"
            x-show="selection.length > 0"
            x-on:click="selection = []"
        >
            {{ __('Clear selection') }}
        </button>
    </div>
</div>

{{-- Script --}}
@pushOnce('scripts')
    {{
        Vite::withEntryPoints('resources/js/dropdown.js')
            ->useBuildDirectory('vendor/root/build')
            ->useHotFile(public_path('vendor/root/hot'))
    }}
@endpushOnce
