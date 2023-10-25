<div class="file-list-item">
    <div class="file-list-item__column">
        @if($isImage)
            <img class="file-list-item__thumbnail" src="{{ $url }}" alt="{{ $label }}">
        @else
            <span class="file-list-item__icon">
                <x-root::icon name="document" class="media-item__icon" />
            </span>
        @endif
        <span id="{{ $uuid }}" class="file-list-item__name">{{ $label }}</span>
        <input type="hidden" name="{{ $attrs->get('name') }}" value="{{ $attrs->get('value') }}">
    </div>
    <div class="file-list-item__actions">
        @if(! empty($fields))
            <button
                type="button"
                class="btn btn--light btn--sm btn--icon"
                aria-label="{{ __('Edit') }}"
                aria-describedby="{{ $uuid }}"
                x-on:click="$dispatch('open-{{ $uuid }}')"
            >
                <x-root::icon name="edit" class="btn__icon" />
            </a>
        @endif
        <button
            type="button"
            class="btn btn--delete btn--sm btn--icon"
            aria-label="{{ __('Remove') }}"
            aria-describedby="{{ $uuid }}"
            x-on:click="selection.splice(index, 1)"
        >
            <x-root::icon name="close" class="btn__icon" />
        </button>
    </div>
</div>

@if(! empty($fields))
    <x-root::modal :title="$label" :key="$uuid">
        @foreach($fields as $field)
            @include($field['template'], $field)
        @endforeach

        <x-slot:footer>
            <div class="modal__column">
                <button type="button" class="btn btn--primary" x-on:click="open = false">
                    {{ __('Close') }}
                </button>
            </div>
        </x-slot:footer>
    </x-root::modal>
@endif
