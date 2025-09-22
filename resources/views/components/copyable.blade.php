{!! $text !!}
<button
    type="button"
    class="btn btn--light btn--sm btn--icon"
    aria-label="{{ __('Copy') }}"
    x-cloak
    x-bind:disabled="copied"
    x-data="{ copied: false }"
    x-on:click="(event) => {
        navigator.clipboard.writeText('{{ $value }}');
        copied = true;
        setTimeout(() => copied = false, 2500);
    }"
>
    <x-root::icon x-show="! copied" name="clipboard" class="btn__icon" />
    <x-root::icon x-show="copied" name="check" class="btn__icon" />
</button>
