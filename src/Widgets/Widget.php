<?php

namespace Cone\Root\Widgets;

use Cone\Root\Http\Controllers\WidgetController;
use Cone\Root\Resources\Resource;
use Cone\Root\Traits\Authorizable;
use Cone\Root\Traits\ResolvesVisibility;
use Cone\Root\Traits\ResourceRoutable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

abstract class Widget implements Arrayable, Renderable
{
    use Authorizable;
    use ResolvesVisibility;
    use ResourceRoutable;

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
    protected string $template = 'root::widget';

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
     * Regsiter the routes for the widget.
     *
     * @param  \Cone\Root\Resources\Resource  $resource
     * @param  string  $uri
     * @return void
     */
    protected function routes(Resource $resource, string $uri): void
    {
        if ($this->async) {
            $resource->routes(function () use ($uri): void {
                Route::get($uri, WidgetController::class);
            });
        }
    }

    /**
     * Get the key.
     *
     * @return string
     */
    public function getKey(): string
    {
        return Str::of(static::class)->classBasename()->kebab();
    }

    /**
     * Get the name.
     *
     * @return string
     */
    public function getName(): string
    {
        return Str::of(static::class)->classBasename()->headline();
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
     * Get the data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function data(Request $request): array
    {
        return [];
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
     * Get the evaluated contents of the object.
     *
     * @return string
     */
    public function render(): string
    {
        return View::make($this->getTemplate(), App::call([$this, 'data']))->render();
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
            'url' => $this->async ? call_user_func($this->urlResolver) : null,
        ];
    }
}
