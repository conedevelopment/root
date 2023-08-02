<div class="form-group--row">
    <span class="form-label">{{ $label }}</span>
    <div class="file-list">
        <button
            type="button"
            class="btn btn--primary btn--lg btn--block"
            x-on:click="$dispatch('open-{{ $modalKey }}')"
        >
            Choose file(s)
        </button>
        <ul class="file-list__items">
            @foreach($options as $attached)
                {!! $attached !!}
            @endforeach
        </ul>
    </div>
</div>

@push('modals')
    <x-root::modal :title="$label" :key="$modalKey">

    </x-root::modal>
@endpush
