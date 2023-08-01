<div
    class="modal-backdrop"
    x-cloak
    x-data="{ open: false }"
    x-show="open"
    x-on:keydown.escape="open = false"
    x-on:open-{{ $key }}.window="open = true"
>
    <div role="dialog" aria-modal="true" tabindex="0" class="modal" x-on:click.away="open = false" x-trap.noscroll="open">
        <div class="modal__header">
            <div class="modal__header-caption">
                <h2 class="modal__title">{{ $title }}</h2>
                @if($subtitle)
                    <p class="modal__subtitle">{{ $subtitle }}</p>
                @endif
            </div>
            <button
                type="button"
                class="btn btn--icon btn--light"
                aria-label="{{ __('Close modal') }}"
                x-on:click="open = false"
            >
                <x-root::icon name="close" class="btn__icon" />
            </button>
        </div>
        <div class="modal__body">
            {{ $slot }}
        </div>
        @if(isset($footer))
            <div class="modal__footer">
                {{ $footer }}
            </div>
        @endif
    </div>
</div>
