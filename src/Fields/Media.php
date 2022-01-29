<?php

namespace Cone\Root\Fields;

use Cone\Root\Http\Controllers\MediaController;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;

class Media extends MorphToMany
{
    /**
     * Indicates if the component is async.
     *
     * @var bool
     */
    protected bool $async = true;

    /**
     * The Vue compoent.
     *
     * @var string
     */
    protected string $component = 'Media';

    /**
     * Resolve the options for the field.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return array
     */
    public function resolveOptions(Request $request, Model $model): array
    {
        return [];
    }

    /**
     * Map the given option.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  \Illuminate\Database\Eloquent\Model  $related
     * @return array
     */
    public function mapOption(Request $request, Model $model, Model $related): array
    {
        return $related->toArray();
    }

    /**
     * The routes that should be registerd.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    public function routes(Router $router): void
    {
        $router->get($this->getKey(), [MediaController::class, 'index']);
        $router->post($this->getKey(), [MediaController::class, 'store']);
        $router->delete($this->getKey(), [MediaController::class, 'destroy']);
    }
}
