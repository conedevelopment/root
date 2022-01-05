<?php

namespace Cone\Root\Support\Collections;

use Cone\Root\Fields\Field;
use Cone\Root\Resources\Resource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
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
     * Register the field routes.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    public function registerRoutes(Request $request, Router $router): void
    {
        $router->prefix('fields')->group(function (Router $router) use ($request): void {
            $this->each(static function (Field $field) use ($request, $router): void {
                if (method_exists($field, 'registerRoutes')) {
                    $field->registerRoutes($request, $router);
                }
            });
        });
    }
}
