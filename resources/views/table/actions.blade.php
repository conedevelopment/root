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
        <form class="data-table-alert__column" autocomplete="off">
            <div class="form-group--stacked">
                <select x-ref="actions" class="form-control form-control--sm" aria-label="{{ __('Actions') }}">
                    <option value="" disabled selected>{{ __('Select Action') }}</option>
                    @foreach($actions as $action)
                        <option value="{{ $action['modalKey'] }}">{{ $action['name'] }}</option>
                    @endforeach
                </select>
                <button
                    type="button"
                    class="btn btn--primary btn--sm"
                    x-on:click="$dispatch('open-'+$refs.actions.value)"
                >
                    {{ __('Run') }}
                </button>
            </div>
            @foreach($actions as $action)
                @include($action['template'], $action)
            @endforeach
        </form>
    </div>
</div>
