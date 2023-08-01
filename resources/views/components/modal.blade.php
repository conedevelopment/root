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
                <p class="modal__subtitle">Set notifications for this post</p>
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
        <div class="modal__footer">
            <button type="button" class="btn btn--outline-primary" x-on:click="open = false">Cancel</button>
            <button type="button" class="btn btn--primary">Save</button>
        </div>
    </div>
</div>
