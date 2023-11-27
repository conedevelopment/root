<?php

namespace Cone\Root\Navigation;

use Cone\Root\Traits\HasAttributes;
use Cone\Root\Traits\Makeable;
use Illuminate\Support\Facades\URL;

class Item
{
    use HasAttributes;
    use HasItems;
    use Makeable;

    /**
     * Create a new item instance.
     */
    public function __construct(string $url, string $label, array $attributes = [])
    {
        $this->url($url);
        $this->label($label);
        $this->icon('archive');
        $this->setAttributes($attributes);
    }

    /**
     * Set the item URL.
     */
    public function url(string $url): static
    {
        return $this->setAttribute('url', URL::to($url));
    }

    /**
     * Set the item label.
     */
    public function label(string $label): static
    {
        return $this->setAttribute('label', $label);
    }

    /**
     * Set the item group.
     */
    public function group(string $group): static
    {
        return $this->setAttribute('group', $group);
    }

    /**
     * Set the item icon.
     */
    public function icon(string $icon): static
    {
        return $this->setAttribute('icon', $icon);
    }

    /**
     * Determine if the item URL matches the current URL.
     */
    public function matched(): bool
    {
        return trim(URL::current(), '/') === trim($this->url, '/');
    }

    /**
     * Determine if the item URL matches the current URL.
     */
    public function partiallyMatched(): bool
    {
        return str_starts_with(URL::current(), $this->url);
    }

    /**
     * Get the given attribute.
     */
    public function __get(string $key): mixed
    {
        return $this->getAttribute($key);
    }
}
