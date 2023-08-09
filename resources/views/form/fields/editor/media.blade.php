<div class="editor__group">
    <button
        type="button"
        class="btn btn--light btn--sm btn--icon"
        aria-label="{{ __('Media') }}"
        x-on:click="$dispatch('open-{{ $modalKey }}')"
    >
        <x-root::icon name="image" class="btn__icon" />
    </button>
</div>

{{-- Modal --}}
@push('modals')
    @include('root::form.fields.media.manager', [
        'label' => $label,
        'modalKey' => $modalKey,
    ])
@endpush
