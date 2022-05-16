<?php

namespace Cone\Root\Fields;

use Closure;
use Cone\Root\Http\Controllers\MediaController;
use Cone\Root\Http\Requests\ResourceRequest;
use Cone\Root\Http\Requests\RootRequest;
use Cone\Root\Models\Medium;
use Illuminate\Database\Eloquent\Model;
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
     * The Vue component.
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
    public function storeUsing(Closure $callback): static
    {
        $this->storingResolver = $callback;

        return $this;
    }

    /**
     * Store the file using the given path and request.
     *
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @param  string  $path
     * @return \Cone\Root\Models\Medium
     */
    public function store(RootRequest $request, string $path): Medium
    {
        $medium = (Medium::proxy())::makeFrom($path);

        if (! is_null($this->storingResolver)) {
            call_user_func_array($this->storingResolver, [$request, $medium]);
        }

        $request->user()->uploads()->save($medium);

        return $medium;
    }

    /**
     * {@inheritdoc}
     */
    public function resolveOptions(RootRequest $request, Model $model): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function mapOption(RootRequest $request, Model $model, Model $related): array
    {
        return array_merge(
            parent::mapOption($request, $model, $related),
            $related->toArray(),
            ['created_at' => $related->created_at->format('Y-m-d H:i')],
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

    /**
     * {@inheritdoc}
     */
    public function toInput(RootRequest $request, Model $model): array
    {
        return array_merge(parent::toInput($request, $model), [
            'selection' => $this->getDefaultValue($request, $model)
                                ->map(function (Model $related) use ($request, $model): array {
                                    return $this->mapOption($request, $model, $related);
                                }),
        ]);
    }
}
