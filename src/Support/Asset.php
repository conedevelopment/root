<?php

namespace Cone\Root\Support;

use Cone\Root\Interfaces\Registries\Item;

class Asset implements Item
{
    public const SCRIPT = 'script';
    public const STYLE = 'style';

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
        $this->key = $key;
        $this->type = $type;
        $this->path = $path;
    }

    /**
     * Get the key.
     *
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * Get the type.
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Get the path.
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }
}
