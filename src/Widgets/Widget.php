<?php

namespace Cone\Root\Widgets;

use Closure;
use Cone\Root\Traits\Authorizable;
use Cone\Root\Traits\Makeable;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use Stringable;

abstract class Widget implements Htmlable, Stringable
{
    use Authorizable;
    use Makeable;

    /**
     * Indicates if the component is async.
     */
    protected bool $async = false;

    /**
     * The blade component.
     */
    protected string $template = 'root::widgets.widget';

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
        $this->dataResolver = is_array($data)
            ? fn (): array => $data
            : $data;

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
     * Render the widget.
     */
    public function render(): View
    {
        return App::make('view')->make(
            $this->template,
            App::call([$this, 'data'])
        );
    }

    /**
     * Render the HTML string.
     */
    public function toHtml(): string
    {
        return $this->render()->render();
    }

    /**
     * Convert the field to a string.
     */
    public function __toString(): string
    {
        return $this->toHtml();
    }
}
