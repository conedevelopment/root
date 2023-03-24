<?php

namespace Cone\Root\Support\Collections;

use Cone\Root\Fields\Field;
use Cone\Root\Http\Requests\RootRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class Fields extends Collection
{
    /**
     * Register the given fields.
     */
    public function register(array|Field $fields): static
    {
        foreach (Arr::wrap($fields) as $field) {
            $this->push($field);
        }

        return $this;
    }

    /**
     * Filter the searchable fields.
     */
    public function searchable(RootRequest $request): static
    {
        return $this->filter->isSearchable($request);
    }

    /**
     * Filter the sortable fields.
     */
    public function sortable(RootRequest $request): static
    {
        return $this->filter->isSortable($request);
    }

    /**
     * Map the fields to display.
     */
    public function mapToDisplay(RootRequest $request, Model $model): Collection
    {
        return $this->map->toDisplay($request, $model)->toBase();
    }

    /**
     * Map the fields to form.
     */
    public function mapToForm(RootRequest $request, Model $model): Collection
    {
        return $this->map->toInput($request, $model)->toBase();
    }

    /**
     * Map the fields to validate.
     */
    public function mapToValidate(RootRequest $request, Model $model): array
    {
        return $this->reduce(static function (array $rules, Field $field) use ($request, $model): array {
            return array_merge_recursive($rules, $field->toValidate($request, $model));
        }, []);
    }
}
