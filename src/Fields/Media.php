<?php

namespace Cone\Root\Fields;

use Closure;
use Cone\Root\Http\Controllers\MediaController;
use Cone\Root\Http\Requests\ResourceRequest;
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
     * Set the storage resolver callback.
     *
     * @param  \Closure  $callback
     * @return $this
     */
    public function storeUsing(Closure $callback): static
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
     * {@inheritdoc}
     */
    public function resolveOptions(Request $request, Model $model): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function mapOption(Request $request, Model $model, Model $related): array
    {
        return array_merge(
            parent::mapOption($request, $model, $related),
            $related->toArray()
        );
    }

    /**
     * Map the items.
     *
     * @param \Cone\Root\Http\Requests\ResourceRequest  $request
     * @return array
     */
    public function mapItems(ResourceRequest $request): array
    {
        $model = $request->resource()->getModelInstance();

        return $this->resolveQuery($request, $model)
                    ->filter($request)
                    ->latest()
                    ->cursorPaginate($request->input('per_page'))
                    ->withQueryString()
                    ->through(function (Model $related) use ($request, $model): array {
                        return $this->mapOption($request, $model, $related);
                    })
                    ->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function routes(Router $router): void
    {
        $router->get($this->getKey(), [MediaController::class, 'index']);
        $router->post($this->getKey(), [MediaController::class, 'store']);
        $router->delete($this->getKey(), [MediaController::class, 'destroy']);
    }
}
