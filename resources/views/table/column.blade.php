<th scope="col">
    @if($sortable)
        <div class="data-table-sort">
            {{ $label }}
            @if(Request::input('sort.by') !== $attribute || Request::input('sort.order', 'asc') === 'asc')
                <a
                    href="{{ Request::fullUrlWithQuery(['sort' => ['order' => 'desc', 'by' => $attribute]]) }}"
                    class="data-table-sort__control"
                    aria-label="{{ __('Sort descending') }}"
                >
                    @if(Request::input('sort.by') !== $attribute)
                        <x-root::icon name="chevron-up-down" class="data-table-sort__icon" />
                    @else
                        <x-root::icon name="chevron-up" class="data-table-sort__icon" />
                    @endif
                </a>
            @else
                <a
                    href="{{ Request::fullUrlWithQuery(['sort' => ['order' => 'asc', 'by' => $attribute]]) }}"
                    class="data-table-sort__control"
                    aria-label="{{ __('Sort ascending') }}"
                >
                    <x-root::icon name="chevron-down" class="data-table-sort__icon" />
                </a>
            @endif
        </div>
    @else
        {{ $label }}
    @endif
</th>
