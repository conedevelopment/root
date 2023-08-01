<?php

namespace Cone\Root\Interfaces\Models;

interface Medium
{
    /**
     * Perform the conversions on the medium.
     */
    public function convert(): static;

    /**
     * Get the path to the conversion.
     */
    public function getPath(string $conversion = null, bool $absolute = false): string;

    /**
     * Get the full path to the conversion.
     */
    public function getAbsolutePath(string $conversion = null): string;

    /**
     * Get the url to the conversion.
     */
    public function getUrl(string $conversion = null): string;
}
