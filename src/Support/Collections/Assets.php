<?php

namespace Cone\Root\Support\Collections;

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
        $asset = new Asset($key, Asset::SCRIPT, $path, $url);

        $this->put($asset->getKey(), $asset);
    }

    /**
     * Register a new style.
     */
    public function style(string $key, string $path, ?string $url = null): void
    {
        $asset = new Asset($key, Asset::STYLE, $path, $url);

        $this->put($asset->getKey(), $asset);
    }

    /**
     * Register a new icon.
     */
    public function icon(string $key, string $path, ?string $url = null): void
    {
        $asset = new Asset($key, Asset::ICON, $path, $url);

        $this->put($asset->getKey(), $asset);
    }

    /**
     * Get the registered scripts.
     *
     * @return \Cone\Root\Support\Collections\Assets
     */
    public function scripts(): static
    {
        return $this->filter(static function (Asset $asset): bool {
            return $asset->getType() === Asset::SCRIPT;
        });
    }

    /**
     * Get the registered styles.
     *
     * @return \Cone\Root\Support\Collections\Assets
     */
    public function styles(): static
    {
        return $this->filter(static function (Asset $asset): bool {
            return $asset->getType() === Asset::STYLE;
        });
    }

    /**
     * Get the registered icons.
     *
     * @return \Cone\Root\Support\Collections\Assets
     */
    public function icons(): static
    {
        return $this->filter(static function (Asset $asset): bool {
            return $asset->getType() === Asset::ICON;
        });
    }
}
