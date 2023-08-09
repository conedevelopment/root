<div class="form-group--row">
    <span class="form-label">{{ $label }}</span>
    <div class="file-list">
        <button
            type="button"
            class="btn btn--primary btn--lg btn--block"
            x-on:click="$dispatch('open-{{ $modalKey }}')"
        >
            {{ __('Choose file(s)') }}
        </button>
        <ul class="file-list__items">
            @foreach($options as $attached)
                {!! $attached !!}
            @endforeach
        </ul>
    </div>
</div>

{{-- Modal --}}
@push('modals')
    @include('root::form.fields.media.manager', [
        'label' => $label,
        'modalKey' => $modalKey,
    ])
@endpush
