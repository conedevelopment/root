<div
    class="form-group--row"
    x-data="{ selection: {{ json_encode($options) }} }"
    x-on:{{ $config['event'] }}.window="selection = $event.detail"
>
    <span class="form-label">{{ $label }}</span>
    <div class="file-list">
        <button
            type="button"
            class="btn btn--primary btn--lg btn--block"
            x-on:click="$dispatch('open-{{ $modalKey }}')"
        >
            {{ __('Choose file(s)') }}
        </button>
        <ul class="file-list__items" x-show="selection.length > 0" x-cloak>
            <template x-for="item in selection" :key="item.uuid">
                @include('root::form.fields.file-option')
            </template>
        </ul>
    </div>
</div>

{{-- Modal --}}
@push('modals')
    @include('root::media.manager', [
        'label' => $label,
        'modalKey' => $modalKey,
        'config' => $config,
    ])
@endpush
