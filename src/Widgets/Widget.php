<?php

namespace Cone\Root\Widgets;

use Closure;
use Cone\Root\Http\Controllers\WidgetController;
use Cone\Root\Traits\Makeable;
use Cone\Root\Traits\RegistersRoutes;
use Cone\Root\Traits\ResolvesVisibility;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

abstract class Widget implements Arrayable, Renderable
{
    use Makeable;
    use RegistersRoutes;
    use ResolvesVisibility;

    /**
     * Indicates if the component is async.
     */
    protected bool $async = false;

    /**
     * The Vue component.
     */
    protected string $component = 'Widget';

    /**
     * The Blade template.
     */
    protected string $template;

    /**
     * The data resolver callback.
     */
    protected ?Closure $dataResolver = null;

    /**
     * Get the key.
     */
    public function getKey(): string
    {
        return Str::of(static::class)->classBasename()->kebab()->value();
    }

    /**
     * Get the URI key.
     */
    public function getUriKey(): string
    {
        return $this->getKey();
    }

    /**
     * Get the name.
     */
    public function getName(): string
    {
        return __(Str::of(static::class)->classBasename()->headline()->value());
    }

    /**
     * Get the Vue component.
     */
    public function getComponent(): string
    {
        return $this->component;
    }

    /**
     * Get the Blade template.
     */
    public function getTemplate(): string
    {
        return $this->template;
    }

    /**
     * Set the async attribute.
     */
    public function async(bool $value = true): static
    {
        $this->async = $value;

        return $this;
    }

    /**
     * Determine if the widget is async.
     */
    public function isAsync(): bool
    {
        return $this->async;
    }

    /**
     * Get the data.
     */
    public function data(Request $request): array
    {
        return [];
    }

    /**
     * Set the data resolver.
     */
    public function with(array|Closure $data): static
    {
        $this->dataResolver = is_array($data) ? fn (): array => $data : $data;

        return $this;
    }

    /**
     * Resolve the data.
     */
    public function resolveData(Request $request): array
    {
        return array_merge(
            $this->data($request),
            is_null($this->dataResolver) ? [] : call_user_func_array($this->dataResolver, [$request])
        );
    }

    /**
     * Get the evaluated contents of the object.
     */
    public function render(): string
    {
        return View::make(
            $this->getTemplate(),
            App::call([$this, 'resolveData'])
        )->render();
    }

    /**
     * The routes that should be registered.
     */
    public function routes(Router $router): void
    {
        if ($this->async) {
            $router->get('/', WidgetController::class);
        }
    }

    /**
     * Get the instance as an array.
     */
    public function toArray(): array
    {
        return [
            'async' => $this->async,
            'component' => $this->getComponent(),
            'key' => $this->getKey(),
            'name' => $this->getName(),
            'template' => $this->async ? null : $this->render(),
            'url' => $this->async ? $this->getUri() : null,
        ];
    }
}
