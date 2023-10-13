<template x-teleport="#modals">
    <div
        class="modal-backdrop"
        x-cloak
        x-data="{ open: {{ json_encode($open) }} }"
        x-show="open"
        x-on:keydown.escape="open = false"
        x-on:open-{{ $key }}.window="open = true"
    >
        <div
            role="dialog"
            aria-modal="true"
            tabindex="0"
            x-on:click.away="open = false"
            x-trap.noscroll="open"
            {{ $attributes->class(['modal']) }}
        >
            <div class="modal__header">
                <div class="modal__header-caption">
                    <h2 class="modal__title">{{ $title }}</h2>
                    @if($subtitle)
                        <p class="modal__subtitle">{{ $subtitle }}</p>
                    @endif
                </div>
                @isset($header)
                    {{ $header }}
                @endisset
                <button
                    type="button"
                    class="btn btn--icon btn--light modal__close"
                    aria-label="{{ __('Close Modal') }}"
                    x-on:click="open = false"
                >
                    <x-root::icon name="close" class="btn__icon" />
                </button>
            </div>
            <div class="modal__body">
                {{ $slot }}
            </div>
            @isset($footer)
                <div {{ $footer->attributes->class(['modal__footer']) }}>
                    {{ $footer }}
                </div>
            @endisset
        </div>
    </div>
</template>
