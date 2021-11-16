<?php

namespace Cone\Root\Widgets;

use Cone\Root\Traits\ResolvesVisibility;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

abstract class Widget implements Arrayable, Renderable
{
    use ResolvesVisibility;

    /**
     * The Vue component.
     *
     * @var string
     */
    protected string $component = 'Widget';

    /**
     * Make a new extract instance.
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
     * Determine if the component should be async.
     *
     * @return bool
     */
    public function async(): bool
    {
        return false;
    }

    /**
     * Get the evaluated contents of the object.
     *
     * @return string
     */
    public function render(): string
    {
        return View::make('root::widget', App::call([$this, 'data']))->render();
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return array_merge(
            [
                'component' => $this->getComponent(),
                'key' => $this->getKey(),
                'name' => $this->getName(),
            ],
            $this->async() ? ['data' => App::call([$this, 'data'])] : ['template' => $this->render()]
        );
    }
}
