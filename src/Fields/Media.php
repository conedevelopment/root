<?php

namespace Cone\Root\Fields;

use Cone\Root\Http\Controllers\MediaController;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;

class Media extends MorphToMany
{
    /**
     * Indicates if the options should be lazily populated.
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
     * Register the routes.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string|null  $uri
     * @return void
     */
    public function routes(Request $request, ?string $uri = null): void
    {
        $this->uri = "{$uri}/{$this->name}";

        if (! App::routesAreCached()) {
            Route::apiResource($this->name, MediaController::class)->only(['index', 'store', 'destroy']);
        }
    }
}
