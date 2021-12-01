<?php

namespace Cone\Root\Widgets;

use Cone\Root\Interfaces\Routable;
use Cone\Root\Traits\ResolvesVisibility;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

abstract class Widget implements Arrayable, Renderable, Routable
{
    use ResolvesVisibility;

    /**
     * The cache store.
     *
     * @var array
     */
    protected array $cache = [];

    /**
     * Indicates if the options should be lazily populated.
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
     * The Blade templte.
     *
     * @var string
     */
    protected string $template = 'root::widget';

    /**
     * The URI for the field.
     *
     * @var string|null
     */
    protected ?string $uri = null;

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
     * Set the URI attribute.
     *
     * @param  string  $uri
     * @return void
     */
    public function setUri(?string $uri = null): void
    {
        $this->uri = $uri;
    }

    /**
     * Get the URI attribute.
     *
     * @return string|null
     */
    public function getUri(): ?string
    {
        return $this->uri;
    }

    /**
     * Register the routes.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function routes(Request $request): void
    {
        if ($this->async) {
            Route::get($this->getKey(), function (): string {
                return $this->render();
            });
        }
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
            'component' => $this->getComponent(),
            'key' => $this->getKey(),
            'name' => $this->getName(),
            'template' => $this->async ? null : $this->render(),
        ];
    }
}
