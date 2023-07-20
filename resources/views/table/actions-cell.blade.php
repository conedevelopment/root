<td>
    <div class="data-table__actions">
        @can('view', $model)
            <a href="{{ $url }}" class="btn btn--light btn--sm btn--icon" aria-label="View">
                <x-root::icon name="eye" class="btn__icon" />
            </a>
        @endcan
        @can('update', $model)
            <a href="{{ $url }}" class="btn btn--light btn--sm btn--icon" aria-label="Edit">
                <x-root::icon name="edit" class="btn__icon" />
            </a>
        @endcan
        @can('delete', $model)
            <form action="{{ $url }}" method="POST" onsubmit="return window.confirm('{{ __('Are you sure?') }}');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn--light btn--sm btn--icon" aria-label="Delete">
                    <x-root::icon name="trash" class="btn__icon" />
                </button>
            </form>
        @endcan
    </div>
</td>
