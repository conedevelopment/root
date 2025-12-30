<div class="file-list-item">
    <div class="file-list-item__column">
        <span id="{{ $attrs->get('id') }}" class="file-list-item__name">{!! $label !!}</span>
        <input type="hidden" name="{{ $attrs->get('name') }}" value="{{ $attrs->get('value') }}">
    </div>
    @unless($attrs->get('readonly') || $attrs->get('disabled'))
        <div class="file-list-item__actions">
            <button
                type="button"
                class="btn btn--delete btn--sm btn--icon"
                aria-label="{{ __('Remove') }}"
                aria-describedby="{{ $attrs->get('id') }}"
                x-on:click="selection.splice(index, 1)"
            >
                <x-root::icon name="close" class="btn__icon" />
            </button>
        </div>
    @endunless
</div>
