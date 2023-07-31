<div class="app-card" x-data="{ selection: [] }">
    <div class="app-card__header">
        <h2 class="app-card__title">Items</h2>
        <div class="app-card__actions">
            @if($searchable)
                <form class="search-form">
                    <input class="form-control  search-form__control" type="text" placeholder="Search..." title="Search">
                    <button type="submit" class="search-form__submit">
                        <span class="sr-only">Search</span>
                        <x-root::icon name="search" class="search-form__icon" />
                    </button>
                </form>
            @endif
            @if(! empty($filters))
                <div class="data-table-filter" x-data="{ open: false }" x-on:click.outside="open = false">
                    <button
                        type="button"
                        class="btn btn--light btn--icon data-table-filter__toggle"
                        x-bind:aria-expanded="open"
                        x-on:click="open = ! open"
                    >
                        <x-root::icon name="filter" class="btn__icon" />
                    </button>
                    <div class="context-menu context-menu--inline-end" x-bind:data-state="open ? 'open' : 'closed'">
                        <div class="form-group-stack form-group-stack--bordered form-group-container">
                            {{-- Filters --}}
                            <div class="data-table-filter__actions">
                                <button class="btn btn--primary btn--sm">Fitler</button>
                                <button class="btn btn--light btn--sm">Reset</button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
    <div class="app-card__body">
        <div class="data-table">
            <div x-cloak x-show="selection.length > 0" class="alert alert--info data-table-alert">
                <span><span x-text="selection.length"></span> items selected.</span>
                <div class="data-table-alert__actions">
                    <button class="btn btn--primary btn--sm">Select all ({{ $items->total() }})</button>
                    <button
                        type="button"
                        class="btn btn--primary btn--sm"
                        x-on:click="selection = []"
                    >
                        {{ __('Clear') }}
                    </button>
                    @if(! empty($actions))
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
                        @foreach($actions as $action)
                            {!! $action !!}
                        @endforeach
                    @endif
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table--hover">
                    <thead>
                        <tr>
                            @if(! empty($actions))
                                <th style="width: 3.25rem;">
                                    <span class="sr-only">{{ __('Select') }}</span>
                                    <label class="form-check" aria-label="{{ __('Select all items') }}">
                                        <input class="form-check__control" type="checkbox">
                                    </label>
                                </th>
                            @endif
                            @foreach($columns as $column)
                                {!! $column !!}
                            @endforeach
                            <th scope="col">
                                <span class="sr-only">{{ __('Actions') }}</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $cells)
                            <tr>
                                @foreach($cells as $cell)
                                    {!! $cell !!}
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="data-table__footer">
                <div class="data-table__footer-column">
                    <div class="form-group">
                        <label class="sr-only" for="per_page">{{ __('Number of results') }} </label>
                        <select class="form-control form-control--sm" id="per_page">
                            <option value="10">10</option>
                            <option value="20" selected>20</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                    <p>{{ __('Showing :from to :to of :total results', ['from' => $items->firstItem(), 'to' => $items->lastItem(), 'total' => $items->total()]) }}</p>
                </div>

                {!! $items->links('root::table.pagination') !!}
            </div>
        </div>
    </div>
</div>
