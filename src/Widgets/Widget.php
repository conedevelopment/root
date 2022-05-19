<?php

namespace Cone\Root\Widgets;

use Closure;
use Cone\Root\Http\Controllers\WidgetController;
use Cone\Root\Http\Requests\RootRequest;
use Cone\Root\Traits\Authorizable;
use Cone\Root\Traits\RegistersRoutes;
use Cone\Root\Traits\ResolvesVisibility;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

abstract class Widget implements Arrayable, Renderable
{
    use Authorizable;
    use RegistersRoutes;
    use ResolvesVisibility;

    /**
     * Indicates if the component is async.
     *
     * @var bool
     */
    protected bool $async = false;

    /**
     * The Vue component.
     *
     * @var string
     */
    protected string $component = 'Widget';

    /**
     * The Blade template.
     *
     * @var string
     */
    protected string $template;

    /**
     * The data resolver callback.
     *
     * @var \Closure|null
     */
    protected ?Closure $dataResolver = null;

    /**
     * Make a new widget instance.
     *
     * @param  array  ...$parameters
     * @return static
     */
    public static function make(...$parameters): static
    {
        return new static(...$parameters);
    }

    /**
     * Get the key.
     *
     * @return string
     */
    public function getKey(): string
    {
        return Str::of(static::class)->classBasename()->kebab()->toString();
    }

    /**
     * Get the name.
     *
     * @return string
     */
    public function getName(): string
    {
        return __(Str::of(static::class)->classBasename()->headline()->toString());
    }

    /**
     * Get the Vue component.
     *
     * @return string
     */
    public function getComponent(): string
    {
        return $this->component;
    }

    /**
     * Get the Blade template.
     *
     * @return string
     */
    public function getTemplate(): string
    {
        return $this->template;
    }

    /**
     * Set the async attribute.
     *
     * @param  bool  $value
     * @return $this
     */
    public function async(bool $value = true): static
    {
        $this->async = $value;

        return $this;
    }

    /**
     * Get the data.
     *
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @return array
     */
    public function data(RootRequest $request): array
    {
        return [];
    }

    /**
     * Set the data resolver.
     *
     * @param  array|\Closure  $data
     * @return static
     */
    public function with(array|Closure $data): static
    {
        if (is_array($data)) {
            $data = static function () use ($data): array {
                return $data;
            };
        }

        $this->dataResolver = $data;

        return $this;
    }

    /**
     * Get the evaluated contents of the object.
     *
     * @return string
     */
    public function render(): string
    {
        return View::make($this->getTemplate(), array_merge(
            App::call([$this, 'data']),
            is_null($this->dataResolver) ? [] : App::call($this->dataResolver)
        ))->render();
    }

    /**
     * The routes that should be registerd.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    public function routes(Router $router): void
    {
        if ($this->async) {
            $router->get('/', WidgetController::class);
        }
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'async' => $this->async,
            'component' => $this->getComponent(),
            'key' => $this->getKey(),
            'name' => $this->getName(),
            'template' => $this->async ? null : $this->render(),
            'url' => $this->async ? URL::to($this->getUri()) : null,
        ];
    }
}
