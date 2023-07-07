<?php

namespace Cone\Root\Widgets;

use Closure;
use Cone\Root\Http\Controllers\WidgetController;
use Cone\Root\Interfaces\Routable;
use Cone\Root\Traits\Authorizable;
use Cone\Root\Traits\Makeable;
use Cone\Root\Traits\RegistersRoutes;
use Cone\Root\Traits\ResolvesVisibility;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Str;

abstract class Widget implements Routable
{
    use Authorizable;
    use Makeable;
    use RegistersRoutes;
    use ResolvesVisibility;

    /**
     * Indicates if the component is async.
     */
    protected bool $async = false;

    /**
     * The blade component.
     */
    protected string $component = 'root::widgets.widget';

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
     * Get the blade component.
     */
    public function getComponent(): string
    {
        return $this->component;
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
        if (is_array($data)) {
            $data = static function () use ($data): array {
                return $data;
            };
        }

        $this->dataResolver = $data;

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
     * The routes that should be registered.
     */
    public function routes(Router $router): void
    {
        if ($this->async) {
            $router->get('/', WidgetController::class);
        }
    }
}
