<div x-cloak x-show="selection.length > 0" class="alert alert--info data-table-alert">
    <span><span x-text="selection.length"></span> {{ __('items selected.') }}</span>
    <div class="data-table-alert__actions">
        <div class="data-table-alert__column">
            <button
                type="button"
                class="btn btn--primary btn--sm"
                x-on:click="selection = {{ $items->pluck('id')->toJson() }}"
            >
                {{ __('Select all') }} ({{ $items->total() }})
            </button>
            <button
                type="button"
                class="btn btn--primary btn--sm"
                x-on:click="selection = []; all = false"
            >
                {{ __('Clear') }}
            </button>
        </div>
        <div class="data-table-alert__column">
            <select
                class="form-control form-control--sm"
                aria-label="{{ __('Actions') }}"
                x-on:change="$dispatch('open-'+$event.target.value)"
            >
                <option value="">--- {{ __('Select Action') }} ---</option>
                @foreach($actions as $action)
                    <option value="{{ $action->getModalKey() }}">{{ $action->getName() }}</option>
                @endforeach
            </select>
            <button type="button" class="btn btn--primary btn--sm">
                {{ __('Run') }}
            </button>
            @foreach($actions as $action)
                {!! $action !!}
            @endforeach
        </div>
    </div>
</div>
