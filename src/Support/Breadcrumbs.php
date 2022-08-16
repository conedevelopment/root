<?php

namespace Cone\Root\Support;

use Illuminate\Contracts\Support\Arrayable;

class Breadcrumbs implements Arrayable
{
    /**
     * The items.
     *
     * @var array
     */
    protected array $items = [];

    /**
     * Create a new breadcrumbs instance.
     *
     * @param  array  $items
     * @return void
     */
    public function __construct(array $items = [])
    {
        $this->items = $items;
    }

    /**
     * Create a new breadcrumb instance with the new items.
     *
     * @param  array  $items
     * @return static
     */
    public function merge(array $items): static
    {
        return new static(array_merge($this->items, $items));
    }

    /**
     * Get the breadcrumb items.
     *
     * @return array
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * Get the instance as an array.
     *
     * @return array<TKey, TValue>
     */
    public function toArray(): array
    {
        return array_values(array_map(function (string $label, string $path): array {
            return [
                'url' => $path,
                'label' => $label,
                'active' => array_key_last($this->items) === $path,
            ];
        }, $this->items, array_keys($this->items)));
    }
}
