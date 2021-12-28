<?php

namespace Cone\Root\Fields;

use Cone\Root\Http\Controllers\MediaController;
use Cone\Root\Resources\Resource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
     * Regsiter the routes for the async component.
     *
     * @param  \Cone\Root\Resources\Resource  $resource
     * @param  string  $uri
     * @return void
     */
    protected function routes(Resource $resource, string $uri): void
    {
        $resource->routes(function () use ($uri): void {
            Route::get($uri, [MediaController::class, 'index'])->resolves($this->resolvedAs);
            Route::post($uri, [MediaController::class, 'store'])->resolves($this->resolvedAs);
            Route::delete($uri, [MediaController::class, 'destroy'])->resolves($this->resolvedAs);
        });
    }
}
