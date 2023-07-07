<tr>
    <td>
        <label class="form-check" aria-label="Select LuminoTech LED Desk Lamp">
            <input class="form-check__control" type="checkbox" value="1" name="1">
        </label>
    </td>
    @foreach($columns as $column)
        <x-dynamic-component :component="$column->getComponent()" :model="$model" :column="$column" />
    @endforeach
    <td>
        <div class="data-table__actions">
            @can('view', $model)
                <a href="#" class="btn btn--light btn--sm btn--icon" aria-label="View">
                    <x-root::icon name="eye" class="btn__icon" />
                </a>
            @endcan
            @can('update', $model)
                <a href="#" class="btn btn--light btn--sm btn--icon" aria-label="Edit">
                    <x-root::icon name="edit" class="btn__icon" />
                </a>
            @endcan
            @can('delete', $model)
                <form action="#" method="POST">
                    @csrf
                    @method('DELETE')
                    <a href="#" class="btn btn--light btn--sm btn--icon" aria-label="Delete">
                        <x-root::icon name="trash" class="btn__icon" />
                    </a>
                </form>
            @endcan
        </div>
    </td>
</tr>
