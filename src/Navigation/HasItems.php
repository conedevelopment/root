<?php

namespace Cone\Root\Navigation;

use Closure;
use Illuminate\Support\Arr;

trait HasItems
{
    /**
     * The items.
     */
    protected array $items = [];

    /**
     * Make a new item.
     */
    public function new(string $url, string $label, array $attributes = [], ?Closure $callback = null): static
    {
        $item = new Item($url, $label, $attributes);

        if (! is_null($callback)) {
            call_user_func_array($callback, [$item]);
        }

        return $this->add($item);
    }

    /**
     * Add new items.
     */
    public function add(array|Item $items): static
    {
        foreach (Arr::wrap($items) as $item) {
            $this->items[$item->url] = $item;
        }

        return $this;
    }

    /**
     * Get the item.
     */
    public function get(string $url): ?Item
    {
        return $this->items[$url] ?? null;
    }

    /**
     * Remove the item.
     */
    public function remove(string $key): static
    {
        unset($this->items[$key]);

        return $this;
    }

    /**
     * Get the items.
     */
    public function all(): array
    {
        return $this->items;
    }
}
