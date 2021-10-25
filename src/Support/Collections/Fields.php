<?php

namespace Cone\Root\Support\Collections;

use Cone\Root\Fields\Field;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class Fields extends Collection
{
    /**
     * Filter the field that are visible for the given request and action.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $action
     * @return static
     */
    public function filterVisibleFor(Request $request, string $action): self
    {
        return $this->filter(static function (Field $field) use ($request, $action): bool {
                        return $field->visible($request, $action);
                    })
                    ->values();
    }

    /**
     * Map the fields to display.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return \Illuminate\Support\Collection
     */
    public function mapToDisplay(Request $request, Model $model): Collection
    {
        return $this->map(static function (Field $field) use ($request, $model): array {
                        return $field->toDisplay($request, $model);
                    })
                    ->toBase();
    }

    /**
     * Map the fields to form.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return \Illuminate\Support\Collection
     */
    public function mapToForm(Request $request, Model $model): Collection
    {
        return $this->map(static function (Field $field) use ($request, $model): array {
                        return $field->toInput($request, $model);
                    })
                    ->toBase();
    }

    /**
     * Map the fields to validate.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $action
     * @return \Illuminate\Support\Collection
     */
    public function mapToValidate(Request $request, Model $model, string $action = '*'): Collection
    {
        return $this->mapWithKeys(static function (Field $field) use ($request, $model, $action): array {
                    return [$field->name => $field->toValidate($request, $model, $action)];
                })
                ->filter()
                ->toBase();
    }
}
