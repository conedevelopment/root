<?php

namespace Cone\Root\Interfaces\Models;

interface Notification
{
    /**
     * Get the formatted type attribute.
     */
    public function getFormattedTypeAttribute(): ?string;

    /**
     * Get the content attribute.
     */
    public function getContentAttribute(): ?string;

    /**
     * Get the formatted created at attribute.
     */
    public function getFormattedCreatedAtAttribute(): ?string;
}
