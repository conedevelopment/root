<div class="editor__group" x-data="{ selection: [] }">
    <button
        type="button"
        class="btn btn--light btn--sm btn--icon"
        aria-label="{{ __('Media') }}"
        x-on:click="$dispatch('open-{{ $modalKey }}')"
    >
        <x-root::icon name="image" class="btn__icon" />
    </button>

    @include('root::media.manager', [
        'label' => $label,
        'modalKey' => $modalKey,
    ])
</div>
