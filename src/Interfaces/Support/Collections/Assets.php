<?php

namespace Cone\Root\Interfaces\Support\Collections;

interface Assets
{
    /**
     * Register a new script.
     *
     * @param  string  $key
     * @param  string  $path
     * @param  string|null  $url
     * @return void
     */
    public function script(string $key, string $path, ?string $url = null): void;

    /**
     * Register a new style.
     *
     * @param  string  $key
     * @param  string  $path
     * @param  string|null  $url
     * @return void
     */
    public function style(string $key, string $path, ?string $url = null): void;

    /**
     * Register a new icon.
     *
     * @param  string  $key
     * @param  string  $path
     * @param  string|null  $url
     * @return void
     */
    public function icon(string $key, string $path, ?string $url = null): void;

        /**
     * Get the registered scripts.
     *
     * @return \Cone\Root\Support\Collections\Assets
     */
    public function scripts(): static;

    /**
     * Get the registered styles.
     *
     * @return \Cone\Root\Support\Collections\Assets
     */
    public function styles(): static;

    /**
     * Get the registered icons.
     *
     * @return \Cone\Root\Support\Collections\Assets
     */
    public function icons(): static;
}
