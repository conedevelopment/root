<th scope="col">
    @if($sortable)
        <div class="data-table-sort">
            {{ $label }}
            <a href="{{ $sortUrl }}" class="data-table-sort__control" aria-label="Sort ascending">
                <x-root::icon name="chevron-up-down" class="data-table-sort__icon" />
            </a>
        </div>
    @else
        {{ $label }}
    @endif
</th>
