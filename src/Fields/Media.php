<?php

namespace Cone\Root\Fields;

use Cone\Root\Http\Controllers\MediaController;
use Cone\Root\Root;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
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
     * Regsiter the routes for the async component.
     *
     * @param  string  $path
     * @return void
     */
    protected function routes(string $path): void
    {
        Root::routes(static function () use ($path): void {
            Route::get($path, [MediaController::class, 'index'])->withReference($path);
            Route::post($path, [MediaController::class, 'store'])->withReference($path);
            Route::delete($path, [MediaController::class, 'destroy'])->withReference($path);
        });
    }
}
