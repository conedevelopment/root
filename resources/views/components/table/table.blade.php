<div class="app-card__body">
    <div class="data-table">
        <div class="table-responsive">
            <table class="table table--hover">
                <thead>
                    <tr>
                        <th style="width: 3.25rem;">
                            <span class="sr-only">Select</span>
                            <label class="form-check" aria-label="Select all item">
                                <input class="form-check__control" type="checkbox" value="1" name="select-all" />
                            </label>
                        </th>
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
                    @foreach($items as $model)
                        <x-root::table.row :model="$model" :columns="$columns" />
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="data-table__footer">
            <div class="data-table__footer-column">
                <div class="form-group">
                    <label class="sr-only" for="number-of-results">Number of results</label>
                    <select class="form-control form-control--sm" id="number-of-results">
                        <option value="10">10</option>
                        <option value="20" selected>20</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
                <p>Showing 1 to 20 of 100 results</p>
            </div>
            <x-root::table.pagination />
        </div>
    </div>
</div>
