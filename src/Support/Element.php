<?php

namespace Cone\Root\Support;

use Cone\Root\Traits\HasAttributes;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\View as ViewFactory;
use JsonSerializable;
use Stringable;

abstract class Element implements Arrayable, Htmlable, JsonSerializable, Stringable
{
    use HasAttributes;

    /**
     * The Blade template.
     */
    protected string $template;

    /**
     * Set the "id" HTML attribute.
     */
    public function id(string $value): static
    {
        return $this->setAttribute('id', strtolower($value));
    }

    /**
     * Render the element.
     */
    public function render(): View
    {
        return ViewFactory::make(
            $this->template,
            $this->toArray()
        );
    }

    /**
     * Convert the element to a JSON serializable format.
     */
    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }

    /**
     * Convert the element to an array.
     */
    public function toArray(): array
    {
        return [
            'attrs' => $this->newAttributeBag(),
        ];
    }

    /**
     * Convert the element to an HTML string.
     */
    public function toHtml(): string
    {
        return $this->render()->render();
    }

    /**
     * Convert the element to a string.
     */
    public function __toString(): string
    {
        return $this->toHtml();
    }
}
