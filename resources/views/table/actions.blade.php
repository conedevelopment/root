<div x-cloak x-show="selection.length > 0" class="alert alert--info data-table-alert">
    <span>
        <template x-if="selectedAllMatchingQuery">
            <span>{{ $data->total() }}</span>
        </template>
        <template x-if="! selectedAllMatchingQuery">
            <span x-text="selection.length"></span>
        </template>
        {{ __('items selected.') }}
    </span>
    <div class="data-table-alert__actions">
        <div class="data-table-alert__column">
            <button
                type="button"
                class="btn btn--primary btn--sm"
                x-on:click="selection = {{ $data->pluck('id')->toJson() }}; selectedAllMatchingQuery = true"
            >
                {{ __('Select all') }} ({{ $data->total() }})
            </button>
            <button
                type="button"
                class="btn btn--primary btn--sm"
                x-on:click="selection = []; selectedAllMatchingQuery = false"
            >
                {{ __('Clear') }}
            </button>
        </div>
        <div class="data-table-alert__column">
            @include('root::actions.actions', ['class' => 'btn--sm'])
        </div>
    </div>
</div>
