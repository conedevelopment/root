<?php

namespace Cone\Root\Support;

use Cone\Root\Interfaces\Registries\Item;
use Illuminate\Contracts\Support\Arrayable;

class Asset implements Arrayable, Item
{
    protected const SCRIPT = 'script';
    protected const STYLE = 'style';

    /**
     * The asset key.
     *
     * @var string
     */
    protected string $key;

    /**
     * The asset type.
     *
     * @var string
     */
    protected string $type;

    /**
     * The asset path.
     *
     * @var string
     */
    protected string $path;

    /**
     * Create a new asset instance.
     *
     * @param  string  $key
     * @param  string  $type
     * @param  string  $path
     * @return void
     */
    public function __construct(string $key, string $type, string $path)
    {
        $this->key;
        $this->type;
        $this->path;
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
