<th scope="col">
    @if($sortable)
        @if(Request::input($sortKey.'.by') !== $attribute || Request::input($sortKey.'.order', 'asc') === 'asc')
            <a
                href="{{ Request::fullUrlWithQuery([$sortKey => ['order' => 'desc', 'by' => $attribute]]) }}"
                class="sort-btn"
                aria-label="{{ __('Sort descending') }}"
                style="text-decoration: none;"
            >
                {{ $label }}
                @if(Request::input($sortKey.'.by') !== $attribute)
                    <x-root::icon name="chevron-up-down" class="sort-btn__icon" />
                @else
                    <x-root::icon name="chevron-up" class="sort-btn__icon" />
                @endif
            </a>
        @else
            <a
                href="{{ Request::fullUrlWithQuery([$sortKey => ['order' => 'asc', 'by' => $attribute]]) }}"
                class="sort-btn"
                aria-label="{{ __('Sort ascending') }}"
                style="text-decoration: none;"
            >
                {{ $label }}
                <x-root::icon name="chevron-down" class="sort-btn__icon" />
            </a>
        @endif
    @else
        {{ $label }}
    @endif
</th>
