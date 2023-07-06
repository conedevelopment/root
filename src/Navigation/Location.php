<?php

namespace Cone\Root\Navigation;

class Location
{
    use HasItems;

    /**
     * The locaiton name.
     */
    protected string $name;

    /**
     * Create a new location instance.
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * Get the items by their groups.
     */
    public function groups(): array
    {
        return array_reduce($this->items, static function (array $groups, Item $item): array {
            $groups[$item->group][] = $item;

            return $groups;
        }, []);
    }
}
