<?php

namespace Cone\Root\Support;

use Cone\Root\Enums\AssetType;
use Illuminate\Support\Facades\App;

class Asset
{
    /**
     * The asset key.
     */
    protected string $key;

    /**
     * The asset type.
     */
    protected AssetType $type;

    /**
     * The asset path.
     */
    protected string $path;

    /**
     * The asset URL.
     */
    protected ?string $url = null;

    /**
     * Create a new asset instance.
     */
    public function __construct(string $key, AssetType $type, string $path, ?string $url = null)
    {
        $this->key = $key;
        $this->path = $path;
        $this->type = $type;
        $this->url = $url;
    }

    /**
     * Get the key.
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * Get the type.
     */
    public function getType(): AssetType
    {
        return $this->type;
    }

    /**
     * Get the path.
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Get the URL.
     */
    public function getUrl(): string
    {
        if (! is_null($this->url)) {
            return $this->url;
        }

        if ($this->getType() === AssetType::Icon) {
            return sprintf('#icon-%s', $this->getKey());
        }

        $path = $this->getPath();

        if (str_contains($path, App::resourcePath())) {
            return sprintf('%s/%s', basename(dirname($path)), basename($path));
        }

        return sprintf('vendor/%s/%s', $this->getKey(), basename($path));
    }
}
