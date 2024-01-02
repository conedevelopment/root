<?php

namespace Cone\Root\Widgets;

use Cone\Root\Http\Controllers\WidgetController;
use Cone\Root\Http\Middleware\Authorize;
use Cone\Root\Traits\Authorizable;
use Cone\Root\Traits\HasAttributes;
use Cone\Root\Traits\Makeable;
use Cone\Root\Traits\RegistersRoutes;
use Cone\Root\Traits\ResolvesVisibility;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Str;

abstract class Widget implements Arrayable
{
    use Authorizable;
    use HasAttributes;
    use Makeable;
    use RegistersRoutes;
    use ResolvesVisibility;

    /**
     * The Blade template.
     */
    protected string $template;

    /**
     * Indicates whether the widget is async loaded.
     */
    protected bool $async = false;

    /**
     * Create a new widget instance.
     */
    public function __construct()
    {
        $this->class('app-widget');
    }

    /**
     * Get the key.
     */
    public function getKey(): string
    {
        return Str::of(static::class)->classBasename()->kebab()->value();
    }

    /**
     * Get the name.
     */
    public function getName(): string
    {
        return __(Str::of(static::class)->classBasename()->headline()->value());
    }

    /**
     * Get the URI key.
     */
    public function getUriKey(): string
    {
        return $this->getKey();
    }

    /**
     * Get the view data.
     */
    public function data(Request $request): array
    {
        return array_merge($this->toArray(), [
            'isTurbo' => $request->isTurboFrameRequest(),
        ]);
    }

    /**
     * Get the route middleware for the regsitered routes.
     */
    public function getRouteMiddleware(): array
    {
        return [
            Authorize::class.':widget',
        ];
    }

    /**
     * The routes should be registered.
     */
    public function routes(Router $router): void
    {
        if ($this->async) {
            $router->get('/', WidgetController::class);
        }
    }

    /**
     * Convert the widget to an array.
     */
    public function toArray(): array
    {
        return [
            'attrs' => $this->newAttributeBag(),
            'key' => $this->getKey(),
            'name' => $this->getName(),
            'template' => $this->template,
            'url' => $this->getUri(),
        ];
    }
}
