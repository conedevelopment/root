<li class="file-list-item" x-data>
    <div class="file-list-item__column">
        <img class="file-list-item__thumbnail" src="https://picsum.photos/80/80" alt="">
        <span id="{{ $value->uuid }}" class="file-list-item__name">{{ $label }}</span>
        <input type="hidden" name="{{ $attrs->get('name') }}" value="{{ $value->getKey() }}">
    </div>
    <div class="file-list-item__actions">
        <button
            type="button"
            class="btn btn--delete btn--sm btn--icon"
            aria-describedby="{{ $value->uuid }}"
            aria-label="{{ __('Remove') }}"
            x-on:click="$root.remove()"
        >
            <x-root::icon name="close" class="btn__icon" />
        </button>
    </div>
</li>
