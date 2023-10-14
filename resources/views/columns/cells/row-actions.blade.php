<td>
    <div class="data-table__actions">
        @can('update', $model)
            <a href="{{ $value }}" class="btn btn--light btn--sm btn--icon" aria-label="{{ __('Edit') }}">
                <x-root::icon name="edit" class="btn__icon" />
            </a>
        @endcan
        @can('delete', $model)
            <form action="{{ $value }}" method="POST" onsubmit="return window.confirm('{{ __('Are you sure?') }}');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn--delete btn--sm btn--icon" aria-label="{{ __('Delete') }}">
                    <x-root::icon name="trash" class="btn__icon" />
                </button>
            </form>
        @endcan
    </div>
</td>
