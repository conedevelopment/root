<form method="GET" action="{{ URL::full() }}" id="{{ $key }}" class="app-card__actions">
    @if(! empty($filters))
        <div class="data-table-filter" x-data="{ open: false }" x-on:click.outside="open = false">
            <button
                type="button"
                class="btn btn--light btn--icon btn--counter data-table-filter__toggle"
                x-bind:aria-expanded="open"
                x-on:click="open = ! open"
            >
                <x-root::icon name="filter" class="btn__icon" />
                @if($activeFilters > 0)
                    <span class="btn__counter">{{ $activeFilters }}</span>
                @endif
            </button>
            <div class="context-menu context-menu--inline-end" x-bind:class="{ 'is-open': open }">
                <div class="form-group-stack form-group-stack--bordered form-group-container">
                    @foreach($filters as $filter)
                        {!! $filter !!}
                    @endforeach
                    <div class="data-table-filter__actions">
                        <button type="submit" class="btn btn--primary btn--sm">
                            {{ __('Filter') }}
                        </button>
                        <a href="{{ $url }}" class="btn btn--light btn--sm">
                            {{ __('Reset') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
</form>
