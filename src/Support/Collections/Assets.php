<?php

namespace Cone\Root\Support\Collections;

use Cone\Root\Enums\AssetType;
use Cone\Root\Interfaces\Support\Collections\Assets as Contract;
use Cone\Root\Support\Asset;
use Illuminate\Support\Collection;

class Assets extends Collection implements Contract
{
    /**
     * Register a new script.
     */
    public function script(string $key, string $path, ?string $url = null): void
    {
        $asset = new Asset($key, AssetType::Script, $path, $url);

        $this->put($asset->getKey(), $asset);
    }

    /**
     * Register a new style.
     */
    public function style(string $key, string $path, ?string $url = null): void
    {
        $asset = new Asset($key, AssetType::Style, $path, $url);

        $this->put($asset->getKey(), $asset);
    }

    /**
     * Register a new icon.
     */
    public function icon(string $key, string $path, ?string $url = null): void
    {
        $asset = new Asset($key, AssetType::Icon, $path, $url);

        $this->put($asset->getKey(), $asset);
    }

    /**
     * Get the registered scripts.
     */
    public function scripts(): static
    {
        return $this->filter(static function (Asset $asset): bool {
            return $asset->getType() === AssetType::Script;
        });
    }

    /**
     * Get the registered styles.
     */
    public function styles(): static
    {
        return $this->filter(static function (Asset $asset): bool {
            return $asset->getType() === AssetType::Style;
        });
    }

    /**
     * Get the registered icons.
     */
    public function icons(): static
    {
        return $this->filter(static function (Asset $asset): bool {
            return $asset->getType() === AssetType::Icon;
        });
    }
}
