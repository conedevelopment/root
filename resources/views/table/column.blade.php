<th scope="col">
    @if($sortable)
        <div class="data-table-sort">
            {{ $label }}
            <button class="data-table-sort__control" type="button" aria-label="Sort ascending">
                <x-root::icon name="chevron-up-down" class="data-table-sort__icon" />
            </button>
        </div>
    @else
        {{ $label }}
    @endif
</th>
