<?php

namespace Cone\Root\Contracts\Models;

interface Medium
{
    /**
     * Perform the conversions on the medium.
     *
     * @return $this
     */
    public function convert(): self;

    /**
     * Get the path to the conversion.
     *
     * @param  string|null  $conversion
     * @param  bool  $absolute
     * @return string
     */
    public function path(?string $conversion = null, bool $absolute = false): string;

    /**
     * Get the full path to the conversion.
     *
     * @param  string|null  $conversion
     * @return string
     */
    public function absolutePath(?string $conversion = null): string;

    /**
     * Get the url to the conversion.
     *
     * @param  string|null  $conversion
     * @return string
     */
    public function url(?string $conversion = null): string;
}
