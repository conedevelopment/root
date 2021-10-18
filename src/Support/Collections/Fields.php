<?php

namespace Cone\Root\Support\Collections;

use Cone\Root\Fields\Field;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class Fields extends Collection
{
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
        })->toBase();
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
        })->toBase();
    }
}
