<div
    class="form-group--row"
    x-data="{ selection: {{ json_encode($selection) }} }"
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
            <template x-for="(item, index) in selection" :key="item.uuid">
                <li class="file-list-item" x-html="item.html"></li>
            </template>
        </ul>
    </div>

    @include('root::media.manager', [
        'label' => $label,
        'modalKey' => $modalKey,
        'config' => $config,
    ])
</div>
