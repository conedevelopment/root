<?php

namespace Cone\Root\Support;

use Cone\Root\Interfaces\Registries\Item;
use Illuminate\Contracts\Support\Arrayable;

class Asset implements Arrayable, Item
{
    protected const SCRIPT = 'script';
    protected const STYLE = 'style';

    protected string $key;
    protected string $type;
    protected string $path;

    /**
     * Create a new asset instance.
     *
     * @return void
     */
    public function __construct(string $key, string $type, string $path)
    {
        //
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [];
    }
}
