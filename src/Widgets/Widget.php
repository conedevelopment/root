<?php

namespace Cone\Root\Widgets;

use Cone\Root\Traits\Authorizable;
use Cone\Root\Traits\HasAttributes;
use Cone\Root\Traits\Makeable;
use Cone\Root\Traits\RegistersRoutes;
use Cone\Root\Traits\ResolvesVisibility;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\View as ViewFactory;
use Illuminate\Support\Str;

abstract class Widget implements Arrayable, Responsable
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
     * Render the widget.
     */
    public function render(): View
    {
        return ViewFactory::make($this->template);
    }

    /**
     * Get the view data.
     */
    public function data(Request $request): array
    {
        return array_merge($this->toArray(), [
            'isTurbo' => $request->hasHeader('Turbo-Frame'),
        ]);
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

    /**
     * Convert the widget to an HTTP Response.
     */
    public function toResponse($request): Response
    {
        return new Response(
            $this->render()->with($this->data($request))->render()
        );
    }
}
