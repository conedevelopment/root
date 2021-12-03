<?php

namespace Cone\Root\Support\Collections;

use Cone\Root\Fields\Field;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class Fields extends Collection
{
    /**
     * Filter the fields that are available for the given request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return static
     */
    public function available(Request $request): static
    {
        return $this->filter(static function (Field $field) use ($request): bool {
                        return $field->authorized($request) && $field->visible($request);
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
     * @return \Illuminate\Support\Collection
     */
    public function mapToValidate(Request $request, Model $model): Collection
    {
        return $this->mapWithKeys(static function (Field $field) use ($request, $model): array {
                    return [$field->name => $field->toValidate($request, $model)];
                })
                ->filter()
                ->toBase();
    }

    /**
     * Call the resolved callbacks on the fields.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function resolved(Request $request): void
    {
        $this->each(static function (Field $field) use ($request): void {
            $field->resolved($request);
        });
    }
}
