<?php

namespace Cone\Root\Widgets;

use Cone\Root\Support\Element;
use Cone\Root\Traits\Authorizable;
use Cone\Root\Traits\Makeable;
use Illuminate\Support\Str;

abstract class Widget extends Element
{
    use Authorizable;
    use Makeable;

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
     * Convert the widget to an array.
     */
    public function toArray(): array
    {
        return [
            'key' => $this->getKey(),
            'name' => $this->getName(),
        ];
    }
}
