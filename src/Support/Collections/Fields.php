<?php

namespace Cone\Root\Support\Collections;

use Cone\Root\Fields\Field;
use Cone\Root\Interfaces\Routable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;

class Fields extends Collection
{
    /**
     * Filter the field that are visible for the given request and action.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return static
     */
    public function filterVisible(Request $request): static
    {
        return $this->filter(static function (Field $field) use ($request): bool {
                        return $field->visible($request);
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
     * Register the field routes.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string|null  $uri
     * @return void
     */
    public function routes(Request $request, ?string $uri = null): void
    {
        Route::prefix('fields')->group(function () use ($request, $uri): void {
            $this->each(static function (Field $field) use ($request, $uri): void {
                if ($field instanceof Routable) {
                    $field->routes($request, "{$uri}/fields");
                }
            });
        });
    }
}
