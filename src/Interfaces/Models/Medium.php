<?php

declare(strict_types=1);

namespace Cone\Root\Interfaces\Models;

use Symfony\Component\HttpFoundation\BinaryFileResponse;

interface Medium
{
    /**
     * Perform the conversions on the medium.
     */
    public function convert(): static;

    /**
     * Get the path to the conversion.
     */
    public function getPath(?string $conversion = null, bool $absolute = false): string;

    /**
     * Get the full path to the conversion.
     */
    public function getAbsolutePath(?string $conversion = null): string;

    /**
     * Get the url to the conversion.
     */
    public function getUrl(?string $conversion = null): string;

    /**
     * Download the medium.
     */
    public function download(?string $conversion = null): BinaryFileResponse;
}
