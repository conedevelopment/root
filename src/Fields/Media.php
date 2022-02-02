<?php

namespace Cone\Root\Fields;

use Closure;
use Cone\Root\Http\Controllers\MediaController;
use Cone\Root\Jobs\MoveFile;
use Cone\Root\Jobs\PerformConversions;
use Cone\Root\Models\Medium;
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
     * The storing resolver callback.
     *
     * @var \Closure|null
     */
    protected ?Closure $storingResolver = null;

    /**
     * Set the storing resolver callback.
     *
     * @param  \Closure  $callback
     * @return $this
     */
    public function storing(Closure $callback): static
    {
        $this->storingResolver = $callback;

        return $this;
    }

    /**
     * Store the file using the given path and request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $path
     * @return \Cone\Root\Models\Medium
     */
    public function store(Request $request, string $path): Medium
    {
        $medium = tap((Medium::proxy())::makeFrom($path), function (Medium $medium) use ($request): void {
            if (! is_null($this->storingResolver)) {
                call_user_func_array($this->storingResolver, [$request, $medium]);
            }
        });

        $request->user()->uploads()->save($medium);

        return $medium;
    }

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
