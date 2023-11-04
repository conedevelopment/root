<?php

namespace Cone\Root\Widgets;

use Cone\Root\Traits\Authorizable;
use Cone\Root\Traits\HasAttributes;
use Cone\Root\Traits\Makeable;
use Cone\Root\Traits\ResolvesVisibility;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Str;
use JsonSerializable;

abstract class Widget implements Arrayable, JsonSerializable
{
    use Authorizable;
    use HasAttributes;
    use Makeable;
    use ResolvesVisibility;

    /**
     * The Blade template.
     */
    protected string $template = 'root::widgets.widget';

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
     * Convert the element to a JSON serializable format.
     */
    public function jsonSerialize(): mixed
    {
        return $this->toArray();
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
        ];
    }
}
