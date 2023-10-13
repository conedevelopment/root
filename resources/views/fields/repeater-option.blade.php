<div class="repeater" x-data="{ open: {{ json_encode($open) }} }">
    <div class="repeater__heading">
        <div class="repeater__column">
            <button
                type="button"
                class="btn btn--primary btn--sm btn--icon repeater__toggle"
                aria-label="{{ __('Toggle') }}"
                aria-describedby="{{ $value }}-title"
                x-bind:aria-expanded="open"
                x-on:click="open = ! open"
            >
                <template x-if="open">
                    <x-root::icon name="minus" class="btn__icon"/>
                </template>
                <template x-if="! open">
                    <x-root::icon name="plus" class="btn__icon"/>
                </template>
            </button>
            <h3 id="{{ $value }}-title" class="repeater__title">
                <span class="repeater__order">#<span x-text="index + 1"></span></span>
                {{ $label }}
            </h3>
        </div>
        <div class="repeater__actions">
            <button
                type="button"
                class="btn btn--light btn--sm btn--icon"
                aria-describedby="{{ $value }}-title"
                aria-label="{{ __('Move up') }}"
                x-on:click="swap(index, index - 1)"
                x-bind:disabled="index === 0"
            >
                <x-root::icon name="chevron-up" class="btn__icon" />
            </button>
            <button
                type="button"
                class="btn btn--light btn--sm btn--icon"
                aria-describedby="{{ $value }}-title"
                aria-label="{{ __('Move down') }}"
                x-on:click="swap(index, index + 1)"
                x-bind:disabled="index + 1 === options.length"
            >
                <x-root::icon name="chevron-down" class="btn__icon" />
            </button>
            <button
                type="button"
                class="btn btn--delete btn--sm btn--icon"
                aria-describedby="{{ $value }}-title"
                aria-label="{{ __('Remove') }}"
                x-on:click="remove(index)"
            >
                <x-root::icon name="close" class="btn__icon" />
            </button>
        </div>
    </div>
    <div class="repeater__body" x-show="open">
        <div class="form-group-stack form-group-stack--bordered form-group-container">
            <div class="form-group--row">
                @foreach($fields as $field)
                    @include($field['template'], $field)
                @endforeach
            </div>
        </div>
    </div>
</div>
