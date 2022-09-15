<?php

declare(strict_types = 1);

namespace Cone\Root\Interfaces\Models;

interface Notification
{
    /**
     * Get the formatted type attribute.
     *
     * @return string|null
     */
    public function getFormattedTypeAttribute(): ?string;

    /**
     * Get the content attribute.
     *
     * @return string|null
     */
    public function getContentAttribute(): ?string;

    /**
     * Get the formatted created at attribute.
     *
     * @return string|null
     */
    public function getFormattedCreatedAtAttribute(): ?string;
}
