<div class="app-card">
    <div class="app-card__header">
        <div class="app-card__actions">
            <form class="search-form">
                <input class="form-control  search-form__control" type="text" placeholder="Search..." title="Search" />
                <button type="submit" class="search-form__submit">
                    <span class="sr-only">Search</span>
                    <x-root::icon name="search" class="search-form__icon" />
                </button>
            </form>
        </div>
    </div>
    <div class="app-card__body">
        <div class="data-table">
            <div class="table-responsive">
                <table class="table table--hover">
                    <thead>
                        <tr>
                            @if($actions->isNotEmpty())
                                <th style="width: 3.25rem;">
                                    <span class="sr-only">{{ __('Select') }}</span>
                                    <label class="form-check" aria-label="{{ __('Select all items') }}">
                                        <input class="form-check__control" type="checkbox">
                                    </label>
                                </th>
                            @endif
                            @foreach($columns as $column)
                                <th scope="col">
                                    @if($column->isSortable())
                                        <div class="data-table-sort">
                                            {{ $column->label }}
                                            <button class="data-table-sort__control" type="button" aria-label="Sort ascending">
                                                <x-root::icon name="chevron-up-down" class="data-table-sort__icon" />
                                            </button>
                                        </div>
                                    @else
                                        {{ $column->label }}
                                    @endif
                                </th>
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
                                    {!! $cell->render() !!}
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
