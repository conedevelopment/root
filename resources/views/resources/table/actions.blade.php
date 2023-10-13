<div x-cloak x-show="selection.length > 0" class="alert alert--info data-table-alert">
    <span><span x-text="selection.length"></span> {{ __('items selected.') }}</span>
    <div class="data-table-alert__actions">
        <div class="data-table-alert__column">
            <button
                type="button"
                class="btn btn--primary btn--sm"
                x-on:click="selection = {{ $data->pluck('id')->toJson() }}"
            >
                {{ __('Select all') }} ({{ $data->total() }})
            </button>
            <button
                type="button"
                class="btn btn--primary btn--sm"
                x-on:click="selection = []; all = false"
            >
                {{ __('Clear') }}
            </button>
        </div>
        <form class="data-table-alert__column" autocomplete="off">
            <select
                class="form-control form-control--sm"
                aria-label="{{ __('Actions') }}"
                x-on:change="$dispatch('open-'+$event.target.value)"
            >
                <option value="">--- {{ __('Select Action') }} ---</option>
                @foreach($actions as $action)
                    <option value="{{ $action['modalKey'] }}">{{ $action['name'] }}</option>
                @endforeach
            </select>
            @foreach($actions as $action)
                @include($action['template'], $action)
            @endforeach
        </form>
    </div>
</div>
