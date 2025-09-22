<?php

declare(strict_types=1);

namespace Cone\Root\Support;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Facades\Blade;
use Stringable;

class Copyable implements Htmlable, Renderable, Stringable
{
    /**
     * Create a new copyable instance.
     */
    public function __construct(protected string $text, protected string $value)
    {
        //
    }

    /**
     * Make a new copyable instance.
     */
    public static function make(string $text, ?string $value = null): static
    {
        return new static($text, $value ?: $text);
    }

    /**
     * Get the evaluated contents of the object.
     */
    public function render(): string
    {
        return Blade::render(sprintf('<x-root::copyable text="%s" value="%s" />', $this->text, $this->value));
    }

    /**
     * Get content as a string of HTML.
     */
    public function toHtml(): string
    {
        return $this->render();
    }

    /**
     * Get the string representation of the object.
     */
    public function __toString(): string
    {
        return $this->toHtml();
    }
}
