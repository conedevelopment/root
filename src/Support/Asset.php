<?php

namespace Cone\Root\Support;

use Illuminate\Support\Facades\App;

class Asset
{
    public const SCRIPT = 'script';
    public const STYLE = 'style';
    public const ICON = 'icon';

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
     * The asset URL.
     *
     * @var string|null
     */
    protected ?string $url = null;

    /**
     * Create a new asset instance.
     *
     * @param  string  $key
     * @param  string  $type
     * @param  string  $path
     * @param  string|null  $url
     * @return void
     */
    public function __construct(string $key, string $type, string $path, ?string $url = null)
    {
        $this->key = $key;
        $this->path = $path;
        $this->type = $type;
        $this->url = $url;
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

    /**
     * Get the URL.
     *
     * @return string
     */
    public function getUrl(): string
    {
        if (! is_null($this->url)) {
            return $this->url;
        }

        if ($this->getType() === static::ICON) {
            return sprintf('#icon-%s', $this->getKey());
        }

        $path = $this->getPath();

        if (str_contains($path, App::resourcePath())) {
            return sprintf('%s/%s', basename(dirname($path)), basename($path));
        }

        return sprintf('vendor/%s/%s', $this->getKey(), basename($path));
    }
}
