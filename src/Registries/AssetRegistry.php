<?php

namespace Cone\Root\Registries;

use Cone\Root\Interfaces\Registries\AssetRegistry as Contract;
use Cone\Root\Support\Asset;

class AssetRegistry extends Registry implements Contract
{
    /**
     * Register a new script.
     *
     * @param  string  $key
     * @param  string  $path
     * @return void
     */
    public function script(string $key, string $path): void
    {
        $asset = new Asset($key, Asset::SCRIPT, $path);

        $this->register($asset->getKey(), $asset);
    }

    /**
     * Register a new style.
     *
     * @param  string  $key
     * @param  string  $path
     * @return void
     */
    public function style(string $key, string $path): void
    {
        $asset = new Asset($key, Asset::STYLE, $path);

        $this->register($asset->getKey(), $asset);
    }

    /**
     * Get the registered scripts.
     *
     * @return array
     */
    public function scripts(): array
    {
        return array_filter($this->items, static function (Asset $asset): bool {
            return $asset->getType() === Asset::SCRIPT;
        });
    }

    /**
     * Get the registered styles.
     *
     * @return array
     */
    public function styles(): array
    {
        return array_filter($this->items, static function (Asset $asset): bool {
            return $asset->getType() === Asset::STYLE;
        });
    }
}
