<div class="file-list-item__column">
    @if($medium->isImage)
        <img
            class="file-list-item__thumbnail"
            src="{{ $medium->getUrl('thumbnail') }}"
            alt="{{ $label }}"
        >
    @else
        <span class="file-list-item__icon">
            <x-root::icon name="document" class="media-item__icon" />
        </span>
    @endif
    <span id="{{ $medium->uuid }}" class="file-list-item__name">{{ $label }}</span>
    <input type="hidden" name="{{ $attrs->get('name') }}" value="{{ $attrs->get('value') }}">
</div>
<div class="file-list-item__actions">
    @if(! empty($fields))
        <button
            type="button"
            class="btn btn--light btn--sm btn--icon"
            aria-label="{{ __('Edit') }}"
            aria-describedby="{{ $medium->uuid }}"
            x-on:click="$dispatch('open-{{ $medium->uuid }}')"
        >
            <x-root::icon name="edit" class="btn__icon" />
        </a>
    @endif
    <button
        type="button"
        class="btn btn--delete btn--sm btn--icon"
        aria-label="{{ __('Remove') }}"
        aria-describedby="{{ $medium->uuid }}"
        x-on:click="selection.splice(index, 1)"
    >
        <x-root::icon name="close" class="btn__icon" />
    </button>
</div>

@if(! empty($fields))
    <x-root::modal :title="$label" :key="$medium->uuid">
        @foreach($fields as $field)
            {!! $field !!}
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
