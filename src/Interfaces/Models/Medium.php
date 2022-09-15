<?php

declare(strict_types = 1);

namespace Cone\Root\Interfaces\Models;

interface Medium
{
    /**
     * Perform the conversions on the medium.
     *
     * @return $this
     */
    public function convert(): static;

    /**
     * Get the path to the conversion.
     *
     * @param  string|null  $conversion
     * @param  bool  $absolute
     * @return string
     */
    public function getPath(?string $conversion = null, bool $absolute = false): string;

    /**
     * Get the full path to the conversion.
     *
     * @param  string|null  $conversion
     * @return string
     */
    public function getAbsolutePath(?string $conversion = null): string;

    /**
     * Get the url to the conversion.
     *
     * @param  string|null  $conversion
     * @return string
     */
    public function getUrl(?string $conversion = null): string;
}
